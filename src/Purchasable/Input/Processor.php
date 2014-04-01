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
use Heystack\Core\Traits\HasEventServiceTrait;
use Heystack\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;
use Heystack\Ecommerce\Purchasable\Interfaces\PurchasableInterface;
use Heystack\Ecommerce\Transaction\Events as TransactionEvents;
use SebastianBergmann\Money\OverflowException;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

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
    use HasEventServiceTrait;
    /**
     * The class this processor handles
     *
     * @var string The ClassName of the object which is to be processed
     */
    protected $purchasableClass;

    /**
     * The state interface for Heystack
     *
     * @var \Heystack\Core\State\State
     */
    protected $state;

    /**
     * PurchasableHolderInterface
     *
     * @var \Heystack\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface
     */
    protected $purchasableHolder;

    /**
     * Construct the processor
     *
     * @param $purchasableClass
     * @param \Heystack\Core\State\State $state
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventService
     * @param \Heystack\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface $purchasableHolder
     */
    public function __construct(
        $purchasableClass,
        State $state,
        EventDispatcherInterface $eventService,
        PurchasableHolderInterface $purchasableHolder
    ) {

        $this->purchasableClass = $purchasableClass;
        $this->state = $state;
        $this->eventService = $eventService;
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
        if (!in_array($request->httpMethod(), ['POST', 'PUT'])) {
            return $this->failed('Invalid method');
        }
        
        if (!$id = $request->param('OtherID')) {
            return $this->failed('No id provided');
        }

        /** @var PurchasableInterface $purchasable */
        $purchasable = $this->getPurchasable($request);

        if (!$purchasable instanceof $this->purchasableClass) {
            return $this->failed("Product doesn't exist");
        }
        
        $action = $request->param('ID');

        try {
            if ($action === 'add') {
                $quantity = max(1, (int) $request->postVar('quantity'));
                $this->purchasableHolder->addPurchasable($purchasable, $quantity);
            } elseif ($action === 'set') {
                $quantity = max(0, (int) $request->postVar('quantity'));
                if ($quantity === 0) {
                    $this->purchasableHolder->removePurchasable($purchasable->getIdentifier());
                } else {
                    $this->purchasableHolder->setPurchasable($purchasable, $quantity);
                }
            } elseif ($action === 'remove') {
                $this->purchasableHolder->removePurchasable($purchasable->getIdentifier());
            } else {
                return $this->failed('Action not allowed');
            }
            
        } catch (OverflowException $e) {
            return $this->failed('Cart bounds exceeded');
        }

        return [
            'Success' => true
        ];
    }

    /**
     * @param $message
     * @return array
     */
    protected function failed($message)
    {
        return [
            'Success' => false,
            'Message' => $message
        ];
    }

    /**
     * @param \SS_HTTPRequest $request
     * @return PurchasableInterface
     */
    protected function getPurchasable(\SS_HTTPRequest $request)
    {
        return \DataList::create($this->purchasableClass)->byID($request->param('OtherID'));
    }
}
