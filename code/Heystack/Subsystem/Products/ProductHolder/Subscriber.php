<?php

namespace Heystack\Subsystem\Products\ProductHolder;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Subscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
            Events::PRODUCTHOLDER_SAVE     => array('onSave', 0),
            Events::PRODUCTHOLDER_ADD_PURCHASABLE     => array('onAdd', 0),
            Events::PRODUCTHOLDER_CHANGE_PURCHASABLE     => array('onChange', 0),
            Events::PRODUCTHOLDER_REMOVE_PURCHASABLE     => array('onRemove', 0),
        );
    }
    
    public function onSave()
    {
        
        $productHolder = \Heystack\Subsystem\Core\ServiceStore::getService(ProductHolder::STATE_KEY);
        $productHolder->saveToDatabase();
        
    }
    
    public function onChange(ProductHolderEvent $event)
    {
        error_log('Change Event on ProductID: ' . $event->product->ID);
    }
    
    public function onRemove(ProductHolderEvent $event)
    {
        error_log('Remove Event on ProductID: ' . $event->product->ID);
    }
    
    public function onAdd(ProductHolderEvent $event)
    {
        error_log('Add Event on ProductID: ' . $event->product->ID);
    }

}
