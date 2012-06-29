<?php

namespace Heystack\Subsystem\Products\ProductHolder;

use Heystack\Subsystem\Core\State\State;
use Heystack\Subsystem\Core\State\StateableInterface;
use Heystack\Subsystem\Ecommerce\Purchaseable\Interfaces\PurchaseableHolderInterface;
use Heystack\Subsystem\Ecommerce\Purchaseable\Interfaces\PurchaseableInterface;

use Symfony\Component\EventDispatcher\EventDispatcher;

class ProductHolder implements PurchaseableHolderInterface, StateableInterface, \Serializable
{

    private $stateService;
    private $eventDispatcher;
    private $purchaseables = array();
    private $stateKey = 'productholder';

    public function __construct(State $stateService, EventDispatcher $eventDispatcher)
    {

        $this->stateService = $stateService;
        $this->eventDispatcher = $eventDispatcher;

    }

    public function serialize()
    {

        return serialize($this->purchaseables);

    }

    public function unserialize($data)
    {

        $this->purchaseables = unserialize($data);

    }

    public function restoreState()
    {

        $this->purchaseables = $this->stateService->getObj($this->stateKey);

    }

    public function saveState()
    {

        $this->stateService->setObj($this->stateKey, $this->purchaseables);

    }

    public function addPurchaseable(PurchaseableInterface $purchaseable)
    {

        $purchaseable->addStateService($this->stateService);
        $purchaseable->addEventDispatcher($this->eventDispatcher);

        $this->purchaseables[$purchaseable->getIdentifier()] = $purchaseable;

    }

    public function getPurchaseable($identifier)
    {

        return isset($this->purchaseables[$identifier]) ? $this->purchaseables[$identifier] : false;

    }

    public function removePurchaseable($identifier)
    {

        if (isset($this->purchaseables[$identifier])) {

            unset($this->purchaseables[$identifier]);

        }

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
