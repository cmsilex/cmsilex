<?php

namespace CMSilex;

use CMSilex\ControllerProviders\AdminController;
use CMSilex\ControllerProviders\AuthenticationController;
use CMSilex\ControllerProviders\CMSController;
use CMSilex\ControllerProviders\FrontendController;
use CMSilex\ControllerProviders\MediaController;
use CMSilex\Entities\CMSField;
use CMSilex\Forms\Types\PageType;
use CMSilex\Forms\Types\TemplateChoiceType;
use CMSilex\ServiceProviders\CMSServiceProvider;
use CMSilex\ServiceProviders\ConfigServiceProvider;
use CMSilex\ServiceProviders\ConverterServiceProvider;
use CMSilex\ServiceProviders\ManagerRegistryServiceProvider;
use CMSilex\ServiceProviders\ORMServiceProvider;
use CMSilex\ServiceProviders\ThemeServiceProvider;
use Silex\Application as SilexApplication;
use Silex\Provider\CsrfServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\HttpFragmentServiceProvider;
use Silex\Provider\RememberMeServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use Silex\Provider\SerializerServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\WebProfilerServiceProvider;
use CMSilex\Entities\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bridge\Doctrine\Security\User\EntityUserProvider;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

class Application extends SilexApplication
{
    use SilexApplication\FormTrait;
    use SilexApplication\TwigTrait;
    use SilexApplication\SecurityTrait;
    use SilexApplication\UrlGeneratorTrait;

    public function __construct(array $values = array())
    {
        parent::__construct($values);
        $this->bootstrap();
    }

    public function bootstrap()
    {
        $app = $this;

        $app['dir.base'] = __DIR__ . "/../../../../";

        $app->register(new ConfigServiceProvider());

        $app['debug'] = $app['config']['debug'];

        ErrorHandler::register();
        ExceptionHandler::register($app['debug']);

        $app->register(new HttpFragmentServiceProvider());
        $app->register(new ServiceControllerServiceProvider());

        $app->register(new ORMServiceProvider());
        $app->register(new SessionServiceProvider());
        $app->register(new SecurityServiceProvider());
        $app->register(new RememberMeServiceProvider());
        $app->register(new CsrfServiceProvider());

        $app['security.encoder.digest'] = function ($app) {
            // uses the password-compat encryption
            return new BCryptPasswordEncoder(10);
        };

        $app->register(new ManagerRegistryServiceProvider());

        $app['security.firewalls'] = array(
            'default' => array(
                'pattern' => '/',
                'form' => array('login_path' => '/login', 'check_path' => '/login_check'),
                'logout' => array('logout_path' => '/logout', 'invalidate_session' => true),
                'users' => function () use ($app) {
                    return new EntityUserProvider($app['manager_registry'], User::class, 'username');
                },
                'remember_me' => array(
                    'key' => 'r12cJffgrWhJTTWsxmtr781'
                ),
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

        $app->extend('twig', function (\Twig_Environment $twig) {
            $twig->addTest(new \Twig_SimpleTest('callable',function ($variable){
                return is_callable($variable);
            }));

            $twig->addFunction(new \Twig_SimpleFunction('is_callable', function ($variable){
                return is_callable($variable);
            }));

            $twig->addFunction(new \Twig_SimpleFunction('call_user_func', function ($callable, $params = null) {
                return call_user_func($callable, $params);
            }));

            $twig->getExtension('core')->setDateFormat('Y/m/d', '%d days');

            return $twig;
        });

        $app->extend('form.types', function ($types) use ($app) {
            $types[] = new EntityType($app['manager_registry']);
            $types[] = new TemplateChoiceType($app['theme']);
            $types[] = new PageType($app['theme']);
            return $types;
        });

        $app->register(new SerializerServiceProvider());

        $app->register(new WebProfilerServiceProvider(), [
            'profiler.cache_dir' => './../storage/framework/cache/profiler',
            'web_profiler.debug_toolbar.enable' => $app['debug'],
            'profiler.mount_prefix' => '/admin/_profiler'
        ]);
        
        $app['finder'] = function () {
            return new Finder();
        };

        $app['filesystem'] = function () {
            return new Filesystem();
        };

        $app->register(new ConverterServiceProvider());

        $app->register(new ThemeServiceProvider());
        $app['twig.loader.filesystem']->addPath($app['dir.theme'], 'theme');

        $app->register(new CMSServiceProvider());

        $app->setRoutes();
    }

    public function setRoutes()
    {
        $app = $this;

        $app->mount('/', new AuthenticationController());
        $app->mount('/admin', new AdminController());
        $app->mount('/admin/media/', new MediaController());
        $app->mount('/admin/cms/', new CMSController());

        $app->mount('/', new FrontendController());

        $app->after(function (Request $request, Response $response) {
            if (strpos($request->getPathInfo(), '/admin') === 0) {
                $response->headers->addCacheControlDirective('must-revalidate', true);
                $response->headers->addCacheControlDirective('no-store', true);
            }
        });
    }
}
