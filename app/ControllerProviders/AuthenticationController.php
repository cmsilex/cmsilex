<?php

namespace CMSilex\ControllerProviders;

use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;

class AuthenticationController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match('/login', function (Application $app, Request $request) {
            $app->form();
        })
        ->method("POST|GET")
        ->bind('login')
        ;

        return $controllers;
    }
}