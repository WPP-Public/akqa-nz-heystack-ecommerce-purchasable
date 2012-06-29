<?php

namespace Heystack\Subsystem\Products\Product;

use Heystack\Subsystem\Core\Processor\ProcessorInterface;
use Heystack\Subsystem\Core\State\State;

use Symfony\Component\EventDispatcher\EventDispatcher;

class Processor implements ProcessorInterface
{

    private $state;
    private $eventDispatcher;

    public function __construct(State $state, EventDispatcher $eventDispatcher)
    {

        $this->state = $state;
        $this->eventDispatcher = $eventDispatcher;

    }

    public function getName()
    {
        return 'product';

    }

    public function process(\SS_HTTPRequest $request)
    {

    }

}
