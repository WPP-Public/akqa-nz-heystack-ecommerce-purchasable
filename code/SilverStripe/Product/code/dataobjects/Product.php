<?php

use Heystack\Subsystem\Ecommerce\Purchaseable\Interfaces\PurchaseableInterface;

class Product extends DataObject implements PurchaseableInterface, Serializable
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

    public function addStateService(\Heystack\Subsystem\Core\State\State $stateService)
    {

    }

    public function addEventDispatcher(\Symfony\Component\EventDispatcher\EventDispatcher $eventDispatcher)
    {

    }

    public function serialize()
    {

        return serialize($this->record);

    }

    public function unserialize($data)
    {

        $this->class = get_class($this);
        $this->record = unserialize($data);

    }

}
