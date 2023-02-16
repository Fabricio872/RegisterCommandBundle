<?php

declare(strict_types=1);

namespace Fabricio872\RegisterCommand\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('RegisterBundle');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
            ->scalarNode("user_class")->defaultValue('App\Entity\User')->info('Entity for your user')->end()
            ->scalarNode("table_limit")->defaultValue(10)->info('Sets default value for maximum rows on single page of list table')->end()
            ->scalarNode("max_col_width")->defaultValue(64)->info('Sets maximum width for single column in characters')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
