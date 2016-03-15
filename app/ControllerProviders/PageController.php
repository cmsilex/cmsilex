<?php

namespace CMSilex\ControllerProviders;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PageController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];

        $controller->get('{slug}', 'CMSilex\ControllerProviders\PageController::getPageAction')
            ->bind('page')
        ;

        return $controller;
    }

    public function getPageAction (Application $app, Request $request, $slug)
    {
        $page = $app['em']->getRepository('CMSilex\Entities\Page')->findOneBy(['slug' => $slug, 'deleted' => false]);

        if (!$page)
        {
            throw new NotFoundHttpException("Page not found");
        }

        return $app->render('page/standard.html.twig', [
            'page' => $page
        ]);
    }
}