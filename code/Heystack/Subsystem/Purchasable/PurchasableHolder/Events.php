<?php
/**
 * This file is part of the Ecommerce-Purchasable package
 *
 * @package Ecommerce-Purchasable
 */

/**
 * PurchasableHolder namespace
 */
namespace Heystack\Subsystem\Purchasable\PurchasableHolder;

/**
 * Events holds constant references to triggerable dispatch events.
 *
 * @copyright  Heyday
 * @author Stevie Mayhew <stevie@heyday.co.nz>
 * @author Cameron Spiers <cam@heyday.co.nz>
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Purchasable
 * @see Symfony\Component\EventDispatcher
 *
 */
final class Events
{
    /**
     * Used to indicate that purchasable was added to the purchasableholder
     */
    const PURCHASABLE_ADDED       = 'purchasableholder.purchasableadded';

    /**
     * Used to indicate that a purchasable has changed on the purchasableholder
     */
    const PURCHASABLE_CHANGED     = 'purchasableholder.purchasablechanged';

    /**
     * Used to indicate that a purchasable has been removed from the
     * purchasableholder
     */
    const PURCHASABLE_REMOVED     = 'purchasableholder.purchasableremoved';

    /**
     * Used to indicate that the purchasableholder has been updated in some way
     */
    const UPDATED                 = 'purchasableholder.updated';

    /**
     * Used to indicate that the purchasableholder has been stored
     */
    const STORED                  = 'purchasableholder.stored';
}
