<?php

use Heystack\Subsystem\Ecommerce\Purchaseable\Interfaces\PurchaseableInterface;

class Product extends DataObject implements PurchaseableInterface, Serializable
{

    use Heystack\Subsystem\Core\State\Traits\DataObjectSerializableTrait;

    private $stateService;
    private $eventService;

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

        $this->stateService = $stateService;

    }

    public function addEventService(\Symfony\Component\EventDispatcher\EventDispatcher $eventService)
    {

        $this->eventService = $eventService;

    }

}
