<?php

use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableInterface;
use Heystack\Subsystem\Core\Storage\DataObjectCodeGenerator\Interfaces\DataObjectCodeGeneratorInterface;

class Product extends DataObject implements PurchasableInterface, Serializable, DataObjectCodeGeneratorInterface
{

    use Heystack\Subsystem\Products\Product\DataObjectTrait;
    use Heystack\Subsystem\Core\State\Traits\ExtraDataTrait;

    protected $quantity = 0;
    protected $unitPrice = 0;

    public static $db = array(
        'Name' => 'Varchar(255)',
        'TestStuff' => 'Varchar(255)'
    );
    
    public static $has_one = array(
        'SingleStorable' => 'TestStorable'
    );
    
    public static $has_many = array(
        'MultiStorable' => 'TestStorable'
    );
    
    public static $many_many = array(
        'ManyStorable'=> 'TestStorable'
    );

    public function getExtraData()
    {
        return array(
            'quantity' => $this->quantity,
            'unitPrice' => $this->unitPrice
        );
    }

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
            'Name' => 'Varchar(255)'
        );
    }
    
    public function getStorableSingleRelations()
    {
       
        return self::$has_one;
  
    }
    
    public function getStorableManyRelations()
    {
        
        //return self::$has_many;
        return array_merge(self::$has_many, self::$many_many);
        
    }

}
