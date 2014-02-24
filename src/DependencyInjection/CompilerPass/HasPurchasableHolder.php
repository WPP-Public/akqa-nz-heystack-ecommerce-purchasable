<?php

namespace Heystack\Subsystem\Purchasable\DependencyInjection\CompilerPass;

use Heystack\Subsystem\Core\DependencyInjection\CompilerPass\HasService;
use Heystack\Subsystem\Purchasable\Services;

/**
 * Class HasPurchasableHolder
 * @package Heystack\Subsystem\Purchasable\DependencyInjection\CompilerPass
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