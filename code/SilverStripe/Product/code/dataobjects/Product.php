<?php

use Heystack\Subsystem\Ecommerce\Purchaseable\Interfaces\PurchaseableInterface;

class Product extends DataObject implements PurchaseableInterface, Serializable
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
