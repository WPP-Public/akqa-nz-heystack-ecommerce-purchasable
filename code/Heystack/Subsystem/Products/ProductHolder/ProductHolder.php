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
use Heystack\Subsystem\Core\Identifier\IdentifierInterface;
use Heystack\Subsystem\Core\Identifier\Identifier;
use Heystack\Subsystem\Core\State\StateableInterface;

use Heystack\Subsystem\Core\Storage\StorableInterface;
use Heystack\Subsystem\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Subsystem\Core\Storage\Traits\ParentReferenceTrait;

use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;
use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableInterface;

use Heystack\Subsystem\Ecommerce\Transaction\TransactionModifierTypes;
use Heystack\Subsystem\Ecommerce\Transaction\Traits\TransactionModifierStateTrait;
use Heystack\Subsystem\Ecommerce\Transaction\Traits\TransactionModifierSerializeTrait;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
class ProductHolder implements PurchasableHolderInterface, StateableInterface, \Serializable, StorableInterface
{
    use TransactionModifierStateTrait;
    use TransactionModifierSerializeTrait;
    use ParentReferenceTrait;

    /**
     * State Key constant
     */
    const IDENTIFIER = 'productholder';
    const PURCHASABLES_KEY = 'purchasables';
    const TOTAL_KEY = 'total';

    /**
     * Holds the State service
     * @var State
     */
    protected $stateService;

    /**
     * Holds the EventDispatcher Service
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventService;

    protected $data = array();

    /**
     * ProductHolder Constructor. Not directly called, use the ServiceStore to
     * get an instance of this class
     *
     * @param \Heystack\Subsystem\Core\State\State                        $stateService
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventService
     */
    public function __construct(State $stateService, EventDispatcherInterface $eventService)
    {
        $this->stateService = $stateService;
        $this->eventService = $eventService;
    }
    /**
     * @return \Heystack\Subsystem\Core\Identifier\IdentifierInterface
     */
    public function getIdentifier()
    {
        return new Identifier(self::IDENTIFIER);
    }

    /**
     * Indicates that this modifier is chargeable
     */
    public function getType()
    {
        return TransactionModifierTypes::CHARGEABLE;
    }

    /**
     * Adds a purchasable object to the product holder and increments the
     * quantity
     * @param PurchasableInterface $purchasable The purchasable object
     * @param integer              $quantity    quantity of the object to add
     */
    public function addPurchasable(PurchasableInterface $purchasable, $quantity = 1)
    {
        if ($cachedPurchasable = $this->getPurchasable($purchasable->getIdentifier())) {
            $this->setPurchasable($cachedPurchasable, $cachedPurchasable->getQuantity() + $quantity);
        } else {
            $this->setPurchasable($purchasable, $quantity);
        }
    }

    /**
     * Sets the quantity of a purchasable object on the product holder
     * @param PurchasableInterface $purchasable The purchasable object
     * @param int                  $quantity    quantity of the purchasable object to be set
     */
    public function setPurchasable(PurchasableInterface $purchasable, $quantity)
    {
        if ($cachedPurchasable = $this->getPurchasable($purchasable->getIdentifier())) {

            $cachedPurchasable->setQuantity($quantity);

            $this->eventService->dispatch(Events::PURCHASABLE_CHANGED);

        } else {

            $purchasable->addStateService($this->stateService);
            $purchasable->addEventService($this->eventService);

            $purchasable->setQuantity($quantity);
            $purchasable->setUnitPrice($purchasable->getPrice());

            $this->data[self::PURCHASABLES_KEY][$purchasable->getIdentifier()->getFull()] = $purchasable;

            $this->eventService->dispatch(Events::PURCHASABLE_ADDED);

        }

    }

    /**
     * Returns a purchasable by its identifier
     * @param  \Heystack\Subsystem\Core\Identifier\IdentifierInterface $identifier The identifier of the purchasable
     * @return PurchasableInterface|false The Purchasable object if found
     */
    public function getPurchasable(IdentifierInterface $identifier)
    {
        $fullIdentifier = $identifier->getFull();

        return isset($this->data[self::PURCHASABLES_KEY][$fullIdentifier]) ? $this->data[self::PURCHASABLES_KEY][$fullIdentifier] : false;
    }

    public function getPurchasablesByPrimaryIdentifier(IdentifierInterface $identifier)
    {
        $matches = array();

        foreach($this->data[self::PURCHASABLES_KEY] as $purchasable) {
            if($purchasable->getIdentifier()->isMatch($identifier)){
                $matches[] = $purchasable;
            }
        }

        if(count($matches)){
            return $matches;
        }

        return false;
    }

    /**
     * Removes a purchasable from the product holder if found
     * @param  \Heystack\Subsystem\Core\Identifier\IdentifierInterface $identifier The identifier of the purchasable to remove
     * @return null
     */
    public function removePurchasable(IdentifierInterface $identifier)
    {
        $fullIdentifier = $identifier->getFull();

        if (isset($this->data[self::PURCHASABLES_KEY][$fullIdentifier])) {

            unset($this->data[self::PURCHASABLES_KEY][$fullIdentifier]);

            $this->eventService->dispatch(Events::PURCHASABLE_REMOVED);

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

            $purchasables = $this->data[self::PURCHASABLES_KEY];

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

    /**
     * Get the current purchasable total on the product holder
     * @return float
     */
    public function getTotal()
    {
        return isset($this->data[self::TOTAL_KEY]) ? $this->data[self::TOTAL_KEY] : 0;
    }

    /**
     * Update the purchasable total on the product holder
     */
    public function updateTotal()
    {

        if (isset($this->data[self::PURCHASABLES_KEY])) {

            $total = 0;

            foreach ($this->data[self::PURCHASABLES_KEY] as $purchasable) {

                $total += $purchasable->getTotal();

            }

            $this->data[self::TOTAL_KEY] = $total;

            $this->eventService->dispatch(Events::UPDATED);

        }

        $this->saveState();

    }

    /**
     * Update the purchasable prices on the holder
     */
    public function updatePurchasablePrices()
    {
        if (isset($this->data[self::PURCHASABLES_KEY])) {

            foreach ($this->data[self::PURCHASABLES_KEY] as $purchasable) {

                $purchasable->setUnitPrice($purchasable->getPrice());

            }

        }

        $this->saveState();
    }

    /**
     * Get tax exemptions from products if they exist
     */
    public function getTaxExemptTotal()
    {
        $total = 0;

        if (isset($this->data[self::PURCHASABLES_KEY])) {

            foreach ($this->data[self::PURCHASABLES_KEY] as $purchasable) {

                if (method_exists($purchasable, 'isTaxExempt') && $purchasable->isTaxExempt()) {

                    $total += $purchasable->getTotal();

                }

            }

        }

        return $total;

    }

    /**
     * Get the data to store for the productholder
     * @return array
     */
    public function getStorableData()
    {

       $data = array();

       $data['id'] = 'ProductHolder';

       $data['flat'] = array(
           'Total' => $this->getTotal(),
           'NoOfItems' => count($this->getPurchasables()),
           'ParentID' => $this->parentReference
       );

       $data['parent'] = true;

       return $data;

    }

    /**
     * Get the identifier for this storage system
     * @return string
     */
    public function getStorableIdentifier()
    {

        return self::IDENTIFIER;

    }

    /**
     * Get the name of the schema this system relates to
     * @return string
     */
    public function getSchemaName()
    {

        return 'ProductHolder';

    }

    /**
     * Get the type of storage that this object is using
     * @return string
     */
    public function getStorableBackendIdentifiers()
    {
        return array(
            Backend::IDENTIFIER
        );
    }

}
