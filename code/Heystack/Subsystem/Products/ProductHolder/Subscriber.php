<?php

namespace Heystack\Subsystem\Products\ProductHolder;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Heystack\Subsystem\Ecommerce\Currency\Events as CurrencyEvents;

use Heystack\Subsystem\Ecommerce\Transaction\Events as TransactionEvents;

use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;

use Heystack\Subsystem\Core\Storage\Storage;
use Heystack\Subsystem\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Subsystem\Core\Storage\Event as StorageEvent;

class Subscriber implements EventSubscriberInterface
{
    protected $eventDispatcher;
    protected $purchasableHolder;
    protected $storageService;

    public function __construct(EventDispatcherInterface $eventDispatcher, PurchasableHolderInterface $purchasableHolder, Storage $storageService)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->purchasableHolder = $purchasableHolder;
        $this->storageService = $storageService;
    }

    public static function getSubscribedEvents()
    {
        return array(
            Events::PURCHASABLE_ADDED            => array('onAdd', 0),
            Events::PURCHASABLE_CHANGED          => array('onChange', 0),
            Events::PURCHASABLE_REMOVED          => array('onRemove', 0),
            CurrencyEvents::CHANGED              => array('onCurrencyChange', 0),
            Backend::IDENTIFIER . '.' . TransactionEvents::STORED  => array('onTransactionStored', 0),
            Backend::IDENTIFIER . '.' . Events::STORED  => array('onProductHolderStored', 0)			
        );
    }

    public function onChange()
    {
        $this->purchasableHolder->updateTotal();
        $this->eventDispatcher->dispatch(TransactionEvents::UPDATE);
    }

    public function onRemove()
    {
        $this->purchasableHolder->updateTotal();
        $this->eventDispatcher->dispatch(TransactionEvents::UPDATE);
    }

    public function onAdd()
    {
        $this->purchasableHolder->updateTotal();
        $this->eventDispatcher->dispatch(TransactionEvents::UPDATE);
    }

    public function onCurrencyChange()
    {
        $this->purchasableHolder->updatePurchasablePrices();
        $this->purchasableHolder->updateTotal();

        $this->eventDispatcher->dispatch(TransactionEvents::UPDATE);
    }
    
    public function onTransactionStored(StorageEvent $storageEvent) 
    {
		
        $this->purchasableHolder->setParentID($storageEvent->getParentReference());

        $this->storageService->process($this->purchasableHolder);
        
    }
    
    public function onProductHolderStored(StorageEvent $storageEvent) 
    {
		
		$parentID = $storageEvent->getParentReference();
		$purchasables = $this->purchasableHolder->getPurchasables();
        
        if ($purchasables) {
            
            foreach ($purchasables as $purchaseable) {
				
				$purchaseable->setParentID($parentID);

                $this->storageService->process($purchaseable);

            }
            
        }
        
    }

}
