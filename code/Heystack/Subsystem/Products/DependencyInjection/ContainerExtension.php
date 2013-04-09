<?php
/**
 * This file is part of the Ecommerce-Products package
 *
 * @package Ecommerce-Products
 */

/**
 * Products namespace
 */
namespace Heystack\Subsystem\Products\DependencyInjection;

use Heystack\Subsystem\Products\Config\ContainerConfig;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

/**
 * Container extension for Heystack.
 *
 * If Heystacks services are loaded as an extension (this happens when there is
 * a primary services.yml file in mysite/config) then this is the container
 * extension that loads heystacks services.yml
 *
 * @copyright  Heyday
 * @author     Cam Spiers <cameron@heyday.co.nz>
 * @author     Glenn Bautista <glenn@heyday.co.nz>
 * @package    Ecommerce-Products
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
            new FileLocator(ECOMMERCE_PRODUCTS_BASE_PATH . '/config')
        );

        $loader->load('services.yml');

        $config = (new Processor())->processConfiguration(
            new ContainerConfig(),
            $configs
        );

        if (isset($config['product_class'])) {
            $container->setParameter('product.class', $config['product_class']);
        }

        if (isset($config['yml_product']) && $container->hasDefinition('product_schema')) {

            $definition = $container->getDefinition('product_schema');

            $definition->replaceArgument(0, $config['yml_product']);

        }

        if (isset($config['yml_productholder']) && $container->hasDefinition('product_holder_schema')) {

            $definition = $container->getDefinition('product_holder_schema');

            $definition->replaceArgument(0, $config['yml_productholder']);

        }

        if (isset($config['yml_transaction_productholder']) && $container->hasDefinition('transaction_product_holder_schema')) {

            $definition = $container->getDefinition('transaction_product_holder_schema');

            $definition->replaceArgument(0, $config['yml_transaction_productholder']);

        }

    }

    /**
     * Returns the namespace of the container extension
     * @return type
     */
    public function getNamespace()
    {
        return 'products';
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
        return 'products';
    }

}
