<?php

namespace CMSilex;

use CMSilex\ControllerProviders\AuthenticationController;
use CMSilex\ServiceProviders\ORMServiceProvider;
use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;

class CMSilex extends Application
{
    public function bootstrap()
    {
        $app = $this;
        $app['debug'] = true;

        $app->register(new ORMServiceProvider());
        $app->register(new SessionServiceProvider());
        $app->register(new SecurityServiceProvider());

        $app->register(new TranslationServiceProvider(), array(
            'locale_fallbacks' => array('en'),
            'locale' => 'en'
        ));
        $app->register(new ValidatorServiceProvider());

        $app->register(new TwigServiceProvider());
        $app->register(new FormServiceProvider());

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
