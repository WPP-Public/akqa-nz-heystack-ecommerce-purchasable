<?php

class ProductsAdmin extends ModelAdmin
{

    public static $managed_models = array(
        'Product',
        'TestStorable',
        'TestManyStorable',
        'TestManyManyStorable',
        'StoredProduct'
    );

    public static $url_segment = 'products';
    public static $menu_title = 'Products';

}
