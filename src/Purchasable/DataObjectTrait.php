<?php

namespace Heystack\Purchasable\Purchasable;

use Heystack\Core\Identifier\Identifier;
use Heystack\Core\State\State;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class DataObjectTrait
 * @package Heystack\Purchasable\Purchasable
 */
trait DataObjectTrait
{
    use \Heystack\Core\State\Traits\DataObjectSerializableTrait;

    /**
     * @var
     */
    private $stateService;
    /**
     * @var
     */
    private $eventService;
    /**
     * @return \Heystack\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return new Identifier($this->ClassName . $this->ID);
    }

    /**
     * @param \Heystack\Core\State\State $stateService
     */
    public function addStateService(State $stateService)
    {
        $this->stateService = $stateService;
    }

    /**
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $eventService
     */
    public function addEventService(EventDispatcher $eventService)
    {
        $this->eventService = $eventService;
    }

    /**
     * @return string
     */
    public function getStorageIdentifier()
    {
        return 'dataobject';
    }
}
