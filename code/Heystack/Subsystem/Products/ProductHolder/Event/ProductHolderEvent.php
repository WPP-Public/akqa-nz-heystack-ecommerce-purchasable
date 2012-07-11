<?php

namespace Heystack\Subsystem\Products\ProductHolder\Event;

use \Symfony\Component\EventDispatcher\Event;
use \Heystack\Subsystem\Products\ProductHolder\ProductHolder;
use \Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableInterface;

class ProductHolderEvent extends Event
{
    protected $product;
    protected $productHolder;

    public function __construct(ProductHolder $productHolder, PurchasableInterface $product)
    {
        $this->productHolder = $productHolder;
        $this->product = $product;
    }

    public function getProduct()
    {
        return $this->product;
    }

    public function getProductHolder()
    {
        return $this->productHolder;
    }

}
