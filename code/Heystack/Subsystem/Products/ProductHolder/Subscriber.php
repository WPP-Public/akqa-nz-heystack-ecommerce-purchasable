<?php

namespace Heystack\Subsystem\Products\ProductHolder;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Heystack\Subsystem\Ecommerce\Currency\Events as CurrencyEvents;
use Heystack\Subsystem\Ecommerce\Currency\Event\CurrencyEvent;

use Heystack\Subsystem\Ecommerce\Transaction\Events as TransactionEvents;

use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;

class Subscriber implements EventSubscriberInterface
{
    protected $eventDispatcher;
    protected $purchasableHolder;

    public function __construct(EventDispatcherInterface $eventDispatcher, PurchasableHolderInterface $purchasableHolder)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->purchasableHolder = $purchasableHolder;
    }

    public static function getSubscribedEvents()
    {
        return array(
            Events::SAVE                        => array('onSave', 0),
            Events::ADD_PURCHASABLE             => array('onAdd', 0),
            Events::CHANGE_PURCHASABLE          => array('onChange', 0),
            Events::REMOVE_PURCHASABLE          => array('onRemove', 0),
            CurrencyEvents::CHANGE              => array('onCurrencyChange', 0),
        );
    }

    public function onSave()
    {

        $this->purchasableHolder->saveToDatabase();

    }

    public function onChange(ProductHolderEvent $event)
    {
        $this->purchasableHolder->updateTotal();
        $this->eventDispatcher->dispatch(TransactionEvents::UPDATE);
    }

    public function onRemove(ProductHolderEvent $event)
    {
        $this->purchasableHolder->updateTotal();
        $this->eventDispatcher->dispatch(TransactionEvents::UPDATE);
    }

    public function onAdd(ProductHolderEvent $event)
    {
        $this->purchasableHolder->updateTotal();
        $this->eventDispatcher->dispatch(TransactionEvents::UPDATE);
    }

    public function onCurrencyChange(CurrencyEvent $event)
    {
        $this->purchasableHolder->updatePurchasablePrices();
        $this->purchasableHolder->updateTotal();

        $this->eventDispatcher->dispatch(TransactionEvents::UPDATE);
    }

}
