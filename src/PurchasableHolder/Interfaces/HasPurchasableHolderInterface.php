<?php

namespace Heystack\Subsystem\Purchasable\PurchasableHolder\Interfaces;

use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface;

interface HasPurchasableHolderInterface
{
    /**
     * @param \Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface $purchasableHolder
     * @return mixed
     */
    public function setPurchasableHolder(PurchasableHolderInterface $purchasableHolder);

    /**
     * @return \Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableHolderInterface
     */
    public function getPurchasableHolder();
} 