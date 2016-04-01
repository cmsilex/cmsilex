<?php

namespace CMSilex\ControllerProviders;

use Doctrine\Common\Collections\ArrayCollection;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class FrontendController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];

        $controller->get('/{url}', 'CMSilex\ControllerProviders\FrontendController::indexAction')
            ->value('url', 'home')
            ->assert('url', '[^/_profiler]+')
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
                return $app->render('frontend/page.html.twig', [
                    'page' => $page
                ]);
            }
        }
        dump($urlParts);exit;
    }
}