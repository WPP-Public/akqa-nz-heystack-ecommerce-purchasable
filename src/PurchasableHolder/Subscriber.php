<?php

namespace Heystack\Purchasable\PurchasableHolder;

use Heystack\Core\EventDispatcher;
use Heystack\Core\State\State;
use Heystack\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Core\Storage\Event as StorageEvent;
use Heystack\Core\Storage\Storage;
use Heystack\Core\Traits\HasEventServiceTrait;
use Heystack\Core\Traits\HasStateServiceTrait;
use Heystack\Ecommerce\Currency\Event\CurrencyEvent;
use Heystack\Ecommerce\Currency\Events as CurrencyEvents;
use Heystack\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;
use Heystack\Ecommerce\Transaction\Events as TransactionEvents;
use Heystack\Purchasable\PurchasableHolder\Traits\HasPurchasableHolderTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;

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
     * @param \Heystack\Core\EventDispatcher $eventService
     * @param \Heystack\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface $purchasableHolder
     * @param \Heystack\Core\Storage\Storage $storageService
     * @param \Heystack\Core\State\State $stateService
     */
    public function __construct(
        EventDispatcher $eventService,
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
            CurrencyEvents::CHANGED                                          => ['onCurrencyChanged', 100],
            sprintf('%s.%s', Backend::IDENTIFIER, TransactionEvents::STORED) => ['onTransactionStored', 0],
            sprintf('%s.%s', Backend::IDENTIFIER, Events::STORED)            => ['onPurchasableHolderStored', 0]
        ];
    }

    /**
     * Updates the total of the holder and lets the transaction know it has to
     * update
     * @param \Symfony\Component\EventDispatcher\Event $event
     * @param string $eventName
     * @param \Heystack\Core\EventDispatcher $dispatcher
     * @return void
     */
    public function onTotalUpdated(Event $event, $eventName, EventDispatcher $dispatcher)
    {
        if (!$this->currencyChanging) {
            $this->eventService->dispatch(TransactionEvents::UPDATE);
        }
    }

    /**
     * Updates the total of the holder and lets the transaction know it has to
     * update
     * @param \Heystack\Ecommerce\Currency\Event\CurrencyEvent $currencyEvent
     * @param string $eventName
     * @param \Heystack\Core\EventDispatcher $dispatcher
     * @return void
     */
    public function onCurrencyChanged(CurrencyEvent $currencyEvent, $eventName, EventDispatcher $dispatcher)
    {
        $this->currencyChanging = true;
        $this->purchasableHolder->updatePurchasablePrices();
        $this->purchasableHolder->updateTotal();
        $this->currencyChanging = false;
    }

    /**
     * Stores the purchasable holder permanently
     * @param \Heystack\Core\Storage\Event $storageEvent
     * @param string $eventName
     * @param \Heystack\Core\EventDispatcher $dispatcher
     * @return void
     */
    public function onTransactionStored(StorageEvent $storageEvent, $eventName, EventDispatcher $dispatcher)
    {
        $this->purchasableHolder->setParentReference($storageEvent->getParentReference());
        $this->storageService->process($this->purchasableHolder);
    }

    /**
     * Stores the purchasables which are attached to the purchasable holder
     * @param \Heystack\Core\Storage\Event $storageEvent
     * @param string $eventName
     * @param \Heystack\Core\EventDispatcher $dispatcher
     * @return void
     */
    public function onPurchasableHolderStored(StorageEvent $storageEvent, $eventName, EventDispatcher $dispatcher)
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
