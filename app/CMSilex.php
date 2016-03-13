<?php

namespace CMSilex;

use CMSilex\ServiceProviders\ORMServiceProvider;
use Silex\Application;
use Silex\Provider\SecurityServiceProvider;

class CMSilex extends Application
{
    public function bootstrap()
    {
        $app = $this;
        $app->register(new ORMServiceProvider());
        $app->register(new SecurityServiceProvider());

        $app['security.firewalls'] = array(
            'default' => array(
                'pattern' => '/',
                'http' => false,
                'users' => array(
                    // raw password is foo
                    'admin' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
                ),
            ),
        );

        $app->setRoutes();
    }

    public function setRoutes()
    {
        $app = $this;

        $app->get('admin', function () {

        });

        $app->get('/', function () {
            return "hello";
        });
    }
}
