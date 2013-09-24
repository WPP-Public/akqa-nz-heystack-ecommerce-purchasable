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

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Heystack\Subsystem\Ecommerce\Currency\Events as CurrencyEvents;

use Heystack\Subsystem\Ecommerce\Transaction\Events as TransactionEvents;

use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;

use Heystack\Subsystem\Core\Storage\Storage;
use Heystack\Subsystem\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Subsystem\Core\Storage\Event as StorageEvent;

/**
 * PurchasableHolder Subscriber
 *
 * Handles both subscribing to events and acting on those events for all
 * information which is related to the purchasableholder.
 *
 * @copyright  Heyday
 * @author Stevie Mayhew <stevie@heyday.co.nz>
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @author Cam Spiers <cameron@heyday.co.nz>
 * @package Ecommerce-Purchasable
 *
 */
class Subscriber implements EventSubscriberInterface
{

    /**
     * The Event Dispatcher
     * @var object
     */
    protected $eventDispatcher;

    /**
     * The purchasableholder which this subscriber will act on
     * @var object
     */
    protected $purchasableHolder;

    /**
     * The storage service which will be used in cases where storage is needed.
     * @var object
     */
    protected $storageService;

    /**
     * Construct the subscriber
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface                     $eventDispatcher
     * @param \Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface $purchasableHolder
     * @param \Heystack\Subsystem\Core\Storage\Storage                                        $storageService
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, PurchasableHolderInterface $purchasableHolder, Storage $storageService)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->purchasableHolder = $purchasableHolder;
        $this->storageService = $storageService;
    }

    /**
     * Set up all of the events which will be subscribed to
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            Events::PURCHASABLE_ADDED            => array('onAdd', 100),
            Events::PURCHASABLE_CHANGED          => array('onChange', 100),
            Events::PURCHASABLE_REMOVED          => array('onRemove', 100),
            CurrencyEvents::CHANGED              => array('onCurrencyChange', 0),
            Backend::IDENTIFIER . '.' . TransactionEvents::STORED  => array('onTransactionStored', 0),
            Backend::IDENTIFIER . '.' . Events::STORED  => array('onPurchasableHolderStored', 0)
        );
    }

    /**
     * Updates the total of the holder and lets the transaction know it has to
     * update
     */
    public function onChange()
    {
        $this->purchasableHolder->updateTotal();
        $this->eventDispatcher->dispatch(TransactionEvents::UPDATE);
    }

    /**
     * Updates the total of the holder and lets the transaction know it has to
     * update
     */
    public function onRemove()
    {
        $this->purchasableHolder->updateTotal();
        $this->eventDispatcher->dispatch(TransactionEvents::UPDATE);
    }

    /**
     * Updates the total of the holder and lets the transaction know it has to
     * update
     */
    public function onAdd()
    {
        $this->purchasableHolder->updateTotal();
        $this->eventDispatcher->dispatch(TransactionEvents::UPDATE);
    }

    /**
     * Updates the total of the holder and lets the transaction know it has to
     * update
     */
    public function onCurrencyChange()
    {
        $this->purchasableHolder->updatePurchasablePrices();
        $this->purchasableHolder->updateTotal();

        $this->eventDispatcher->dispatch(TransactionEvents::UPDATE);
    }

    /**
     * Stores the purchasable holder permanently
     */
    public function onTransactionStored(StorageEvent $storageEvent)
    {

        $this->purchasableHolder->setParentReference($storageEvent->getParentReference());

        $this->storageService->process($this->purchasableHolder);

    }

    /**
     * Stores the purchasables which are attached to the purchasable holder
     * @param \Heystack\Subsystem\Core\Storage\Event $storageEvent
     */
    public function onPurchasableHolderStored(StorageEvent $storageEvent)
    {

        $parentReference = $storageEvent->getParentReference();
        $purchasables = $this->purchasableHolder->getPurchasables();

        if ($purchasables) {

            foreach ($purchasables as $purchaseable) {

                $purchaseable->setParentReference($parentReference);

                $this->storageService->process($purchaseable);

            }

        }

    }
}
