<?php

namespace CMSilex;

use CMSilex\ControllerProviders\AdminController;
use CMSilex\ControllerProviders\AuthenticationController;
use CMSilex\ControllerProviders\FrontendController;
use CMSilex\ControllerProviders\MediaController;
use CMSilex\ControllerProviders\MenuController;
use CMSilex\ControllerProviders\PageController;
use CMSilex\ControllerProviders\PostController;
use CMSilex\Entities\Page;
use CMSilex\ServiceProviders\ConfigServiceProvider;
use CMSilex\ServiceProviders\ConverterServiceProvider;
use CMSilex\ServiceProviders\ManagerRegistryServiceProvider;
use CMSilex\ServiceProviders\ORMServiceProvider;
use CMSilex\ServiceProviders\RSTServiceProvider;
use CMSilex\ServiceProviders\TextileServiceProvider;
use CMSilex\ServiceProviders\ThemeServiceProvider;
use Silex\Application;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SerializerServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use CMSilex\Entities\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

class CMSilex extends Application
{
    use Application\FormTrait;
    use Application\TwigTrait;
    use Application\UrlGeneratorTrait;
    use Application\SecurityTrait;

    public function bootstrap()
    {
        $app = $this;

        $app['dir.base'] = __DIR__ . "/../../../../";

        $app->register(new ConfigServiceProvider());

        $app['debug'] = $app['config']['debug'];
        $app->register(new UrlGeneratorServiceProvider());

        $app->register(new HttpFragmentServiceProvider());
        $app->register(new ServiceControllerServiceProvider());

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
                'logout' => array('logout_path' => '/logout', 'invalidate_session' => true),
                'users' => function () use ($app) {
                    return new EntityUserProvider($app['manager_registry'], User::class, 'username');
                },
                'anonymous' => true,
            ),
        );

        $app['security.access_rules'] = [
            ['^/admin', 'ROLE_ADMIN'],
        ];

        $app->register(new TranslationServiceProvider(), array(
            'locale_fallbacks' => array('en'),
            'locale' => 'en'
        ));
        $app->register(new ValidatorServiceProvider());
        $app->register(new FormServiceProvider());
        $app->register(new TwigServiceProvider(),[
            'twig.path' => __DIR__ . '/../resources/views',
            'twig.form.templates' => [
                'bootstrap_3_layout.html.twig'
            ],
            'twig.strict_variables' => false
        ]);

        $app['twig.loader.filesystem']->addPath($app['dir.base'] . "/themes/" . $app['config']['theme'], 'theme');

        $app['twig'] = $app->share($app->extend('twig', function (\Twig_Environment $twig) {
            $twig->addTest(new \Twig_SimpleTest('callable',function ($variable){
                return is_callable($variable);
            }));

            $twig->addFunction(new \Twig_SimpleFunction('is_callable', function ($variable){
                return is_callable($variable);
            }));

            $twig->addFunction(new \Twig_SimpleFunction('call_user_func', function ($callable, $params = null) {
                return call_user_func($callable, $params);
            }));

            $propFunction = new \Twig_SimpleFilter('prop', function ($object, $property) use ($twig) {
                if (is_callable($property)) {
                    return call_user_func($property, $object);
                } else {
                    return  "LOL";
                }
            }, ['is_safe' => array('html')]);

            $twig->addFilter($propFunction);

            return $twig;
        }));

        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new EntityType($app['manager_registry']);

            return $types;
        }));

        $app->register(new SerializerServiceProvider());

        $app->register(new TextileServiceProvider());

        $app->register(new WebProfilerServiceProvider(), [
            'profiler.cache_dir' => './../storage/framework/cache/profiler',
            'web_profiler.debug_toolbar.enable' => $app['debug'],
            'profiler.mount_prefix' => '/admin/_profiler'
        ]);
        
        $app['finder'] = $app->share(function () {
            return new Finder();
        });

        $app['filesystem'] = $app->share(function () {
            return new Filesystem();
        });

        $app->register(new RSTServiceProvider());

        $app->register(new ConverterServiceProvider());

        $app->register(new ThemeServiceProvider());

        $app->setRoutes();
    }

    public function setRoutes()
    {
        $app = $this;

        $app->mount('/', new AuthenticationController());
        $app->mount('/admin', new AdminController());
        $app->mount('/admin', new PageController());
        $app->mount('/admin', new PostController());
        $app->mount('/admin/menus/', new MenuController());
        $app->mount('/admin/media/', new MediaController());

        $app->mount('/', new FrontendController());

    }
}
