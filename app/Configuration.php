<?php

namespace CMSilex;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('database');

        $rootNode
            ->children()
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