<?php

class Product implements PurchaseableInterface
{
    
    public static $db = array(
        'Price' => 'EcommercePrice'
    );
    
    public function getID()
    {
        return $this->ID;
    }
    
    public function getIdentifier()
    {
        return $this->ClassName . $this->ID;
    }
    
    public function getPrice()
    {
        return $this->PriceAmount;
    }
}
