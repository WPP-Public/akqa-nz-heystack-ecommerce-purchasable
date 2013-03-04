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

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use Heystack\Subsystem\Core\DependencyInjection\ContainerExtensionConfigProcessor;

/**
 * Container extension for Heystack.
 *
 * If Heystacks services are loaded as an extension (this happens when there is
 * a primary services.yml file in mysite/config) then this is the container
 * extension that loads heystacks services.yml
 *
 * @copyright  Heyday
 * @author Cam Spiers <cameron@heyday.co.nz>
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Products
 *
 */
class ContainerExtension extends ContainerExtensionConfigProcessor implements ExtensionInterface
{

    /**
     * Loads a services.yml file into a fresh container, ready to me merged
     * back into the main container
     *
     * @param  array            $config
     * @param  ContainerBuilder $container
     * @return null
     */
    public function load(array $config, ContainerBuilder $container)
    {

        $loader = new YamlFileLoader(
            $container,
            new FileLocator(ECOMMERCE_PRODUCTS_BASE_PATH . '/config')
        );

        $loader->load('services.yml');

        $this->processConfig($config, $container);
        
        $config = array_pop($config);
        
        if(isset($config['yml.product']) && $container->hasDefinition('product_schema')){
            
            $definition = $container->getDefinition('product_schema');
            
            $definition->replaceArgument(0, $config['yml.product']);
            
        }
        
        if(isset($config['yml.productholder']) && $container->hasDefinition('product_holder_schema')){
            
            $definition = $container->getDefinition('product_holder_schema');
            
            $definition->replaceArgument(0, $config['yml.productholder']);
            
        }
        
        if(isset($config['yml.transaction_productholder']) && $container->hasDefinition('transaction_product_holder_schema')){
            
            $definition = $container->getDefinition('transaction_product_holder_schema');
            
            $definition->replaceArgument(0, $config['yml.transaction_productholder']);
            
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
