<?php

namespace Heystack\Subsystem\Products\ProductHolder;

use Heystack\Subsystem\Core\State\State;

use Symfony\Component\EventDispatcher\EventDispatcher;

class ProductHolder implements PurchaseableHolderInterface
{

    private $stateService;
    private $eventDispatcher;
    private $purchaseables = array();

    public function __construct(State $stateService, EventDispatcher $eventDispatcher)
    {

        $this->stateService = $stateService;
        $this->eventDispatcher = $eventDispatcher;

    }

    public function addPurchaseable(PurchaseableInterface $purchaseable)
    {

        $purchaseable->addStateService($this->stateService);
        $purchaseable->addEventDispatcher($this->eventDispatcher);

        $this->purchaseables[$purchaseables->getIdentifier()] = $purchaseable;

    }

    public function getPurchaseable($identifier)
    {

        return isset($this->purchaseables[$identifier]) ? $this->purchaseables[$identifier] : false;

    }

    public function getPurchaseables($identifiers = null)
    {

        $purchaseables = array();

        if (!is_null($identifiers) && $identifiers == (array) $identifiers) {

            foreach ($identifiers as $identifier) {

                if ($purchaseable = $this->getPurchaseable($identifier)) {

                    $purchaseables[] = $purchaseable;

                }

            }

        } else {

            $purchaseables = $this->purchaseables;

        }

        return $purchaseables;

    }

    public function setPurchaseables(array $purchaseables)
    {

        foreach ($purchaseables as $purchaseable) {

            $this->addPurchaseable($purchaseable);

        }

    }

}
