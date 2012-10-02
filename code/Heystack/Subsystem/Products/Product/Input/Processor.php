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
     * Construct the processor
     *
     * @param string                                                                          $productClass
     * @param \Heystack\Subsystem\Core\State\State                                            $state
     * @param \Symfony\Component\EventDispatcher\EventDispatcher                              $eventDispatcher
     * @param \Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface $purchasableHolder
     */
    public function __construct($productClass, State $state, EventDispatcher $eventDispatcher, PurchasableHolderInterface $purchasableHolder)
    {

        $this->productClass = $productClass;
        $this->state = $state;
        $this->eventDispatcher = $eventDispatcher;
        $this->purchasableHolder = $purchasableHolder;

    }

    /**
     * Get the identifier for this processor
     *
     * @return string Identifier
     */
    public function getIdentifier()
    {

        return strtolower($this->productClass);

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
