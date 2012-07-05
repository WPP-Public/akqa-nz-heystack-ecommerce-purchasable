<?php

namespace Heystack\Subsystem\Products\Product;

trait DataObjectTrait
{

    use \Heystack\Subsystem\Core\State\Traits\DataObjectSerializableTrait;

    private $stateService;
    private $eventService;

    public function getIdentifier()
    {
        return $this->ClassName . $this->ID;
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
