<?php

namespace CMSilex\ControllerProviders;

use CMSilex\Entities\Post;
use Doctrine\Common\Collections\ArrayCollection;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class FrontendController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];

        $controller->get('/{url}', 'CMSilex\ControllerProviders\FrontendController::indexAction')
            ->value('url', 'home')
            ->bind('page')
        ;

        $controller->get('/{year}/{month}/{day}/{slug}', 'CMSilex\ControllerProviders\FrontendController::getPostAction')
            ->assert('year', '[0-9]{4}')
            ->assert('month', '[0-9]{2}')
            ->assert('day', '[0-9]{2}')
            ->bind('post')
            ->convert('post', function ($post, Request $request) use ($app) {
                $post = $app['em']->getRepository('CMSilex\Entities\Post')->findOneBySlug($request->get('slug'));
                if (!$post)
                {
                    throw new ResourceNotFoundException();
                } else {
                    return $post;
                }
            })
        ;
        
        return $controller;
    }

    public function indexAction (Application $app, Request $request, $url)
    {
        if ($url) {
            $urlParts = new ArrayCollection(explode('/', $url));
            $last = $urlParts->last();
            $page = $app['em']->getRepository('CMSilex\Entities\Page')->findOneBy(['slug' => $last]);

            $posts = $app['em']->getRepository('CMSilex\Entities\Post')->findAll();

            if ($page) {
                return $app->render('@theme/page.html.twig', [
                    'page' => $page,
                    'posts' => $posts
                ]);
            } else {
                $post = $app['em']->getRepository('CMSilex\Entities\Post')->findOneBy(['slug' => $last]);

                if ($post) {
                    return $app->render('frontend/page.html.twig', [
                        'page' => $post,
                        'posts' => []
                    ]);
                }
            }
        }
        
        throw new NotFoundHttpException();
    }

    public function getPostAction (Post $post, Application $app, Request $request)
    {
        return $app->render('@theme/post.html.twig', [
            'page' => $post
        ]);
    }
}