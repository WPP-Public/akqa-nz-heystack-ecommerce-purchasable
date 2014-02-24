<?php
/**
 * This file is part of the Ecommerce-Purchasable package
 *
 * @package Ecommerce-Purchasable
 */

namespace Heystack\Subsystem\Purchasable\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ContainerConfig implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('purchasable');

        $rootNode
            ->children()
                ->scalarNode('purchasable_class')->isRequired()->end()
                ->scalarNode('yml_transaction_purchasableholder')->end()
                ->scalarNode('yml_purchasable')->end()
                ->scalarNode('yml_purchasableholder')->end()
            ->end();

        return $treeBuilder;
    }
}
