<?php

namespace CMSilex;

use CMSilex\ControllerProviders\AdminController;
use CMSilex\ControllerProviders\AuthenticationController;
use CMSilex\ControllerProviders\FrontendController;
use CMSilex\ControllerProviders\MediaController;
use CMSilex\ControllerProviders\PageController;
use CMSilex\ControllerProviders\PostController;
use CMSilex\Entities\Page;
use CMSilex\ServiceProviders\ConfigServiceProvider;
use CMSilex\ServiceProviders\ManagerRegistryServiceProvider;
use CMSilex\ServiceProviders\ORMServiceProvider;
use CMSilex\ServiceProviders\RSTServiceProvider;
use CMSilex\ServiceProviders\TextileServiceProvider;
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
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
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
                'users' => [
                    $app['config']['username'] => ['ROLE_ADMIN', $app['config']['password']],
                ],
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

        $app['form.types'] = $app->share($app->extend('form.types', function ($types) use ($app) {
            $types[] = new EntityType($app['manager_registry']);

            return $types;
        }));

        $app->extend('twig', $app->share(function (\Twig_Environment $twig) use ($app) {
            $isCallableFunction = new \Twig_SimpleFunction('is_callable', function ($subject) {
                return is_callable($subject);
            });
            $twig->addFunction($isCallableFunction);
            $callUserFuncFunction = new \Twig_SimpleFunction('call_user_func', function ($subject, $param) {
                return call_user_func($subject, $param);
            });
            $twig->addFunction($callUserFuncFunction);
            return $twig;
        }));

        $app->register(new SerializerServiceProvider());

        $app->register(new TextileServiceProvider());

        $app->register(new WebProfilerServiceProvider(), [
            'profiler.cache_dir' => __DIR__ . '/../storage/framework/cache/profiler'
        ]);
        
        $app['finder'] = $app->share(function () {
            return new Finder();
        });

        $app['filesystem'] = $app->share(function () {
            return new Filesystem();
        });

        $app->register(new RSTServiceProvider());
        
        $app->setRoutes();
    }

    public function setRoutes()
    {
        $app = $this;

        $app->mount('/', new AuthenticationController());
        $app->mount('/admin/', new AdminController());
        $app->mount('/admin/', new PageController());
        $app->mount('/admin/', new PostController());
        $app->mount('/admin/media/', new MediaController());
        $app->mount('/', new FrontendController());


        $app->get('/create', function (Application $app, Request $request) {
            exit;
            $user = new User();
            $user->setUsername('admin');
            $user->setAccountNonExpired(true);
            $user->setAccountNonLocked(true);
            $user->setCredentialsNonExpired(true);
            $user->setEnabled(true);
            $user->setRoles(['ROLE_ADMIN']);
            $user->setPassword($app->encodePassword($user, "foo"));
            dump($user->getPassword());exit;
            $user->setSalt(null);
            try {
                dump($user);
                $success = $app['em']->persist($user);
                dump($success);
                $success = $app['em']->flush();
                dump($success);

                $page = new Page();
                $page->setSlug('lol');
                $page->setTitle("LOL");


                $success = $app['em']->persist($page);
                dump($success);
                $success = $app['em']->flush();
                dump($success);
                dump($page->getId());
            } catch (\Exception $e) {
                dump($e);
            }

            return $app->json($user);
        });
    }
}
