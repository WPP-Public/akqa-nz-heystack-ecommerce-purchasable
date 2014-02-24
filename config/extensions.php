<?php

use Camspiers\DependencyInjection\SharedContainerFactory;
use Heystack\Purchasable\DependencyInjection\ContainerExtension;
use Heystack\Purchasable\DependencyInjection\CompilerPass\HasPurchasableHolder;

SharedContainerFactory::addExtension(new ContainerExtension());
SharedContainerFactory::addCompilerPass(new HasPurchasableHolder());
