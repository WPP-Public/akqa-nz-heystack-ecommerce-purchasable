<?php
/**
 * This file is part of the Ecommerce-Purchasable package
 *
 * @package Ecommerce-Purchasable
 */

/**
 * Purchasable namespace
 */
namespace Heystack\Subsystem\Purchasable;

/**
 * Holds constants corresponding to the services defined in the services.yml file
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Purchasable
 */
final class Services
{
    /**
     * Holds the identfier of the purchasable holder
     * For use with the Injector::inst()->get('heystack.' . 'SERVICE_NAME') call
     */
    const PURCHASABLEHOLDER = 'purchasableholder';

    /**
     * Holds the identifier of the purchasable holder subscriber
     * For use with the Injector::inst()->get('heystack.' . 'SERVICE_NAME') call
     */
    const PURCHASABLEHOLDER_SUBSCRIBER = 'purchasableholder_subscriber';

    /**
     * Holds the identifier of the purchasable input processor
     * For use with the Injector::inst()->get('heystack.' . 'SERVICE_NAME') call
     */
    const PURCHASABLE_INPUT_PROCESSOR = 'purchasable_input_processor';

    /**
     * Holds the identifier of the purchasable output processor
     * For use with the Injector::inst()->get('heystack.' . 'SERVICE_NAME') call
     */
    const PURCHASABLE_OUTPUT_PROCESSOR = 'purchasable_output_processor';
}
