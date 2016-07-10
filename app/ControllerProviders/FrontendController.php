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

        $controller->get('/{date}/{slug}', 'CMSilex\ControllerProviders\FrontendController::getPostAction')
            ->assert('date', '[0-9]{4}/[0-9]{2}/[0-9]{2}')
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
            
            if ($page) {
                $template = $page->getTemplate();
                return $app->render('@theme/' . $template . '.html.twig', [
                    'page' => $page
                ]);
            } else {
                throw new NotFoundHttpException();
            }
        }
        
        throw new NotFoundHttpException();
    }

    public function getPostAction (Post $post, Application $app, Request $request)
    {
        return $app->render('@theme/page.html.twig', [
            'page' => $post
        ]);
    }
}