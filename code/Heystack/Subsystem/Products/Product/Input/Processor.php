<?php

namespace Heystack\Subsystem\Products\Product\Input;

use Heystack\Subsystem\Core\Input\ProcessorInterface;
use Heystack\Subsystem\Core\State\State;
use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;

use Symfony\Component\EventDispatcher\EventDispatcher;

class Processor implements ProcessorInterface
{

    private $productClass;
    private $state;
    private $eventDispatcher;
    private $purchasableHolder;

    public function __construct($productClass, State $state, EventDispatcher $eventDispatcher, PurchasableHolderInterface $purchasableHolder)
    {

        $this->productClass = $productClass;
        $this->state = $state;
        $this->eventDispatcher = $eventDispatcher;
        $this->purchasableHolder = $purchasableHolder;

    }

    public function getIdentifier()
    {

        return strtolower($this->productClass);

    }

    public function process(\SS_HTTPRequest $request)
    {

        if ($id = $request->param('OtherID')) {

            $product = \DataObject::get_by_id($this->productClass, $request->param('OtherID'));

            $quantity = $request->param('ExtraID') ? $request->param('ExtraID') : 1 ;

            if ($product instanceof $this->productClass) {

                switch ($request->param('ID')) {

                    case 'add':
                        $this->purchasableHolder->addPurchasable($product,$quantity);
                        break;
                    case 'remove':
                        $this->purchasableHolder->removePurchasable($product->getIdentifier());
                        break;
                    case 'set':
                        $this->purchasableHolder->setPurchasable($product,$quantity);
                        break;

                }

                $this->purchasableHolder->saveState();

                return array(
                    'Success' => true
                );

            }

        }

        return array(
            'Success' => false
        );

    }

}
