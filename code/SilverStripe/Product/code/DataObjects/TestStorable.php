<?php

use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableInterface;
use Heystack\Subsystem\Core\Storage\DataObjectCodeGenerator\Interfaces\DataObjectCodeGeneratorInterface;
use Heystack\Subsystem\Core\State\ExtraDataInterface;

class TestStorable extends DataObject implements Serializable, DataObjectCodeGeneratorInterface
{

    use Heystack\Subsystem\Products\Product\DataObjectTrait;


    protected $quantity = 0;
    protected $unitPrice = 0;

    public static $db = array(
        'Name' => 'Varchar(255)',
        'TestStuff' => 'Int'
    );
    
    public function getStorableData()
    {
        return self::$db;
    }
    
    public function getStorableSingleRelations()
    {
       
        //return self::$has_one;
  
    }
    
    public function getStorableManyRelations()
    {
        
        //return self::$has_many;
        //return array_merge(self::$has_many, self::$many_many);
        
    }

}
