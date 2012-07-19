<?php

use Heystack\Subsystem\Core\Storage\StorableInterface;

class TestManyStorable extends DataObject implements Serializable, StorableInterface
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
        $data = array();
        
        $data['id'] = "TestManyStorable";
        
        $data['flat'] = array(
            'Name' => $this->Name,
            'TestStuff' => $this->TestStuff
            
        );
        
        return $data;
    }


}
