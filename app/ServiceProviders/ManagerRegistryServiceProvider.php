<?php

namespace CMSilex\ServiceProviders;

use CMSilex\ManagerRegistry;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ManagerRegistryServiceProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $app['manager_registry'] = $app->share(function () use ($app) {
            return new ManagerRegistry($app['em']);
        });
    }

    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }
}