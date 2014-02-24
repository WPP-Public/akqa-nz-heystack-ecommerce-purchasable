<?php

namespace Heystack\Purchasable\PurchasableHolder\Interfaces;

use Heystack\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;

interface HasPurchasableHolderInterface
{
    /**
     * @param \Heystack\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface $purchasableHolder
     * @return mixed
     */
    public function setPurchasableHolder(PurchasableHolderInterface $purchasableHolder);

    /**
     * @return \Heystack\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface
     */
    public function getPurchasableHolder();
} 