<?php

namespace CMSilex;

use CMSilex\ControllerProviders\AuthenticationController;
use CMSilex\ServiceProviders\ORMServiceProvider;
use Silex\Application;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;

class CMSilex extends Application
{
    public function bootstrap()
    {
        $app = $this;
        $app->register(new ORMServiceProvider());
        $app->register(new SessionServiceProvider());
        $app->register(new SecurityServiceProvider());

        $app['security.firewalls'] = array(
            'default' => array(
                'pattern' => '/',
                'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
                'users' => array(
                    'admin' => array('ROLE_ADMIN', '5FZ2Z8QIkA7UTZ4BYkoC+GsReLf569mSKDsfods6LYQ8t+a8EW9oaircfMpmaLbPBh4FOBiiFyLfuZmTSUwzZg=='),
                ),
                'anonymous' => true,
            ),
        );

        $app['security.access_rules'] = array(
            array('^/admin', 'ROLE_ADMIN'),
        );

        $app->setRoutes();
    }

    public function setRoutes()
    {
        $app = $this;

        $app->get('/admin', function () {
            return "admin";
        });

        $app->get('/', function () {
            return "hello";
        });

        $app->mount('/', new AuthenticationController());
    }
}
