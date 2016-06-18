<?php

namespace CMSilex\ControllerProviders;

use CMSilex\Entities\Menu;
use CMSilex\Forms\Types\MenuType;
use Doctrine\ORM\Persisters\PersisterException;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class MenuController implements ControllerProviderInterface
{
    public function connect (Application $app)
    {
        $controller = $app['controllers_factory'];
        
        $controller->get('/', 'CMSilex\ControllerProviders\MenuController::listMenusAction')
            ->bind('list_menus')
        ;

        $controller->match('/edit/{id}', 'CMSilex\ControllerProviders\MenuController::editMenuAction')
            ->bind('edit_menu')
            ->value('id', null)
            ->convert('menu', 'converter:convertEntity')
            ->method('GET|POST')
        ;
        
        return $controller;
    }
    
    public function listMenusAction (Application $app)
    {
        $menus = $app['em']->getRepository('CMSilex\Entities\Menu')->findAll();

        $columns = [
            'Name' => 'name',
            'Edit' => function (Menu $menu) use ($app) {
                return '<a href="' . $app->url('edit_menu', ['id' => $menu->getId()]) . '">Edit</a>';
            }
        ];

        return $app->render('admin/list.html.twig', [
            'items' => $menus,
            'columns' => $columns
        ]);
    }

    public function editMenuAction (Menu $menu, Request $request, Application $app)
    {
        $form = $app['form.factory']->create(MenuType::class, $menu);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $formData = $form->getData();

            dump($formData);

            $app['em']->persist($formData);

            $app['em']->flush();

            return $app->redirect($app->url('edit_menu', ['id' => $formData->getId()]));
        }
        
        return $app->render('admin/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
}