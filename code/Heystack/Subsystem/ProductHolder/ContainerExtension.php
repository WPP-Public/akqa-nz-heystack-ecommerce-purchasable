<?php

namespace Heystack\Subsystem\ProductHolder;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class ContainerExtension implements ExtensionInterface
{

    public function load(array $config, ContainerBuilder $container)
    {

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../../../../config/')
        );

        $loader->load('services.yml');

    }

    public function getNamespace()
    {
        return 'productholder';
    }

    public function getXsdValidationBasePath()
    {
        return false;
    }

    public function getAlias()
    {
        return 'productholder';
    }
    
}
