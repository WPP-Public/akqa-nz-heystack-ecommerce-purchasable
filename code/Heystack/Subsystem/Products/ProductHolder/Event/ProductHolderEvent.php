<?php

namespace Heystack\Subsystem\Products\ProductHolder\Event;

use \Symfony\Component\EventDispatcher\Event;
use \Heystack\Subsystem\Products\ProductHolder\ProductHolder;
use \Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableInterface;

class ProductHolderEvent extends Event
{
    public $product;
    public $productHolder;
    
    public function __construct(ProductHolder $productHolder, PurchasableInterface $product)
    {
        $this->productHolder = $productHolder;
        $this->product = $product;
    }
}
