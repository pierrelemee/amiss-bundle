<?php

namespace Amiss\Bundle\AmissBundle\Command;

use Amiss\Sql\Manager;
use Amiss\Sql\TableBuilder;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use PDOException;
use Amiss\Exception;
use Symfony\Component\Finder\Finder;

/**
 * Class AmissSchemaCreateCommand
 * @package Mots\Bundle\MainBundle\Command
 *
 * TODO: move this command to a dedicated bundle
 */
class AmissSchemaCreateCommand extends ContainerAwareCommand
{
    protected $alias;
    /**
     * @var $manager Manager
     */
    protected $manager;

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('amiss:schema:create')
            ->setDescription("Create schema for entities mapped in classes under associated directory")
            ->addOption('manager', 'm', InputOption::VALUE_REQUIRED, "Name of the manager to use", 'main')
            ->addOption('recursive', 'r', InputOption::VALUE_NONE, "Recursive")
            ->addArgument('directory', InputArgument::OPTIONAL, "Directory containing all model classes");
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->manager = $this->getContainer()->get(sprintf('amiss.manager.%s', $input->getOption('manager')));
        $directory = $input->hasArgument('directory') ? $input->getArgument('directory') : $this->getContainer()->get('kernel')->getRootDir() . "/../src";
        $classes = $this->getClassesInDir($directory);
        $count = 0;
        $output->writeln("Creating table(s) for:");
        foreach ($classes as $class) {
            try {
                $tableBuilder = new TableBuilder($this->manager, $class);
                $tableBuilder->createTable();
                $output->writeln(sprintf("  - <comment>%s</comment> <info>OK</info>", $class));
                $count++;
            } catch (PDOException $e) {
                $output->writeln(sprintf("  - <comment>%s</comment> <fg=red>KO</> (%s)", $class, $e->getMessage()));
            } catch (Exception $e) {
                $output->writeln(sprintf("  - <comment>%s</comment> N/A (unmapped class)", $class));

                // No way to know if it's a database error or just an unmapped class
            }
        }
        $output->writeln(sprintf("Created <info>%d</info> table%s successfully", $count, $count > 1 ? "s" : ""));
    }

    protected function getClassesInDir($directory)
    {
        $finder = new Finder();
        $classes = get_declared_classes();
        foreach ($finder->files("*.php")->in($directory) as $file) {
            require_once $file->getRealPath();

        }
        return array_diff(get_declared_classes(), $classes);
    }
}