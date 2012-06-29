<?php

namespace Heystack\Subsystem\Products\Product;

use Heystack\Subsystem\Currency\Events as CurrencyEvents;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class Subscriber implements EventSubscriberInterface
{
    
    static public function getSubscribedEvents()
    {
        return array(
           CurrencyEvents::CURRENCY_CHANGE => array('onCurrencyChange', 10)
        );
    }
    
    public function onCurrencyChange()
    {
        \HeydayLog::log('Currency did change');
    }

}