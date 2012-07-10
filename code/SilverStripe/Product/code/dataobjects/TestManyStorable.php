<?php

use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableInterface;
use Heystack\Subsystem\Core\Storage\DataObjectCodeGenerator\Interfaces\DataObjectCodeGeneratorInterface;
use Heystack\Subsystem\Core\State\ExtraDataInterface;

class TestManyStorable extends DataObject implements Serializable, DataObjectCodeGeneratorInterface
{

    use Heystack\Subsystem\Products\Product\DataObjectTrait;

    protected $quantity = 0;
    protected $unitPrice = 0;

    public static $db = array(
        'Name' => 'Varchar(255)',
        'TestStuff' => 'Varchar(255)'
    );
    
    public static $has_one = array(
        'Product' => 'Product'
    );
    

    public function getPrice()
    {
        return $this->ID * 100.00;
    }

    public function setUnitPrice(\Float $unitPrice)
    {
        $this->unitPrice = $unitPrice;
    }

    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    public function setQuantity($quantity = 1)
    {
        $this->quantity = $quantity;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getTotal()
    {
        return $this->getQuantity() * $this->getUnitPrice();
    }
    
    public function getStorableData()
    {
        return array(
            'TestStuff' => 'Varchar(255)'
        );
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
