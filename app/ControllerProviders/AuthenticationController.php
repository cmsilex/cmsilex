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
            $form = $app['form.factory']->createNamedBuilder(null)
                ->add('_username')
                ->add('_password')
                ->add('submit', SubmitType::class)
                ->setAction($app->url('login_check'))
                ->getForm()
            ;

            $form->handleRequest($request);

            $form = $form->createView();

            return $app->render('authentication/login.html.twig', [
                'form' => $form,
                'error'         => $app['security.last_error']($request),
                'last_username' => $app['session']->get('_security.last_username'),
            ]);
        })
        ->method("POST|GET")
        ->bind('login')
        ;

        return $controllers;
    }
}