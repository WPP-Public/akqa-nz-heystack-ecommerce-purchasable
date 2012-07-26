<?php

use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableInterface;
use Heystack\Subsystem\Core\State\ExtraDataInterface;
use Heystack\Subsystem\Core\Storage\StorableInterface;

class Product extends DataObject implements PurchasableInterface, Serializable, ExtraDataInterface, StorableInterface
{

    use Heystack\Subsystem\Products\Product\DataObjectTrait;
    use Heystack\Subsystem\Core\State\Traits\ExtraDataTrait;

    protected $quantity = 0;
    protected $unitPrice = 0;

    public static $db = array(
        'Name' => 'Varchar(255)',
        'TestStuff' => 'Varchar(255)'
    );

    public static $has_one = array(
        'SingleStorable' => 'TestStorable'
    );

    public static $has_many = array(
        'HasyManyStore' => 'TestManyStorable'
    );

    public static $many_many = array(
        'ManyManyStorable'=> 'TestManyManyStorable'
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
        $currencyService = Heystack\Subsystem\Core\ServiceStore::getService(Heystack\Subsystem\Ecommerce\Currency\CurrencyService::STATE_KEY);

        $activeCurrencyCode = $currencyService->getActiveCurrency()->getIdentifier();

        $price = $this->ID * 100.00;

        switch ($activeCurrencyCode) {
            case 'NZD':
                $price *= 1;
                break;
            case 'USD':
                $price *= 2;
                break;
            default:
                $price *= 3;
                break;
        }

        return $price;
    }

    public function setUnitPrice($unitPrice)
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

    public function getStorableData()
    {
        $data = array();

        $data['id'] = "Product";

        $data['flat'] = array(
            'Name' => $this->Name,
            'TestStuff' => $this->TestStuff,
            'Total' => $this->getTotal(),
            'Quantity' => $this->getQuantity(),
            'UnitPrice' => $this->getUnitPrice(),
        );

        $data['parent'] = true;

        foreach ($this->HasyManyStore() as $related) {

            $relatedData = $related->getStorableData();

            $data['related'][] = array(
                'flat' => $relatedData['flat'],
                'id' => $relatedData['id']
            );

        }

        return $data;

    }

    public function getStorageBackendIdentifiers()
    {
        return array(
            'silverstripe_orm'
        );
    }

}
