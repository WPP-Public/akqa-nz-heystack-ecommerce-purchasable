<?php

use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableInterface;

class Product extends DataObject implements PurchasableInterface, Serializable
{

    use Heystack\Subsystem\Products\Product\DataObjectTrait;

    public static $db = array(
        'Name' => 'Varchar(255)'
    );

    public function getPrice()
    {
        return 100;
    }

}
