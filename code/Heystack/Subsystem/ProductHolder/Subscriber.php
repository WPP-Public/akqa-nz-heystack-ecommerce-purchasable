<?php

namespace Heystack\Subsystem\ProductHolder;

use Heystack\Subsystem\Currency\Events as CurrencyEvents;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class Subscriber implements EventSubscriberInterface
{
    
    static public function getSubscribedEvents()
    {
        return array(
           
        );
    }
    
    public function onCurrencyChange()
    {
        
    }

}