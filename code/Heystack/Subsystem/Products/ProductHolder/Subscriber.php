<?php

namespace Heystack\Subsystem\Products\ProductHolder;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Subscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
            Events::PRODUCTHOLDER_CHANGE     => array('onChange', 0),
            Events::PRODUCTHOLDER_SAVE     => array('onSave', 0),
            Events::PRODUCTHOLDER_UPDATE     => array('onUpdate', 0),
        );
    }

    public function onChange()
    {
        
    }
    
    public function onSave()
    {
        
        $productHolder = \Heystack\Subsystem\Core\ServiceStore::getService(ProductHolder::STATE_KEY);
        $productHolder->saveToDatabase();
        
    }
    
    public function onUpdate()
    {
        
    }

}
