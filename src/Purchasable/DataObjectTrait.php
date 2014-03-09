<?php

namespace Heystack\Purchasable\Purchasable;

use Heystack\Core\Identifier\Identifier;
use Heystack\Core\State\State;
use Heystack\Core\State\Traits\DataObjectSerializableTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class DataObjectTrait
 * @package Heystack\Purchasable\Purchasable
 */
trait DataObjectTrait
{
    use DataObjectSerializableTrait;

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
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventService
     */
    public function addEventService(EventDispatcherInterface $eventService)
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
