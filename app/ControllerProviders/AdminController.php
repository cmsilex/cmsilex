<?php

namespace CMSilex\ControllerProviders;

use CMSilex\Entities\Page;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use CMSilex\Forms\Types\PageType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AdminController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];

        $controller->get('/', function (Application $app, Request $request) {
            return $app['twig']->render('admin/dashboard.html.twig');
        });

        return $controller;
    }


}