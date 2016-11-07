<?php

namespace CMSilex\ServiceProviders;

use CMSilex\ManagerRegistry;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;

class ManagerRegistryServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['manager_registry'] = function () use ($container) {
            return new ManagerRegistry($container['em']);
        };
    }
}