<?php

class Product extends DataObject implements PurchaseableInterface
{

    public static $db = array(

    );

    public function getIdentifier()
    {
        return $this->ClassName . $this->ID;
    }

    public function getPrice()
    {
        return 100;
    }
}
