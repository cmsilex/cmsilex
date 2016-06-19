<?php

namespace CMSilex\ControllerProviders;

use CMSilex\Entities\Setting;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class AdminController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];

        $controller->get('/', function (Application $app, Request $request) {
            return $app['twig']->render('admin/dashboard.html.twig');
        })
        ->bind('dashboard')
        ;

        $controller->match('/settings', function (Application $app, Request $request) {

            $qb = $app['em']->createQueryBuilder();
            $qb
                ->select('s')
                ->from('CMSilex\Entities\Setting', 's', 's.att')
            ;
            $currentSettings = $qb->getQuery()->getResult();

            $builder = $app->form($currentSettings);

            $form = $builder
                ->add('about', TextareaType::class)
                ->add('github', TextType::class)
                ->add('Save', SubmitType::class)
            ->getForm();

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid())
            {
                $allSettings = $form->getData();
                foreach ($allSettings as $key => $value)
                {
                    $newSetting = new Setting($key, $value);
                    $app['em']->merge($newSetting);
                }

                $app['em']->flush();

                return $app->redirect($app->url("settings"));
            }

            return $app['twig']->render('admin/settings.html.twig', [
                'form' => $form->createView()
            ]);
        })
        ->bind('settings')
        ->method('POST|GET')
        ;

        return $controller;
    }
    
}