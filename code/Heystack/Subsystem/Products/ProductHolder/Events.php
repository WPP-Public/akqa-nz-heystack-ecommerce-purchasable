<?php
/**
 * This file is part of the Ecommerce-Products package
 *
 * @package Ecommerce-Products
 */

/**
 * ProductHolder namespace
 */
namespace Heystack\Subsystem\Products\ProductHolder;

/**
 * Events holds constant references to triggerable dispatch events.
 *
 * @copyright  Heyday
 * @author Stevie Mayhew <stevie@heyday.co.nz>
 * @author Cameron Spiers <cam@heyday.co.nz>
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Products
 * @see Symfony\Component\EventDispatcher
 *
 */
final class Events
{
    /**
     * Used to indicate that purchasable was added to the productholder
     */
    const PURCHASABLE_ADDED       = 'productholder.purchasableadded';

    /**
     * Used to indicate that a purchasable has changed on the productholder
     */
    const PURCHASABLE_CHANGED    = 'productholder.purchasablechanged';

    /**
     * Used to indicate that a purchasable has been removed from the
     * productholder
     */
    const PURCHASABLE_REMOVED    = 'productholder.purchasableremoved';

    /**
     * Used to indicate that the productholder has been updated in some way
     */
    const UPDATED               = 'productholder.updated';

    /**
     * Used to indicate that the productholder has been stored
     */
    const STORED               = 'productholder.stored';
}
