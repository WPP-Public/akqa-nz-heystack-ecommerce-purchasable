<?php

use Camspiers\DependencyInjection\SharedContainerFactory;
use Heystack\Subsystem\Purchasable\DependencyInjection\ContainerExtension;
use Heystack\Subsystem\Purchasable\DependencyInjection\CompilerPass\HasPurchasableHolder;

SharedContainerFactory::addExtension(new ContainerExtension());
SharedContainerFactory::addCompilerPass(new HasPurchasableHolder());
