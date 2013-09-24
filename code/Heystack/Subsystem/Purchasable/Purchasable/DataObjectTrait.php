<?php

namespace Heystack\Subsystem\Purchasable\Purchasable;

use Heystack\Subsystem\Core\Identifier\Identifier;
use Heystack\Subsystem\Core\State\State;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Class DataObjectTrait
 * @package Heystack\Subsystem\Purchasable\Purchasable
 */
trait DataObjectTrait
{
    use \Heystack\Subsystem\Core\State\Traits\DataObjectSerializableTrait;

    /**
     * @var
     */
    private $stateService;
    /**
     * @var
     */
    private $eventService;
    /**
     * @return \Heystack\Subsystem\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return new Identifier($this->ClassName . $this->ID);
    }

    /**
     * @param \Heystack\Subsystem\Core\State\State $stateService
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
