<?php

namespace Heystack\Subsystem\Products\ProductHolder;

use Heystack\Subsystem\Core\State\State;
use Heystack\Subsystem\Core\State\StateableInterface;
use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;
use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableInterface;

use Symfony\Component\EventDispatcher\EventDispatcher;

class ProductHolder implements PurchasableHolderInterface, StateableInterface, \Serializable
{

    const STATE_KEY = 'productholder';

    private $stateService;
    private $eventService;
    private $purchasables = array();

    public function __construct(State $stateService, EventDispatcher $eventService)
    {

        $this->stateService = $stateService;
        $this->eventService = $eventService;

    }

    public function serialize()
    {

        return serialize($this->purchasables);

    }

    public function unserialize($data)
    {

        $this->purchasables = unserialize($data);

    }

    public function restoreState()
    {

        $this->purchasables = $this->stateService->getObj(self::STATE_KEY);

    }

    public function saveState()
    {

        $this->stateService->setObj(self::STATE_KEY, $this->purchasables);

    }

    public function addPurchasable(PurchasableInterface $purchasable)
    {

        $purchasable->addStateService($this->stateService);
        $purchasable->addEventService($this->eventService);

        $this->purchasables[$purchasable->getIdentifier()] = $purchasable;

    }

    public function getPurchasable($identifier)
    {

        return isset($this->purchasables[$identifier]) ? $this->purchasables[$identifier] : false;

    }

    public function removePurchasable($identifier)
    {

        if (isset($this->purchasables[$identifier])) {

            unset($this->purchasables[$identifier]);

        }

    }

    public function getPurchasables($identifiers = null)
    {

        $purchasables = array();

        if (!is_null($identifiers) && $identifiers == (array) $identifiers) {

            foreach ($identifiers as $identifier) {

                if ($purchasable = $this->getPurchasable($identifier)) {

                    $purchasables[] = $purchasable;

                }

            }

        } else {

            $purchasables = $this->purchasables;

        }

        return $purchasables;

    }

    public function setPurchasables(array $purchasables)
    {

        foreach ($purchasables as $purchasable) {

            $this->addPurchasable($purchasable);

        }

    }

}
