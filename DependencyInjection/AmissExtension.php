<?php

namespace Amiss\Bundle\AmissBundle\DependencyInjection;

use Amiss;
use Amiss\Exception;
use Amiss\Sql\Connector;
use Amiss\Sql\Manager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

class AmissExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new AmissConfiguration(), $configs);

        foreach ($config['connections'] as $name => $c) {
            $definition = new Definition();
            $definition->setClass('Amiss\Sql\Connector');
            $definition->setFactory(array(__CLASS__, 'createConnection'));
            $definition->setArguments([$c]);
            $container->setDefinition(sprintf('amiss.connection.%s', $name), $definition);

            $definition = new Definition();
            $definition->setClass('Amiss\Sql\Manager');
            $definition->setFactory(array(__CLASS__, 'createManager'));
            $definition->setArguments([$c]);
            $container->setDefinition(sprintf('amiss.manager.%s', $name), $definition);
        }

    }

    /**
     * @param array $options
     * @return Manager
     * @throws Exception
     */
    public static function createConnection(array $options)
    {
        switch ($options['scheme']) {
            case "mysql":
                return new Connector(sprintf("mysql:dbname=%s;host=%s;charset=utf8", $options['database'], $options['host']), $options['username'], $options['password'], $options['extra']);
                break;
            default:
                throw new Exception(sprintf("Unknown connection scheme '%s'", $options['scheme']));
        }
    }

    /**
     * @param array $options
     * @return Manager
     */
    public static function createManager(array $options)
    {
        return Amiss::createSqlManager(self::createConnection($options), $options['extra']);
    }
}