<?php


namespace Fabricio872\RegisterCommand\DependencyInjection;


use App\Entity\User;
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
                ->scalarNode("user_class")->defaultValue(User::class)->info('Entity for your user')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
