<?php
/**
 * This file is part of the Ecommerce-Purchasable package
 *
 * @package Ecommerce-Purchasable
 */

/**
 * DependencyInjection namespace
 */
namespace Heystack\Subsystem\Purchasable\DependencyInjection;

use Heystack\Subsystem\Purchasable\Config\ContainerConfig;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Container extension for Heystack.
 *
 * If Heystack's services are loaded as an extension (this happens when there is
 * a primary services.yml file in mysite/config) then this is the container
 * extension that loads heystack's services.yml
 *
 * @copyright  Heyday
 * @author     Cam Spiers <cameron@heyday.co.nz>
 * @author     Glenn Bautista <glenn@heyday.co.nz>
 * @package    Ecommerce-Purchasable
 *
 */
class ContainerExtension extends Extension
{

    /**
     * Loads a services.yml file into a fresh container, ready to me merged
     * back into the main container
     *
     * @param  array $configs
     * @param  ContainerBuilder $container
     * @return null
     */
    public function load(array $configs, ContainerBuilder $container)
    {

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(ECOMMERCE_PURCHASABLE_BASE_PATH . '/config')
        );

        $loader->load('services.yml');

        $config = (new Processor())->processConfiguration(
            new ContainerConfig(),
            $configs
        );

        if (isset($config['purchasable_class'])) {
            $container->setParameter('purchasable.class', $config['purchasable_class']);
        }

        if (isset($config['yml_purchasable']) && $container->hasDefinition('purchasable_schema')) {

            $definition = $container->getDefinition('purchasable_schema');

            $definition->replaceArgument(0, $config['yml_purchasable']);

        }

        if (isset($config['yml_purchasableholder']) && $container->hasDefinition('purchasable_holder_schema')) {

            $definition = $container->getDefinition('purchasable_holder_schema');

            $definition->replaceArgument(0, $config['yml_purchasableholder']);

        }

        if (isset($config['yml_transaction_purchasableholder'])
            && $container->hasDefinition('transaction_purchasable_holder_schema')
        ) {

            $definition = $container->getDefinition('transaction_purchasable_holder_schema');

            $definition->replaceArgument(0, $config['yml_transaction_purchasableholder']);

        }

    }

    /**
     * Returns the namespace of the container extension
     * @return type
     */
    public function getNamespace()
    {
        return 'purchasable';
    }

    /**
     * Returns Xsd Validation Base Path, which is not used, so false
     * @return boolean
     */
    public function getXsdValidationBasePath()
    {
        return false;
    }

    /**
     * Returns the container extensions alias
     * @return type
     */
    public function getAlias()
    {
        return 'purchasable';
    }
}
