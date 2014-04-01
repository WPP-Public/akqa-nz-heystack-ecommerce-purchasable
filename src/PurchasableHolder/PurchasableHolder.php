<?php

namespace Heystack\Purchasable\PurchasableHolder;

use Heystack\Core\Identifier\Identifier;
use Heystack\Core\Identifier\IdentifierInterface;
use Heystack\Core\Interfaces\HasEventServiceInterface;
use Heystack\Core\Interfaces\HasStateServiceInterface;
use Heystack\Core\State\State;
use Heystack\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Core\Storage\Traits\ParentReferenceTrait;
use Heystack\Core\Traits\HasEventServiceTrait;
use Heystack\Core\Traits\HasStateServiceTrait;
use Heystack\Ecommerce\Currency\Interfaces\CurrencyServiceInterface;
use Heystack\Ecommerce\Currency\Interfaces\HasCurrencyServiceInterface;
use Heystack\Ecommerce\Currency\Traits\HasCurrencyServiceTrait;
use Heystack\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;
use Heystack\Ecommerce\Purchasable\Interfaces\PurchasableInterface;
use Heystack\Ecommerce\Transaction\Events as TransactionEvents;
use Heystack\Ecommerce\Transaction\Traits\TransactionModifierSerializeTrait;
use Heystack\Ecommerce\Transaction\Traits\TransactionModifierStateTrait;
use Heystack\Ecommerce\Transaction\TransactionModifierTypes;
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
    HasEventServiceInterface,
    HasStateServiceInterface,
    HasCurrencyServiceInterface
{
    use TransactionModifierSerializeTrait;
    use ParentReferenceTrait;
    use HasEventServiceTrait;
    use HasStateServiceTrait;
    use HasCurrencyServiceTrait;

    /**
     * State Key constant
     */
    const IDENTIFIER = 'purchasableholder';

    /**
     * @var \Heystack\Ecommerce\Purchasable\Interfaces\PurchasableInterface[]
     */
    protected $purchasables = [];

    /**
     * @var \SebastianBergmann\Money\Money
     */
    protected $total;

    /**
     * PurchasableHolder Constructor.
     *
     * @param \Heystack\Core\State\State $stateService
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventService
     * @param \Heystack\Ecommerce\Currency\Interfaces\CurrencyServiceInterface $currencyService
     */
    public function __construct(
        State $stateService,
        EventDispatcherInterface $eventService,
        CurrencyServiceInterface $currencyService
    )
    {
        $this->stateService = $stateService;
        $this->eventService = $eventService;
        $this->currencyService = $currencyService;
        $this->total = $this->currencyService->getZeroMoney();
    }

    /**
     * @return \Heystack\Core\Identifier\IdentifierInterface
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
     * @param int $quantity quantity of the object to add
     * @throws \InvalidArgumentException
     */
    public function addPurchasable(PurchasableInterface $purchasable, $quantity = 1)
    {
        $identifier = $purchasable->getIdentifier();
        if ($this->hasPurchasable($identifier)) {
            $purchasable = $this->getPurchasable($identifier);
            $this->changePurchasableQuantity($purchasable, $purchasable->getQuantity() + $quantity);
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
        $this->assertValidQuantity($quantity);
        $identifier = $purchasable->getIdentifier();
        
        // If already in the holder
        if ($this->hasPurchasable($identifier)) {
            $this->changePurchasableQuantity($this->getPurchasable($identifier), $quantity);
        } else {
            $purchasable->addStateService($this->stateService);
            $purchasable->addEventService($this->eventService);

            $purchasable->setQuantity($quantity);
            $purchasable->setUnitPrice($purchasable->getPrice());

            $this->purchasables[$identifier->getFull()] = $purchasable;

            $this->updateTotal();
            $this->eventService->dispatch(Events::PURCHASABLE_ADDED);
        }
    }

    /**
     * Removes a purchasable from the Purchasable holder if found
     * @param  \Heystack\Core\Identifier\IdentifierInterface $identifier The identifier of the purchasable to remove
     * @return void
     */
    public function removePurchasable(IdentifierInterface $identifier)
    {
        $fullIdentifier = $identifier->getFull();

        if (isset($this->purchasables[$fullIdentifier])) {

            unset($this->purchasables[$fullIdentifier]);

            $this->updateTotal();
            $this->eventService->dispatch(Events::PURCHASABLE_REMOVED);
        }
    }

    /**
     * Change a purchasble on the holder
     * @param $purchasable
     * @param int $quantity
     */
    protected function changePurchasableQuantity(PurchasableInterface $purchasable, $quantity)
    {
        if ($purchasable->getQuantity() !== $quantity) {
            $purchasable->setQuantity($quantity);
            $this->updateTotal();
            $this->eventService->dispatch(Events::PURCHASABLE_CHANGED);
        }
    }

    /**
     * Returns a purchasable by its identifier
     * @param  \Heystack\Core\Identifier\IdentifierInterface $identifier The identifier of the purchasable
     * @return PurchasableInterface|false The Purchasable object if found
     */
    public function getPurchasable(IdentifierInterface $identifier)
    {
        return $this->hasPurchasable($identifier) ? $this->purchasables[$identifier->getFull()] : false;
    }

    /**
     * Returns whether or not the purchable is on the holder
     * @param IdentifierInterface $identifier
     * @return bool
     */
    public function hasPurchasable(IdentifierInterface $identifier)
    {
        return isset($this->purchasables[$identifier->getFull()]);
    }

    /**
     * @param IdentifierInterface $identifier
     * @return bool|\Heystack\Ecommerce\Purchasable\Interfaces\PurchasableInterface[]
     */
    public function getPurchasablesByPrimaryIdentifier(IdentifierInterface $identifier)
    {
        $matches = [];

        foreach ($this->purchasables as $purchasable) {
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
     * Removes all purchasables from the service
     * @return void
     */
    public function removePurchasables()
    {
        $this->purchasables = [];
    }

    /**
     * Get multiple purchasables, if no identifiers are passed in then return all purchasables
     * @param  array|null $identifiers An array of identifiers if passed in
     * @return \Heystack\Ecommerce\Purchasable\Interfaces\PurchasableInterface[]  An array of purchasables
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

            $purchasables = $this->purchasables;

        }

        return $purchasables;
    }

    /**
     * Set an array of purchasables on the purchasable holder
     * @param \Heystack\Ecommerce\Purchasable\Interfaces\PurchasableInterface[] $purchasables Array of purchasables
     */
    public function setPurchasables(array $purchasables)
    {
        foreach ($purchasables as $purchasable) {
            $this->addPurchasable($purchasable);
        }
    }

    /**
     * Get the current purchasable total on the purchasable holder
     * @return \SebastianBergmann\Money\Money
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Update the purchasable total on the purchasable holder
     * @throws \SebastianBergmann\Money\OverflowException
     */
    public function updateTotal($saveState = true)
    {
        $this->total = $this->currencyService->getZeroMoney();
        
        if ($this->purchasables) {
            foreach ($this->purchasables as $purchasable) {
                $this->total = $this->total->add($purchasable->getTotal());
            }
            
            $this->eventService->dispatch(Events::UPDATED);
        }

        if ($saveState) {
            $this->saveState();
        }
    }

    /**
     * Update the purchasable prices on the holder
     */
    public function updatePurchasablePrices()
    {
        foreach ($this->purchasables as $purchasable) {
            $purchasable->setUnitPrice($purchasable->getPrice());
        }

        $this->saveState();
    }

    /**
     * Get the data to store for the purchasableholder
     * @return array
     */
    public function getStorableData()
    {
        return [
            'id' => 'PurchasableHolder',
            'flat' => [
                'Total' => $this->total->getAmount() / $this->total->getCurrency()->getSubUnit(),
                'NoOfItems' => count($this->purchasables),
                'ParentID' => $this->parentReference
            ],
            'parent' => true
        ];
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
     * @return mixed
     */
    public function saveState()
    {
        $this->stateService->setByKey(self::IDENTIFIER, $this->getData());
    }

    /**
     * @return mixed
     */
    public function restoreState()
    {
        if ($data = $this->stateService->getByKey(self::IDENTIFIER)) {
            $this->setData($data);
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [$this->total, $this->purchasables];
    }

    /**
     * @param array $data
     */
    public function setData($data)
    {
        if (is_array($data)) {
            list($this->total, $this->purchasables) = $data;
        }
    }

    /**
     * @param $quantity
     * @throws \InvalidArgumentException
     */
    protected function assertValidQuantity($quantity)
    {
        if (!is_int($quantity)) {
            throw new \InvalidArgumentException("Quantity must be an integer");
        }
        if ($quantity < 1) {
            throw new \InvalidArgumentException("Quantity must be positive");
        }
    }
}
