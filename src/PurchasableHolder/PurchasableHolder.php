<?php
/**
 * This file is part of the Ecommerce-Purchasable package
 *
 * @package Ecommerce-Purchasable
 */

/**
 * PurchasableHolder namespace
 */
namespace Heystack\Subsystem\Purchasable\PurchasableHolder;

use Heystack\Subsystem\Core\Identifier\Identifier;
use Heystack\Subsystem\Core\Identifier\IdentifierInterface;
use Heystack\Subsystem\Core\Interfaces\HasDataInterface;
use Heystack\Subsystem\Core\Interfaces\HasEventServiceInterface;
use Heystack\Subsystem\Core\Interfaces\HasStateServiceInterface;
use Heystack\Subsystem\Core\State\State;
use Heystack\Subsystem\Core\State\StateableInterface;
use Heystack\Subsystem\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Subsystem\Core\Storage\StorableInterface;
use Heystack\Subsystem\Core\Storage\Traits\ParentReferenceTrait;
use Heystack\Subsystem\Core\Traits\HasEventService;
use Heystack\Subsystem\Core\Traits\HasStateService;
use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;
use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableInterface;
use Heystack\Subsystem\Ecommerce\Transaction\Traits\TransactionModifierSerializeTrait;
use Heystack\Subsystem\Ecommerce\Transaction\Traits\TransactionModifierStateTrait;
use Heystack\Subsystem\Ecommerce\Transaction\TransactionModifierTypes;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Purchasable Holder implementation for Ecommerce-Purchasable
 *
 * This class is our version of a 'shopping cart'. It holds together all the
 * 'purchasables' in for an order. Notice that it also implements serializable
 * and Stateable.
 *
 * @copyright  Heyday
 * @author Stevie Mayhew <stevie@heyday.co.nz>
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @author Cam Spiers <cameron@heyday.co.nz>
 * @package Ecommerce-Purchasable
 *
 */
class PurchasableHolder implements
    PurchasableHolderInterface,
    StateableInterface,
    \Serializable,
    StorableInterface,
    HasEventServiceInterface,
    HasStateServiceInterface,
    HasDataInterface
{
    use TransactionModifierStateTrait;
    use TransactionModifierSerializeTrait;
    use ParentReferenceTrait;
    use HasEventService;
    use HasStateService;

    /**
     * State Key constant
     */
    const IDENTIFIER = 'purchasableholder';
    const PURCHASABLES_KEY = 'purchasables';
    const TOTAL_KEY = 'total';

    /**
     * Stores data for state
     * @var array
     */
    protected $data;

    /**
     * PurchasableHolder Constructor. Not directly called, use the ServiceStore to
     * get an instance of this class
     *
     * @param \Heystack\Subsystem\Core\State\State $stateService
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
     * Adds a purchasable object to the purchasable holder and increments the
     * quantity
     * @param PurchasableInterface $purchasable The purchasable object
     * @param integer $quantity    quantity of the object to add
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
     * Sets the quantity of a purchasable object on the Purchasable holder
     * @param PurchasableInterface $purchasable The purchasable object
     * @param int $quantity    quantity of the purchasable object to be set
     */
    public function setPurchasable(PurchasableInterface $purchasable, $quantity)
    {
        if ($quantity === 0) {
            $this->removePurchasable($purchasable->getIdentifier());
        } else {
            if ($cachedPurchasable = $this->getPurchasable($purchasable->getIdentifier())) {

                if ($cachedPurchasable->getQuantity() != $quantity) {

                    $cachedPurchasable->setQuantity($quantity);

                    $this->getEventService()->dispatch(Events::PURCHASABLE_CHANGED);
                }

            } else {

                $eventService = $this->getEventService();

                $purchasable->addStateService($this->getStateService());
                $purchasable->addEventService($eventService);

                $purchasable->setQuantity($quantity);
                $purchasable->setUnitPrice($purchasable->getPrice());

                $this->data[self::PURCHASABLES_KEY][$purchasable->getIdentifier()->getFull()] = $purchasable;

                $eventService->dispatch(Events::PURCHASABLE_ADDED);
            }
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
        $matches = [];

        foreach ($this->data[self::PURCHASABLES_KEY] as $purchasable) {
            if ($purchasable->getIdentifier()->isMatch($identifier)) {
                $matches[] = $purchasable;
            }
        }

        if (count($matches)) {
            return $matches;
        }

        return false;
    }

    /**
     * Removes a purchasable from the Purchasable holder if found
     * @param  \Heystack\Subsystem\Core\Identifier\IdentifierInterface $identifier The identifier of the purchasable to remove
     * @return null
     */
    public function removePurchasable(IdentifierInterface $identifier)
    {
        $fullIdentifier = $identifier->getFull();

        if (isset($this->data[self::PURCHASABLES_KEY][$fullIdentifier])) {

            unset($this->data[self::PURCHASABLES_KEY][$fullIdentifier]);

            $this->getEventService()->dispatch(Events::PURCHASABLE_REMOVED);
        }
    }

    /**
     * Get multiple purchasables, if no identifiers are passed in then return all purchasables
     * @param  array|null $identifiers An array of identifiers if passed in
     * @return \Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableInterface[]  An array of purchasables
     */
    public function getPurchasables(array $identifiers = null)
    {
        $purchasables = [];

        if (!is_null($identifiers) && $identifiers == (array)$identifiers) {

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
     * Set an array of purchasables on the purchasable holder
     * @param array $purchasables Array of purchasables
     */
    public function setPurchasables(array $purchasables)
    {
        foreach ($purchasables as $purchasable) {

            $this->addPurchasable($purchasable);

        }
    }

    /**
     * Get the current purchasable total on the purchasable holder
     * @return float
     */
    public function getTotal()
    {
        return isset($this->data[self::TOTAL_KEY]) ? $this->data[self::TOTAL_KEY] : 0;
    }

    /**
     * Update the purchasable total on the purchasable holder
     */
    public function updateTotal()
    {

        if (isset($this->data[self::PURCHASABLES_KEY])) {

            $total = 0;

            foreach ($this->data[self::PURCHASABLES_KEY] as $purchasable) {

                $total += $purchasable->getTotal();

            }

            $this->data[self::TOTAL_KEY] = $total;
            $this->getEventService()->dispatch(Events::UPDATED);


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
     * Get tax exemptions from purchasables if they exist
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
     * Get the data to store for the purchasableholder
     * @return array
     */
    public function getStorableData()
    {

        $data = [];

        $data['id'] = 'PurchasableHolder';

        $data['flat'] = [
            'Total' => $this->getTotal(),
            'NoOfItems' => count($this->getPurchasables()),
            'ParentID' => $this->parentReference
        ];

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

        return 'PurchasableHolder';

    }

    /**
     * Get the type of storage that this object is using
     * @return string
     */
    public function getStorableBackendIdentifiers()
    {
        return [
            Backend::IDENTIFIER
        ];
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param \Heystack\Subsystem\Core\State\State $stateService
     */
    public function setStateService(State $stateService)
    {
        $this->stateService = $stateService;
    }

    /**
     * @return \Heystack\Subsystem\Core\State\State
     */
    public function getStateService()
    {
        return $this->stateService;
    }
}
