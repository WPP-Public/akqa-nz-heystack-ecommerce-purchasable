<?php
/**
 * This file is part of the Heystack package
 *
 * @package Ecommerce-Products
 */

/**
 * Product ModelAdmin
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @author Cameron Spiers <cameron@heyday.co.nz>
 * @author Stevie Mayhew <glenn@heyday.co.nz>
 * @package Heystack
 *
 */
class ProductsAdmin extends ModelAdmin
{

    public static $managed_models = array(
        'Product'
    );

    public static $url_segment = 'products';
    public static $menu_title = 'Products';

}
