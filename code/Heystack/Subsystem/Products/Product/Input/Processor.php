<?php

namespace Heystack\Subsystem\Products\Product\Input;

use Heystack\Subsystem\Core\Input\ProcessorInterface;
use Heystack\Subsystem\Core\State\State;
use Heystack\Subsystem\Products\ProductHolder\ProductHolder;

use Symfony\Component\EventDispatcher\EventDispatcher;

class Processor implements ProcessorInterface
{

    private $productClass;
    private $state;
    private $eventDispatcher;
    private $productHolder;

    public function __construct($productClass, State $state, EventDispatcher $eventDispatcher, ProductHolder $productHolder)
    {

        $this->productClass = $productClass;
        $this->state = $state;
        $this->eventDispatcher = $eventDispatcher;
        $this->productHolder = $productHolder;

    }

    public function getIdentifier()
    {

        return strtolower($this->productClass);

    }

    public function process(\SS_HTTPRequest $request)
    {

        if ($id = $request->param('OtherID')) {

            $product = \DataObject::get_by_id($this->productClass, $request->param('OtherID'));

            if ($product instanceof $this->productClass) {

                switch ($request->param('ID')) {

                    case 'add':
                        $this->productHolder->addPurchaseable($product);
                        break;
                    case 'remove':
                        $this->productHolder->removePurchaseable($product->getIdentifier());
                        break;

                }
                
//                \Heystack\Subsystem\Core\ServiceStore::getService('monolog')->addError('Something went wrong!', array(
//                    'Product' => $product
//                ));

                $this->productHolder->saveState();
                
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