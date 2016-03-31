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
                        ->scalarNode('path')
                            ->defaultValue('../db.sqlite')
                        ->end()
                    ->end()
                ->end()
                ->scalarNode('pages_dir')
                    ->defaultValue('../pages/')
                ->end()
                ->booleanNode('debug')
                    ->defaultFalse()
                ->end()
                ->scalarNode('username')
                    ->isRequired()
                ->end()
                ->scalarNode('password')
                    ->isRequired()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}