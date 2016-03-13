<?php

namespace CMSilex;

use CMSilex\ControllerProviders\AuthenticationController;
use CMSilex\ServiceProviders\ManagerRegistryServiceProvider;
use CMSilex\ServiceProviders\ORMServiceProvider;
use Doctrine\Common\Persistence\Proxy;
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
use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use CMSilex\Entities\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

class CMSilex extends Application
{
    use Application\FormTrait;
    use Application\TwigTrait;
    use Application\UrlGeneratorTrait;
    use Application\SecurityTrait;

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

        $app['security.encoder.digest'] = $app->share(function ($app) {
            // uses the password-compat encryption
            return new BCryptPasswordEncoder(10);
        });

        $app->register(new ManagerRegistryServiceProvider());

        $app['security.firewalls'] = array(
            'default' => array(
                'pattern' => '/',
                'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
                'users' => $app->share(function() use ($app) {
                    return new EntityUserProvider($app['manager_registry'], User::class, 'username');
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

        $app->get('/create', function (Application $app, Request $request) {
            $user = new User();
            $user->setUsername('admin');
            $user->setAccountNonExpired(true);
            $user->setAccountNonLocked(true);
            $user->setCredentialsNonExpired(true);
            $user->setEnabled(true);
            $user->setRoles(['ROLE_ADMIN']);
            $user->setPassword($app->encodePassword($user, 'password'));
            $user->setSalt(null);
            $user->setId(1);

            $success = $app['em']->persist($user);
            dump($success);
            $success = $app['em']->flush();
            dump($success);
            return $app->json($user);
        });
    }
}
