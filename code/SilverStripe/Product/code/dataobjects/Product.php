<?php

use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableInterface;
use Heystack\Subsystem\Core\State\ExtraDataInterface;

class Product extends DataObject implements PurchasableInterface, Serializable, ExtraDataInterface
{

    use Heystack\Subsystem\Products\Product\DataObjectTrait;
    use Heystack\Subsystem\Core\State\Traits\ExtraDataTrait;

    protected $quantity = 0;
    protected $unitPrice = 0;

    public static $db = array(
        'Name' => 'Varchar(255)'
    );

    public function getExtraData()
    {
        return array(
            'quantity' => $this->quantity,
            'unitPrice' => $this->unitPrice
        );
    }

    public function getPrice()
    {
        return $this->ID * 100.00;
    }

    public function setUnitPrice(\Float $unitPrice)
    {
        $this->unitPrice = $unitPrice;
    }

    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    public function setQuantity($quantity = 1)
    {
        $this->quantity = $quantity;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getTotal()
    {
        return $this->getQuantity() * $this->getUnitPrice();
    }

}
