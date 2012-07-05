<?php
/**
 * This file is part of the Ecommerce-Products package
 * 
 * @package Heystack
 */

/**
 * ProductHolder namespace
 */
namespace Heystack\Subsystem\Products\ProductHolder;

use Heystack\Subsystem\Core\State\State;
use Heystack\Subsystem\Core\State\StateableInterface;
use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;
use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableInterface;

use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Purchasable Holder implementation for Ecommerce-Products
 * 
 * This class is our version of a 'cart'. It holds together all the 
 * 'purchasables' in for an order. Notice that it also implements serializable 
 * and Stateable.
 * 
 * @copyright  Heyday
 * @author Stevie Mayhew <stevie@heyday.co.nz>
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @author Cam Spiers <cameron@heyday.co.nz>
 * @package Heystack
 * 
 */
class ProductHolder implements PurchasableHolderInterface, StateableInterface, \Serializable
{

    /**
     * State Key constant
     */
    const STATE_KEY = 'productholder';

    /**
     * Holds the State service
     * @var State 
     */
    private $stateService;
    
    /**
     * Holds the EventDispatcher Service
     * @var EventDispatcher 
     */
    private $eventService;
    
    /**
     * An array of Purchasables
     * @var array
     */
    private $purchasables = array();
    
    /**
     * ProductHolder Constructor. Not directly called, use the ServiceStore to
     * get an instance of this class
     * 
     * @param \Heystack\Subsystem\Core\State\State $stateService
     * @param \Symfony\Component\EventDispatcher\EventDispatcher $eventService
     */
    public function __construct(State $stateService, EventDispatcher $eventService)
    {

        $this->stateService = $stateService;
        $this->eventService = $eventService;

    }

    /**
     * Returns a serialized string from the purchasables array
     * @return string
     */
    public function serialize()
    {

        return serialize($this->purchasables);

    }

    /**
     * Unserializes the data into the purchasables array
     * @param string $data
     */
    public function unserialize($data)
    {

        $this->purchasables = unserialize($data);

    }

    /**
     * Uses the State service to restore the pruchasables array
     */
    public function restoreState()
    {

        $this->purchasables = $this->stateService->getObj(self::STATE_KEY);

    }

    /**
     * Saves the purchasables array on the State service
     */
    public function saveState()
    {

        $this->stateService->setObj(self::STATE_KEY, $this->purchasables);

    }

    public function addPurchasable(PurchasableInterface $purchasable,$quantity = 1)
    {
        if ($cachedPurchasable = $this->getPurchasable($purchasable->getIdentifier())) {

            $cachedPurchasable->setQuantity($cachedPurchasable->getQuantity() + $quantity);

        } else {
            $purchasable->addStateService($this->stateService);
            $purchasable->addEventService($this->eventService);

            $purchasable->setQuantity($purchasable->getQuantity() + $quantity);
            $purchasable->setUnitPrice($purchasable->getPrice());

            $this->purchasables[$purchasable->getIdentifier()] = $purchasable;

        }

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
