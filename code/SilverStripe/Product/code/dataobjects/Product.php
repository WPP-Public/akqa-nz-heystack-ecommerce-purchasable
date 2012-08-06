<?php

use Heystack\Subsystem\Ecommerce\Purchasable\Interfaces\PurchasableInterface;
use Heystack\Subsystem\Core\State\ExtraDataInterface;

use Heystack\Subsystem\Products\Product\DataObjectTrait;
use Heystack\Subsystem\Core\State\Traits\ExtraDataTrait;

use Heystack\Subsystem\Core\Storage\StorableInterface;
use Heystack\Subsystem\Core\Storage\Backends\SilverStripeOrm\Backend;
use Heystack\Subsystem\Core\Storage\Traits\ParentReferenceTrait;

class Product extends DataObject implements PurchasableInterface, Serializable, ExtraDataInterface, StorableInterface
{

    use DataObjectTrait;
    use ExtraDataTrait;
    use ParentReferenceTrait;

    const IDENTIFIER = 'product';

    protected $quantity = 0;
    protected $unitPrice = 0;

    public static $db = array(
        'Name' => 'Varchar(255)',
        'TestStuff' => 'Varchar(255)'
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

    public function getStorableIdentifier()
    {

        return self::IDENTIFIER;

    }

    public function getStorableData()
    {
        $data = array();

        $data['id'] = "Product";

        $data['flat'] = array(
            'Name' => $this->Name,
            'Total' => $this->getTotal(),
            'Quantity' => $this->getQuantity(),
            'UnitPrice' => $this->getUnitPrice(),
            'ParentID' => $this->parentReference
        );

        $data['parent'] = true;

        $data['related'] = false;

        return $data;

    }

    /**
     * @todo document this
     * @return string
     */
    public function getStorableBackendIdentifiers()
    {
        return array(
            Backend::IDENTIFIER
        );
    }

}
