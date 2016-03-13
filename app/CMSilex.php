<?php

namespace CMSilex;

use CMSilex\ControllerProviders\AuthenticationController;
use CMSilex\ServiceProviders\ORMServiceProvider;
use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

class CMSilex extends Application
{
    use Application\FormTrait;
    use Application\TwigTrait;
    use Application\UrlGeneratorTrait;

    public function bootstrap()
    {
        $app = $this;
        $app['debug'] = true;

        ErrorHandler::register();
        ExceptionHandler::register();

        $app->register(new UrlGeneratorServiceProvider());

        $app->register(new HttpFragmentServiceProvider());
        $app->register(new ServiceControllerServiceProvider());
        $app->register(new TwigServiceProvider(),[
            'twig.path' => __DIR__ . '/../resources/views'
        ]);
        $app->register(new ORMServiceProvider());
        $app->register(new SessionServiceProvider());
        $app->register(new SecurityServiceProvider());
        $app['security.firewalls'] = array(
            'default' => array(
                'pattern' => '/',
                'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
                'users' => $app->share(function() use ($app) {
                    return new EntityU
                }),
                'anonymous' => true,
            ),
        );

        $app['security.access_rules'] = array(
            array('^/admin', 'ROLE_ADMIN'),
        );

        $app->register(new WebProfilerServiceProvider(), [
            'profiler.cache_dir' => __DIR__ . '/../storage/framework/cache/profiler'
        ]);
        $app->register(new TranslationServiceProvider(), array(
            'locale_fallbacks' => array('en'),
            'locale' => 'en'
        ));
        $app->register(new ValidatorServiceProvider());
        $app->register(new FormServiceProvider());



        $app->setRoutes();
    }

    function __call($name, $arguments)
    {
        // TODO: Implement __call() method.
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
