<?php
/**
 * This file is part of the Ecommerce-Products package
 *
 * @package Ecommerce-Products
 */

/**
 * Product Input namespace
 */
namespace Heystack\Subsystem\Products\Product\Input;

use Heystack\Subsystem\Core\DataObjectHandler\DataObjectHandlerInterface;
use Heystack\Subsystem\Core\Identifier\Identifier;
use Heystack\Subsystem\Core\Input\ProcessorInterface;
use Heystack\Subsystem\Core\State\State;
use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;

use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Process input for the product system.
 *
 * This processor takes care of all interactions which involve input for the
 * product system.
 *
 * @copyright  Heyday
 * @author Stevie Mayhew <stevie@heyday.co.nz>
 * @author Cameron Spiers <cam@heyday.co.nz>
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Products
 * @see Symfony\Component\EventDispatcher
 *
 */
class Processor implements ProcessorInterface
{
    /**
     * The class this processor handles
     *
     * @var string The ClassName of the object which is to be processed
     */
    protected $productClass;

    /**
     * The state interface for Heystack
     *
     * @uses State
     * @var object State
     */
    protected $state;

    /**
     * The event dispatcher for Heystack
     * @see EventDispatcher
     *
     * @var object
     */
    protected $eventDispatcher;

    /**
     * PurchasableHolderInterface
     *
     * @var Interface
     */
    protected $purchasableHolder;

    /**
     * @var \Heystack\Subsystem\Core\DataObjectHandler\DataObjectHandlerInterface
     */
    protected $dataObjectHandler;

    /**
     * Construct the processor
     *
     * @param $productClass
     * @param State $state
     * @param EventDispatcher $eventDispatcher
     * @param PurchasableHolderInterface $purchasableHolder
     * @param DataObjectHandlerInterface $dataObjectHandler
     */
    public function __construct(
        $productClass,
        State $state,
        EventDispatcher $eventDispatcher,
        PurchasableHolderInterface $purchasableHolder,
        DataObjectHandlerInterface $dataObjectHandler
    ) {

        $this->productClass = $productClass;
        $this->state = $state;
        $this->eventDispatcher = $eventDispatcher;
        $this->purchasableHolder = $purchasableHolder;
        $this->dataObjectHandler = $dataObjectHandler;
    }

    /**
     * Get the identifier for this processor
     * @return \Heystack\Subsystem\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return new Identifier(
            strtolower($this->productClass)
        );
    }

    /**
     * Process input requests which are relevant to products
     *
     * @param  \SS_HTTPRequest $request
     * @return array           Success/Failure
     */
    public function process(\SS_HTTPRequest $request)
    {

        if ($id = $request->param('OtherID')) {

            $product = $this->dataObjectHandler->getDataObjectById($this->productClass, $request->param('OtherID'));

            $quantity = $request->param('ExtraID') ? $request->param('ExtraID') : 1 ;

            if ($product instanceof $this->productClass) {

                switch ($request->param('ID')) {

                    case 'add':
                        $this->purchasableHolder->addPurchasable($product,$quantity);
                        break;
                    case 'remove':
                        $this->purchasableHolder->removePurchasable($product->getIdentifier()->getFull());
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
