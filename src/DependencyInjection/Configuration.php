<?php

namespace Fabricio872\RegisterCommand\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('RegisterBundle');
        $rootNode = $treeBuilder->getRootNode();

        $rootNode
            ->children()
                ->scalarNode("user_class")->defaultValue('App\Entity\User')->info('Entity for your user')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
