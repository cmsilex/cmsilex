<?php

namespace CMSilex;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cmsilex');

        $rootNode
            ->children()
                ->arrayNode('db')
                    ->children()
                        ->scalarNode('user')
                        ->end()
                        ->scalarNode('password')
                        ->end()
                        ->scalarNode('dbname')
                        ->end()
                        ->scalarNode('host')
                        ->end()
                        ->scalarNode('port')
                        ->end()
                        ->scalarNode('driver')
                        ->end()
                        ->scalarNode('path')
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('debug')
                    ->defaultFalse()
                ->end()
                ->scalarNode('theme')
                ->end()
                ->booleanNode('register')
                    ->defaultFalse()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}