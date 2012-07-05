<?php

namespace Heystack\Subsystem\Products\Product\Output;

use Heystack\Subsystem\Core\Output\ProcessorInterface;
use Heystack\Subsystem\Core\State\State;
use Heystack\Subsystem\Products\ProductHolder\ProductHolder;

class Processor implements ProcessorInterface
{

    private $productClass;
    private $state;
    private $productHolder;

    public function __construct($productClass, State $state, ProductHolder $productHolder)
    {

        $this->productClass = $productClass;
        $this->state = $state;
        $this->productHolder = $productHolder;

    }

    public function getIdentifier()
    {

        return strtolower($this->productClass);

    }

    public function process(\Controller $controller, $result = null)
    {
        if ($controller->isAjax()) {

            $response = $controller->getResponse();
            $response->setStatusCode(200);
            $response->addHeader('Content-Type', 'application/json');

            $response->setBody(json_encode($result));

            return $response;
        } else {
            $controller->redirectBack();
        }

        return null;
    }

}
