<?php

class ProductsAdmin extends ModelAdmin
{

    public static $managed_models = array(
        'Product'
    );

    public static $url_segment = 'products';
    public static $menu_title = 'Products';

}