<?php
/**
 * This file is part of the Ecommerce-Products package
 *
 * @package Ecommerce-Products
 */

/**
 * Products namespace
 */
namespace Heystack\Subsystem\Products;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use Heystack\Subsystem\Core\ContainerExtensionConfigProcessor;
use Heystack\Subsystem\Core\Services as CoreServices;

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
        
        $dataObjectGenerator =
            $container->hasDefinition(CoreServices::DATAOBJECT_GENERATOR)
            ? $container->getDefinition(CoreServices::DATAOBJECT_GENERATOR)
            : false;        
        
        $config = array_pop($config);
            
        if ($dataObjectGenerator) {
            $dataObjectGenerator->addMethodCall(
                'addYamlSchema', array(
                    isset($config['yml.product'])
                        ? $config['yml.product']
                        : 'ecommerce-products/config/storage/product.yml'
                )
            );

            $dataObjectGenerator->addMethodCall(
                'addYamlSchema', array(
                    isset($config['yml.productholder'])
                        ? $config['yml.productholder']
                        : 'ecommerce-products/config/storage/productholder.yml'
                )
            );
            
            $dataObjectGenerator->addMethodCall(
                'addYamlSchema', array(
                    isset($config['yml.transaction_productholder'])
                        ? $config['yml.transaction_productholder']
                        : 'ecommerce-products/config/storage/transaction_productholder.yml'
                )
            );
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
