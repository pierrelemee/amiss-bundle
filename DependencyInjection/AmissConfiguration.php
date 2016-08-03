<?php

namespace Amiss\Bundle\AmissBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class AmissConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        // TODO adapt required parameters to each scheme needs
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('amiss');
        $root
            ->children()
            ->arrayNode('connections')
                ->prototype('array')
                    ->children()
                    ->scalarNode('scheme')->isRequired()->end()
                    ->scalarNode('host')->isRequired()->end()
                    ->scalarNode('username')->end()
                    ->scalarNode('password')->end()
                    ->scalarNode('database')->end()
                    ->integerNode('port')->end()
                    ->arrayNode('extra')
                        ->isRequired()
                        ->requiresAtLeastOneElement()
                        ->useAttributeAsKey('whatever')
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}