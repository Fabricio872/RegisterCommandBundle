<?php

namespace Fabricio872\RegisterCommand\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class RegisterCommandExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        $registerDefinition = $container->getDefinition('fabricio872.register_command.command.user_register_command');
        $registerDefinition->setArgument(0, $config['user_class']);

        $listDefinition = $container->getDefinition('fabricio872.register_command.command.user_list_command');
        $listDefinition->setArgument(0, $config['user_class']);
    }
}