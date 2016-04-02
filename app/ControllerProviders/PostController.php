<?php

namespace CMSilex\ControllerProviders;

use CMSilex\Entities\Post;
use CMSilex\Forms\Types\PostType;
use Silex\Application;
use Silex\ControllerProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class PostController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];

        $controller->get('/posts', 'CMSilex\ControllerProviders\PostController::listPostsAction')
            ->bind('list_posts')
        ;

        $controller->get('/posts/new', 'CMSilex\ControllerProviders\PostController::editPostAction')
            ->method('POST|GET')
            ->bind('new_post')
            ->value('id', null)
        ;

        $controller->match('/posts/{id}', 'CMSilex\ControllerProviders\PostController::editPostAction')
            ->method('POST|GET')
            ->bind('edit_post')
        ;

        return $controller;
    }

    public function listPostsAction (Application $app, Request $request)
    {
        $posts = $app['em']->getRepository('CMSilex\Entities\Post')->findAll();

        return $app->render('admin/post/list.html.twig', [
            'rows' => $posts,
            'columns' => [
                'title',
                'slug',
                'edit' => function(Post $post) use ($app) {
                    return '<a href="' .$app->url('edit_post',
                        ['id' => $post->getId()]
                    ) . '">Edit</a>';
                },
            ]
        ]);
    }

    public function editPostAction (Application $app, Request $request, $id)
    {
        $post = null;

        if ($id) {
            $post = $app['em']->find('CMSilex\Entities\Post', $id);
        }

        $form = $app['form.factory']->createBuilder(PostType::class, $post)
            ->add('save', SubmitType::class)
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isSubmitted())
        {
            if ($form->isValid())
            {
                $post = $form->getData();

                $app['em']->persist($post);
                $app['em']->flush();

                return $app->redirect($app->url('list_posts'));
            }
        }

        return $app->render('admin/post/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    public function deletePostAction (Application $app, Request $request, $id)
    {
        $post = $app['em']->find('CMSilex\Entities\Post', $id);

        if ($post)
        {
            $post->setDeleted(true);
            $app['em']->persist($post);
            $app['em']->flush();
        }

        return $app->redirect($app->url('list_posts'));
    }
}