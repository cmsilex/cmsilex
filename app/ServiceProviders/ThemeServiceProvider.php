<?php

namespace CMSilex\ServiceProviders;

use CMSilex\Components\ThemeComponent;
use Silex\Application;
use Silex\ServiceProviderInterface;

class ThemeServiceProvider implements ServiceProviderInterface
{
    protected $em;

    public function register(Application $app)
    {
        $app['theme'] = $app->share(function() use ($app) {
            return new ThemeComponent($app['em'], $app['twig']);
        });
    }

    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }
}