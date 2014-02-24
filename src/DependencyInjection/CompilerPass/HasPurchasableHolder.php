<?php

namespace Heystack\Purchasable\DependencyInjection\CompilerPass;

use Heystack\Core\DependencyInjection\CompilerPass\HasService;
use Heystack\Purchasable\Services;

/**
 * Class HasPurchasableHolder
 * @package Heystack\Purchasable\DependencyInjection\CompilerPass
 */
class HasPurchasableHolder extends HasService
{
    /**
     * @return string
     */
    protected function getServiceName()
    {
        return Services::PURCHASABLEHOLDER;
    }

    /**
     * @return string
     */
    protected function getServiceSetterName()
    {
        return 'setPurchasableHolder';
    }
}