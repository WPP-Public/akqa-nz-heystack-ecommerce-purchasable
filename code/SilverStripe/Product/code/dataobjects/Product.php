<?php

use Heystack\Subsystem\Ecommerce\Purchaseable\Interfaces\PurchaseableInterface;
use Heystack\Subsystem\Core\State\State;

use Symfony\Component\EventDispatcher\EventDispatcher;

class Product extends DataObject implements PurchaseableInterface
{

    public static $db = array(
        'Name' => 'Varchar(255)'
    );

    public function getIdentifier()
    {
        return $this->ClassName . $this->ID;
    }

    public function getPrice()
    {
        return 100;
    }

    public function addStateService(State $stateService)
    {

    }

    public function addEventDispatcher(EventDispatcher $eventDispatcher)
    {

    }

}
