<?php

namespace Heystack\Subsystem\Products\ProductHolder;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Subscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return array(

        );
    }

    public function onCurrencyChange()
    {

    }

}
