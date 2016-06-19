<?php

namespace CMSilex\ControllerProviders;

use CMSilex\Entities\User;
use Silex\ControllerProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Silex\Application;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class AuthenticationController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->match('/login', 'CMSilex\ControllerProviders\AuthenticationController::loginAction')
            ->method("POST|GET")
            ->bind('login')
        ;

        if ($app['config']['register'] == true) {
            $controllers->match('/register', 'CMSilex\ControllerProviders\AuthenticationController::loginAction');
        }

        return $controllers;
    }

    public function loginAction (Application $app, Request $request) {
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
            'form'          => $form,
            'error'         => $app['security.last_error']($request),
            'last_username' => $app['session']->get('_security.last_username'),
        ]);
    }

    public function registerAction (Application $app, Request $request) {
        $builder = $app->form();
        $builder
            ->add('email', EmailType::class)
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'Password'
                ],
                'second_options' => [
                    'label' => 'Repeat Password'
                ]
            ])
            ->add('register', SubmitType::class)
        ;
        $form = $builder->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $userInfo = $form->getData();

            $newUser = new User();
            $password = $app->encodePassword($newUser, $userInfo['password']);
            $newUser->setUsername($userInfo['email']);
            $newUser->setPassword($password);
            $newUser->setEnabled(true);
            $newUser->setAccountNonExpired(true);
            $newUser->setAccountNonLocked(true);
            $newUser->setCredentialsNonExpired(true);
            $newUser->setRoles(['ROLE_USER']);
            $app['em']->persist($newUser);
            $app['em']->flush();

            return $app->redirect($app->url('login'));
        }

        return $app->render('authentication/register.html.twig', [
            'form' => $form->createView()
        ]);
    }
}