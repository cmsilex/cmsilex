<?php

namespace CMSilex\ControllerProviders;

use CMSilex\Forms\Types\PageType;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\Finder\SplFileInfo;
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

        $controller->get('/pages/publish', 'CMSilex\ControllerProviders\PageController::publishAction')
            ->bind('publish')
        ;

        $controller->get('/pages/new', 'CMSilex\ControllerProviders\PageController::editPageAction')
            ->method('POST|GET')
            ->bind('new_page')
            ->value('url', null)
        ;

        $controller->match('/pages/{url}', 'CMSilex\ControllerProviders\PageController::editPageAction')
            ->method('POST|GET')
            ->assert('url', '.+')
            ->bind('edit_page')
        ;

        return $controller;
    }

    public function publishAction (Application $app, Request $request)
    {
        $inputDir = $app['config']['pages_dir'];
        $outputDir = "../public/";

        try {
            $app['rst']->build($inputDir, $outputDir);
        } catch (\Exception $e)
        {
            dump($e);
            exit;
        }


        return $app->redirect($app->url('list_pages'));
    }

    public function listPagesAction (Application $app, Request $request)
    {

        $pagesDir = $app['config']['pages_dir'];
        $finder = $app['finder']->in($pagesDir);

        $pages = iterator_to_array($finder->files());

        return $app->render('admin/list.html.twig', [
            'rows' => $pages,
            'columns' => [
                'relativePathName',
                'edit' => function(SplFileInfo $fileInfo) use ($app) {
                    return '<a href="' .$app->url('edit_page',
                        ['url' => $fileInfo->getRelativePathname()]
                    ) . '">Edit</a>';
                },
            ]
        ]);
    }

    public function editPageAction (Application $app, Request $request, $url)
    {

        $pagesDir = $app['config']['pages_dir'];

        $fileDir = $pagesDir . $url;

        $page = [];

        if ($app['filesystem']->exists($fileDir)) {
            $fileContents = file_get_contents($fileDir);
            $page['content'] = $fileContents;
            $page['slug'] = $url;
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


                $newUrl = $page['slug'];

                if (strcmp($url, $newUrl) !== 0)
                {
                    $app['filesystem']->remove($fileDir);
                }


                $newFileDir = $pagesDir . $newUrl;

                $i = strrpos($newFileDir, '/');

                $folderDir = substr($newFileDir, 0, $i);

                if (!$app['filesystem']->exists($folderDir))
                {
                    $app['filesystem']->mkdir($folderDir);
                }

                file_put_contents($newFileDir, $page['content']);

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