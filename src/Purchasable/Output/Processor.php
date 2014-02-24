<?php
/**
 * This file is part of the Ecommerce-Purchasable package
 *
 * @package Ecommerce-Purchasable
 */

/**
 * Purchasable Output namespace
 */
namespace Heystack\Subsystem\Purchasable\Purchasable\Output;

use Heystack\Subsystem\Core\Identifier\Identifier;
use Heystack\Subsystem\Core\Output\ProcessorInterface;
use Heystack\Subsystem\Core\State\State;
use Heystack\Subsystem\Purchasable\PurchasableHolder\PurchasableHolder;

/**
 * Purchasable output processor
 *
 * Determines what to do with the results from the Purchasable Input Processor.
 *
 * @copyright  Heyday
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Vouchers
 *
 */
class Processor implements ProcessorInterface
{
    /**
     * The class name of the Purchasable object
     * @var string
     */
    private $purchasableClass;

    /**
     * @var \Heystack\Subsystem\Core\State\State
     */
    private $state;

    /**
     * @var \Heystack\Subsystem\Purchasable\PurchasableHolder\PurchasableHolder
     */
    private $purchasableHolder;

    /**
     * Creates the Purchasable output processor
     *
     * @param string $purchasableClass
     * @param \Heystack\Subsystem\Core\State\State $state
     * @param \Heystack\Subsystem\Purchasable\PurchasableHolder\PurchasableHolder $purchasableHolder
     */
    public function __construct($purchasableClass, State $state, PurchasableHolder $purchasableHolder)
    {
        $this->purchasableClass = $purchasableClass;
        $this->state = $state;
        $this->purchasableHolder = $purchasableHolder;
    }

    /**
     * Get the identifier for this processor
     * @return \Heystack\Subsystem\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return new Identifier(strtolower($this->purchasableClass));
    }

    /**
     * Determines what to do with the result from the input processor
     * @param  \Controller $controller
     * @param  type $result
     * @return mixed
     */
    public function process(\Controller $controller, $result = null)
    {
        if ($controller->getRequest()->isAjax()) {

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
