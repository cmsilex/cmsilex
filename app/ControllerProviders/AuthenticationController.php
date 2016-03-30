<?php

namespace CMSilex\ControllerProviders;

use CMSilex\Entities\User;
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


        $controllers->match('/encode', function (Application $app, Request $request) {
            $form = $app['form.factory']->createNamedBuilder(null)
                ->add('password')
                ->add('submit', SubmitType::class)
                ->getForm()
            ;

            $form->handleRequest($request);

            $password = null;

            if ($form->isValid() && $form->isSubmitted())
            {
                $tmpUser = new User();
                $data = $form->getData();
                $password = $app->encodePassword($tmpUser, $data['password']);
            }

            $form = $form->createView();

            return $app->render('authentication/encode.html.twig', [
                'form' => $form,
                'password' => $password
            ]);
        })
        ->method("POST|GET")
        ->bind('encode')
        ;

        return $controllers;
    }
}