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

        $controller->get('/pages', 'CMSilex\ControllerProviders\AdminController::listPagesAction')
            ->bind('list_pages')
        ;

        $controller->get('/pages/new', 'CMSilex\ControllerProviders\AdminController::editPageAction')
            ->method('POST|GET')
            ->bind('new_page')
            ->value('id', null)
        ;

        $controller->get('/pages/{id}/edit', 'CMSilex\ControllerProviders\AdminController::editPageAction')
            ->method('POST|GET')
            ->bind('edit_page')
        ;

        $controller->get('/pages/{id}/delete', 'CMSilex\ControllerProviders\AdminController::deletePageAction')
            ->method('GET')
            ->bind('delete_page')
        ;

        return $controller;
    }

    public function listPagesAction (Application $app, Request $request)
    {
        $pages = $app['em']->getRepository('CMSilex\Entities\Page')->findBy(['deleted' => false]);

        return $app->render('admin/list.html.twig', [
            'rows' => $pages,
            'columns' => [
                'title',
                'slug',
                'edit' => function(Page $page) use ($app) {
                    return '<a href="' .$app->url('edit_page',
                        ['id' => $page->getId()]
                    ) . '">Edit</a>';
                },
                'view' => function(Page $page) use ($app) {
                    return '<a href="/' . $page->getSlug() . '">View</a>';
                },
                'delete' => function(Page $page) use ($app) {
                    return '<a href="' .$app->url('delete_page',
                        ['id' => $page->getId()]
                    ) . '">Delete</a>';
                },
            ]
        ]);
    }

    public function editPageAction (Application $app, Request $request, $id)
    {
        $page = $id ? $app['em']->find('CMSilex\Entities\Page', $id) : null;

        $form = $app['form.factory']->createBuilder(PageType::class, $page)
            ->add('save', SubmitType::class)
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            if ($form->isValid())
            {
                $page = $form->getData();
                $app['em']->persist($page);
                $app['em']->flush();
                if (!is_dir($page->getSlug()))
                {
                    mkdir($page->getSlug());
                }

                if ($page->getSlug()){
                    $filedir = $page->getSlug() . '/index.html';
                } else {
                    $filedir = './index.html';
                }



                $string = $app['twig']->render('page/standard.html.twig', [
                    'page' => $page
                ]);

                file_put_contents($filedir, $string);

                return $app->redirect($app->url('list_pages'));
            }
        }

        return $app->render('admin/edit.html.twig', [
           'form' => $form->createView()
        ]);
    }

    public function deletePageAction (Application $app, Request $request, $id)
    {
        $page = $app['em']->find('CMSilex\Entities\Page', $id);

        if ($page)
        {
            $page->setDeleted(true);
            $app['em']->persist($page);
            $app['em']->flush();
        }

        return $app->redirect($app->url('list_pages'));
    }
}