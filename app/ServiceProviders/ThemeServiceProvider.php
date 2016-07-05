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
        $app['dir.theme'] = $app['dir.base'] . "/themes/" . $app['config']['theme'];

        $app['theme'] = $app->share(function() use ($app) {
            return new ThemeComponent($app['em'], $app['twig'], $app['dir.theme']);
        });

    }

    public function boot(Application $app)
    {
        
    }
}