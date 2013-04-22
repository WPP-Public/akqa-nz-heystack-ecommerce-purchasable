<?php
/**
 * This file is part of the Ecommerce-Products package
 *
 * @package Ecommerce-Products
 */

/**
 * Product Output namespace
 */
namespace Heystack\Subsystem\Products\Product\Output;

use Heystack\Subsystem\Core\Identifier\Identifier;
use Heystack\Subsystem\Core\Output\ProcessorInterface;
use Heystack\Subsystem\Core\State\State;
use Heystack\Subsystem\Products\ProductHolder\ProductHolder;

/**
 * Product output processor
 *
 * Determines what to do with the results from the Product Input Processor.
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Vouchers
 *
 */
class Processor implements ProcessorInterface
{
    /**
     * The class name of the Product object
     * @var string
     */
    private $productClass;

    /**
     * The application state
     * @var object
     */
    private $state;

    /**
     * This application productholder
     * @var object
     */
    private $productHolder;

    /**
     * Creates the Product input processor
     *
     * @param string                                                   $productClass
     * @param \Heystack\Subsystem\Core\State\State                     $state
     * @param \Heystack\Subsystem\Products\ProductHolder\ProductHolder $productHolder
     */
    public function __construct($productClass, State $state, ProductHolder $productHolder)
    {
        $this->productClass = $productClass;
        $this->state = $state;
        $this->productHolder = $productHolder;
    }

    /**
     * Get the identifier for this processor
     * @return \Heystack\Subsystem\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return new Identifier(strtolower($this->productClass));
    }

    /**
     * Determines what to do with the result from the input processor
     * @param  \Controller $controller
     * @param  type        $result
     * @return mixed
     */
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
