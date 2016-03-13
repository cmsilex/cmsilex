<?php

namespace CMSilex\ControllerProviders;

use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AuthenticationController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match('/login', function (Application $app, Request $request) {
            $builder = $app->form();
            $builder
                ->add('_username')
                ->add('_password')
                ->add('submit', SubmitType::class)
                ->setAction($app->url('default_login_check'))
            ;

            $form = $builder->getForm()->createView();
            return $app->render('authentication/login.html.twig', compact('form'));
        })
        ->method("POST|GET")
        ->bind('login')
        ;

        return $controllers;
    }
}