<?php

namespace Heystack\Subsystem\Products\ProductHolder;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

use Heystack\Subsystem\Ecommerce\Currency\Events as CurrencyEvents;
use Heystack\Subsystem\Ecommerce\Currency\Event\CurrencyEvent;

use \Heystack\Subsystem\Core\ServiceStore;

class Subscriber implements EventSubscriberInterface
{
    protected $eventDispatcher;
    
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public static function getSubscribedEvents()
    {
        return array(
            Events::PRODUCTHOLDER_SAVE     => array('onSave', 0),
            Events::PRODUCTHOLDER_ADD_PURCHASABLE     => array('onAdd', 0),
            Events::PRODUCTHOLDER_CHANGE_PURCHASABLE     => array('onChange', 0),
            Events::PRODUCTHOLDER_REMOVE_PURCHASABLE     => array('onRemove', 0),
            CurrencyEvents::CURRENCY_CHANGE     => array('onCurrencyChange', 0),
        );
    }

    public function onSave()
    {

        $productHolder = \Heystack\Subsystem\Core\ServiceStore::getService(ProductHolder::STATE_KEY);
        $productHolder->saveToDatabase();

    }

    public function onChange(ProductHolderEvent $event)
    {
        $this->eventDispatcher->dispatch(Events::PRODUCTHOLDER_UPDATED);
    }

    public function onRemove(ProductHolderEvent $event)
    {
        $this->eventDispatcher->dispatch(Events::PRODUCTHOLDER_UPDATED);
    }

    public function onAdd(ProductHolderEvent $event)
    {
        $this->eventDispatcher->dispatch(Events::PRODUCTHOLDER_UPDATED);
    }

    public function onCurrencyChange(CurrencyEvent $event)
    {        
        $productHolder = ServiceStore::getService(ProductHolder::STATE_KEY);
        $productHolder->updatePurchasablePrices();
        
        $this->eventDispatcher->dispatch(Events::PRODUCTHOLDER_UPDATED);
    }

}
