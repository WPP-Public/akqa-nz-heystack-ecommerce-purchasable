<?php

use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableInterface;
use Heystack\Subsystem\Core\Storage\DataObjectCodeGenerator\Interfaces\DataObjectCodeGeneratorInterface;

class TestStorable extends DataObject implements PurchasableInterface, Serializable, DataObjectCodeGeneratorInterface
{

    use Heystack\Subsystem\Products\Product\DataObjectTrait;

    public static $db = array(
        'Name' => 'Varchar(255)',
        'TestStuff' => 'Varchar(255)'
    );

    public function getPrice()
    {
        return 100;
    }
    
    public function getStorableData()
    {
        return self::$db;
    }
    
    public function getStorableSingleRelations()
    {
        return array();
    }
    
    public function getStorableManyRelations()
    {
        return array();
    }

}
