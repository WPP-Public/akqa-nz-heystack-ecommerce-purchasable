<?php
/**
 * This file is part of the Ecommerce-Purchasable package
 *
 * @package Ecommerce-Purchasable
 */

/**
 * Purchasable Input namespace
 */
namespace Heystack\Purchasable\Purchasable\Input;

use Heystack\Core\Identifier\Identifier;
use Heystack\Core\Input\ProcessorInterface;
use Heystack\Core\State\State;
use Heystack\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;

use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * Process input for the purchasable system.
 *
 * This processor takes care of all interactions which involve input for the
 * purchasable system.
 *
 * @copyright  Heyday
 * @author Stevie Mayhew <stevie@heyday.co.nz>
 * @author Cameron Spiers <cam@heyday.co.nz>
 * @author Glenn Bautista <glenn@heyday.co.nz>
 * @package Ecommerce-Purchasable
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
    protected $purchasableClass;

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
     * @param $purchasableClass
     * @param State $state
     * @param EventDispatcher $eventDispatcher
     * @param PurchasableHolderInterface $purchasableHolder
     */
    public function __construct(
        $purchasableClass,
        State $state,
        EventDispatcher $eventDispatcher,
        PurchasableHolderInterface $purchasableHolder
    ) {

        $this->purchasableClass = $purchasableClass;
        $this->state = $state;
        $this->eventDispatcher = $eventDispatcher;
        $this->purchasableHolder = $purchasableHolder;
    }

    /**
     * Get the identifier for this processor
     * @return \Heystack\Core\Identifier\Identifier
     */
    public function getIdentifier()
    {
        return new Identifier(
            strtolower($this->purchasableClass)
        );
    }

    /**
     * Process input requests which are relevant to purchasables
     *
     * @param  \SS_HTTPRequest $request
     * @return array           Success/Failure
     */
    public function process(\SS_HTTPRequest $request)
    {
        if ($id = $request->param('OtherID')) {

            $purchasable = \DataList::create($this->purchasableClass)->byID($request->param('OtherID'));

            $quantity = $request->param('ExtraID') ? $request->param('ExtraID') : 1;

            if ($purchasable instanceof $this->purchasableClass) {

                switch ($request->param('ID')) {

                    case 'add':
                        $this->purchasableHolder->addPurchasable($purchasable, $quantity);
                        break;
                    case 'remove':
                        $this->purchasableHolder->removePurchasable($purchasable->getIdentifier());
                        break;
                    case 'set':
                        $this->purchasableHolder->setPurchasable($purchasable, $quantity);
                        break;

                }

                $this->purchasableHolder->saveState();

                return [
                    'Success' => true
                ];

            }

        }

        return [
            'Success' => false
        ];
    }
}
