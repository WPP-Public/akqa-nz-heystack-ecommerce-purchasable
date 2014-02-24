<?php

namespace Heystack\Subsystem\Purchasable\PurchasableHolder\Traits;

use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;

trait HasPurchasableHolderTrait
{
    /**
     * @var \Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface
     */
    protected $purchasableHolder;

    /**
     * @param \Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface $purchasableHolder
     */
    public function setPurchasableHolder(PurchasableHolderInterface $purchasableHolder)
    {
        $this->purchasableHolder = $purchasableHolder;
    }

    /**
     * @return \Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface
     */
    public function getPurchasableHolder()
    {
        return $this->purchasableHolder;
    }
}