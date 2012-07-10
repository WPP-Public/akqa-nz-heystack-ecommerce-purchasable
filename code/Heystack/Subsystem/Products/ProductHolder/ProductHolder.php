<?php
/**
 * This file is part of the Ecommerce-Products package
 *
 * @package Ecommerce-Products
 */

/**
 * ProductHolder namespace
 */
namespace Heystack\Subsystem\Products\ProductHolder;

use Heystack\Subsystem\Core\State\State;
use Heystack\Subsystem\Core\State\StateableInterface;
use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;
use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableInterface;
use Heystack\Subsystem\Products\ProductHolder\Event\ProductHolderEvent;

use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Purchasable Holder implementation for Ecommerce-Products
 *
 * This class is our version of a 'shopping cart'. It holds together all the
 * 'purchasables' in for an order. Notice that it also implements serializable
 * and Stateable.
 *
 * @copyright  Heyday
 * @author Stevie Mayhew <stevie@heyday.co.nz>
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @author Cam Spiers <cameron@heyday.co.nz>
 * @package Ecommerce-Products
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
     * @param \Heystack\Subsystem\Core\State\State               $stateService
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

    /**
     * Adds a purchasable object to the product holder and increments the
     * quantity
     * @param PurchasableInterface $purchasable The purchasable object
     * @param integer              $quantity    quantity of the object to add
     */
    public function addPurchasable(PurchasableInterface $purchasable, $quantity)
    {
        
        if ($cachedPurchasable = $this->getPurchasable($purchasable->getIdentifier())) {
            
            $this->setPurchasable($cachedPurchasable, $cachedPurchasable->getQuantity() + $quantity);
            
            $dispatchedPurchasable = $cachedPurchasable;

        } else {
            
            $this->setPurchasable($purchasable, $quantity);
            
            $dispatchedPurchasable = $cachedPurchasable;

        }

    }
    
    /**
     * Sets the quantity of a purchasable object on the product holder
     * @param PurchasableInterface $purchasable The purchasable object
     * @param int $quantity quantity of the purchasable object to be set
     */
    public function setPurchasable(PurchasableInterface $purchasable, $quantity)
    {        
        if ($cachedPurchasable = $this->getPurchasable($purchasable->getIdentifier())) {

            $cachedPurchasable->setQuantity($quantity);
            
            $this->eventService->dispatch(Events::PRODUCTHOLDER_CHANGE_PURCHASABLE, new ProductHolderEvent($this,$cachedPurchasable));

        } else {

            $purchasable->addStateService($this->stateService);
            $purchasable->addEventService($this->eventService);

            $purchasable->setQuantity($quantity);
            $purchasable->setUnitPrice($purchasable->getPrice());

            $this->purchasables[$purchasable->getIdentifier()] = $purchasable;
            
            $this->eventService->dispatch(Events::PRODUCTHOLDER_ADD_PURCHASABLE, new ProductHolderEvent($this,$purchasable));

        }
        
    }

    /**
     * Returns a purchasable by its identifier
     * @param  string                     $identifier The identifier of the purchasable
     * @return PurchasableInterface|false The Purchasable object if found
     */
    public function getPurchasable($identifier)
    {

        return isset($this->purchasables[$identifier]) ? $this->purchasables[$identifier] : false;

    }

    /**
     * Removes a purchasable from the product holder if found
     * @param  string $identifier The identifier of the purchasable to remove
     * @return null
     */
    public function removePurchasable($identifier)
    {

        if (isset($this->purchasables[$identifier])) {
            
            $purchasable = $this->purchasables[$identifier];

            unset($this->purchasables[$identifier]);
            
            $this->eventService->dispatch(Events::PRODUCTHOLDER_REMOVE_PURCHASABLE, new ProductHolderEvent($this,$purchasable));

        }

    }

    /**
     * Get multiple purchasables, if no identifiers are passed in then return all purchasables
     * @param  array|null $identifiers An array of identifiers if passed in
     * @return array      And array of purchasables
     */
    public function getPurchasables(array $identifiers = null)
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

    /**
     * Set an array of purchasables on the product holder
     * @param array $purchasables Array of purchasables
     */
    public function setPurchasables(array $purchasables)
    {

        foreach ($purchasables as $purchasable) {

            $this->addPurchasable($purchasable);

        }

    }
    
    public function saveToDatabase() 
    {
        
        $storage = \Heystack\Subsystem\Core\ServiceStore::getService('storage_processor');
        
        $purchaseables = $this->getPurchasables(NULL);
        
        foreach ($purchaseables as $purchaseable) {
            
            $storage->process($purchaseable);
            
        }
        
    }

}
