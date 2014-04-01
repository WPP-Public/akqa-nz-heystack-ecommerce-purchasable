<?php
/**
 * This file is part of the Ecommerce-Purchasable package
 *
 * @package Ecommerce-Purchasable
 */

/**
 * PurchasableHolder namespace
 */
namespace Heystack\Purchasable\PurchasableHolder;

use Heystack\Core\State\State;
use Heystack\Core\Traits\HasEventServiceTrait;
use Heystack\Core\Traits\HasStateServiceTrait;
use Heystack\Purchasable\PurchasableHolder\Traits\HasPurchasableHolderTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Heystack\Ecommerce\Currency\Events as CurrencyEvents;

use Heystack\Ecommerce\Transaction\Events as TransactionEvents;

use Heystack\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;

use Heystack\Core\Storage\Storage;
use Heystack\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Core\Storage\Event as StorageEvent;

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
    use HasEventServiceTrait;
    use HasStateServiceTrait;
    use HasPurchasableHolderTrait;
    
    /**
     * The storage service which will be used in cases where storage is needed.
     * @var object
     */
    protected $storageService;

    /**
     * @var
     */
    protected $currencyChanging;

    /**
     * Construct the subscriber
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventService
     * @param \Heystack\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface $purchasableHolder
     * @param \Heystack\Core\Storage\Storage $storageService
     * @param \Heystack\Core\State\State $stateService
     */
    public function __construct(
        EventDispatcherInterface $eventService,
        PurchasableHolderInterface $purchasableHolder,
        Storage $storageService,
        State $stateService
    )
    {
        $this->eventService = $eventService;
        $this->purchasableHolder = $purchasableHolder;
        $this->storageService = $storageService;
        $this->stateService = $stateService;
    }

    /**
     * Set up all of the events which will be subscribed to
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::UPDATED                                                  => ['onTotalUpdated', 0],
            CurrencyEvents::CHANGED                                          => ['onCurrencyChanged', 0],
            sprintf('%s.%s', Backend::IDENTIFIER, TransactionEvents::STORED) => ['onTransactionStored', 0],
            sprintf('%s.%s', Backend::IDENTIFIER, Events::STORED)            => ['onPurchasableHolderStored', 0]
        ];
    }

    /**
     * Updates the total of the holder and lets the transaction know it has to
     * update
     */
    public function onTotalUpdated()
    {
        if (!$this->currencyChanging) {
            $this->eventService->dispatch(TransactionEvents::UPDATE);
        }
    }

    /**
     * Updates the total of the holder and lets the transaction know it has to
     * update
     */
    public function onCurrencyChanged()
    {
        $this->currencyChanging = true;
        $this->purchasableHolder->updatePurchasablePrices();
        $this->purchasableHolder->updateTotal();
        $this->currencyChanging = false;
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
     * @param \Heystack\Core\Storage\Event $storageEvent
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
        
        $this->stateService->removeByKey(PurchasableHolder::IDENTIFIER);
    }
}
