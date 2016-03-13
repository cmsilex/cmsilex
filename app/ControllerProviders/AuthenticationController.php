<?php

namespace CMSilex\ControllerProviders;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class AuthenticationController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match('/login', function (Application $app, Request $request) {
            $builder = $app['form.factory']->createNamedBuilder(null);
            $builder
                ->add('_username')
                ->add('_password')
            ;
            return "lol";
        })
        ->method("POST|GET")
        ->bind('login')
        ;

        return $controllers;
    }
}