<?php


namespace Heystack\Subsystem\Products\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @package Heystack\Subsystem\Ecommerce\Config
 */
class ContainerConfig implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('products');

        $rootNode
            ->children()
                ->scalarNode('product_class')->isRequired()->end()
                ->scalarNode('yml_transaction_productholder')->end()
                ->scalarNode('yml_product')->end()
                ->scalarNode('yml_productholder')->end()
            ->end();

        return $treeBuilder;
    }
}