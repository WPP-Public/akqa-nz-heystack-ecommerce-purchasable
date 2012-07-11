<?php

namespace Heystack\Subsystem\Products\Product;

use Heystack\Subsystem\Ecommerce\Currency\Events as CurrencyEvents;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Subscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(
           CurrencyEvents::CURRENCY_CHANGE => array('onCurrencyChange', 10),
        );
    }

    public function onCurrencyChange()
    {
//        \HeydayLog::log('Currency did change');
    }

}
