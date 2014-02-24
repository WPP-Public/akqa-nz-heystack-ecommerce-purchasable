<?php

namespace Heystack\Purchasable\PurchasableHolder\Traits;

use Heystack\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;

trait HasPurchasableHolderTrait
{
    /**
     * @var \Heystack\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface
     */
    protected $purchasableHolder;

    /**
     * @param \Heystack\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface $purchasableHolder
     */
    public function setPurchasableHolder(PurchasableHolderInterface $purchasableHolder)
    {
        $this->purchasableHolder = $purchasableHolder;
    }

    /**
     * @return \Heystack\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface
     */
    public function getPurchasableHolder()
    {
        return $this->purchasableHolder;
    }
}