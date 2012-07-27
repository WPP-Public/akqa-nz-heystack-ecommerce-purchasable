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

/**
 * Holds constants corresponding to the services defined in the services.yml file
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Products
 */
final class Services
{
    /**
     * Holds the identfier of the product holder
     * For use with the ServiceStore::getService($identifier) call
     */
    const PRODUCTHOLDER = 'productholder';

    /**
     * Holds the identifier of the product holder subscriber
     * For use with the ServiceStore::getService($identifier) call
     */
    const PRODUCTHOLDER_SUBSCRIBER = 'productholder_subscriber';

    /**
     * Holds the identifier of the product input processor
     * For use with the ServiceStore::getService($identifier) call
     */
    const PRODUCT_INPUT_PROCESSOR = 'product_input_processor';

    /**
     * Holds the identifier of the product output processor
     * For use with the ServiceStore::getService($identifier) call
     */
    const PRODUCT_OUTPUT_PROCESSOR = 'product_output_processor';
}
