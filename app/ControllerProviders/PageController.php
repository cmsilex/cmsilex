<?php

namespace CMSilex\ControllerProviders;

use CMSilex\Entities\Page;
use CMSilex\Forms\Types\PageType;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class PageController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];

        $controller->get('/pages', 'CMSilex\ControllerProviders\PageController::listPagesAction')
            ->bind('list_pages')
        ;

        $controller->get('/pages/new', 'CMSilex\ControllerProviders\PageController::editPageAction')
            ->method('POST|GET')
            ->bind('new_page')
            ->value('id', null)
        ;

        $controller->match('/pages/{id}', 'CMSilex\ControllerProviders\PageController::editPageAction')
            ->method('POST|GET')
            ->bind('edit_page')
        ;

        return $controller;
    }

    public function listPagesAction (Application $app, Request $request)
    {
        $pages = $app['em']->getRepository('CMSilex\Entities\Page')->findAll();

        return $app->render('admin/page/list.html.twig', [
            'heading' => 'Pages',
            'rows' => $pages,
            'columns' => [
                'title' => function (Page $page) use ($app) {
                    return '<a target="_blank" href="' .$app->url('page',
                        ['url' => $page->getSlug()]
                    ) . '">' . $page->getTitle() . '</a>';
                },
                'slug',
                'edit' => function(Page $page) use ($app) {
                    return '<a href="' .$app->url('edit_page',
                        ['id' => $page->getId()]
                    ) . '">Edit</a>';
                },
            ]
        ]);
    }

    public function editPageAction (Application $app, Request $request, $id)
    {
        $page = null;

        if ($id) {
            $page = $app['em']->find('CMSilex\Entities\Page', $id);
        }

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

                return $app->redirect($app->url('list_pages'));
            }
        }

        return $app->render('admin/page/edit.html.twig', [
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