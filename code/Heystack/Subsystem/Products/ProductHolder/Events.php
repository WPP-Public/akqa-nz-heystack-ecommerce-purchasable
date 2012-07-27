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
    const PURCHASABLE_ADDED       = 'productholder.purchasableadded';
    const PURCHASABLE_CHANGED    = 'productholder.purchasablechanged';
    const PURCHASABLE_REMOVED    = 'productholder.purchasableremoved';
    const UPDATED               = 'productholder.updated';
    const STORED               = 'productholder.stored';
}
