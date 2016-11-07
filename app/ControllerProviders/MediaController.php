<?php

namespace CMSilex\ControllerProviders;

use CMSilex\Entities\File;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

class MediaController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];

        $controller->get('/', 'CMSilex\ControllerProviders\MediaController::indexAction')
        ->bind('media')
        ;

        $controller->post('/upload', 'CMSilex\ControllerProviders\MediaController::uploadAction')
        ->bind('upload')
        ;

        $controller->get('/get-media', 'CMSilex\ControllerProviders\MediaController::getMediaAction')
        ->bind('get_media')
        ;

        $controller->post('/delete', 'CMSilex\ControllerProviders\MediaController::deleteMediaAction')
        ->bind('delete_media')
        ;

        return $controller;
    }

    public function indexAction (Application $app)
    {
        $files = $app['em']->getRepository('CMSilex\Entities\File')->findAll();

        $form = $app['form.factory']->createNamedBuilder(null);

        $form
            ->add('file', FileType::class)
            ->add('upload', SubmitType::class)
            ->setAction($app->path('upload'))
        ;

        return $app->render('media/index.html.twig', [
            'form' => $form->getForm()->createView(),
            'files' => $files,
            'heading' => 'Media'
        ]);
    }

    public function getMediaAction (Application $app, Request $request)
    {
        $files = $app['em']->getRepository('CMSilex\Entities\File')->findAll();
        $files = $app['serializer']->normalize(compact('files'));
        return $app->json($files);
    }

    public function deleteMediaAction (Application $app, Request $request)
    {
        $id = $request->request->get('id');
        $media = $app['em']->find('CMSilex\Entities\File', $id);
        $uploadsDir = "./uploads/";
        $app['filesystem']->remove($uploadsDir . $media->getPath());
        $app['em']->remove($media);
        $app['em']->flush();

        return $app->json([
            'success' => true,
            'file' => $app['serializer']->normalize($media)
        ]);
    }

    public function uploadAction (Application $app, Request $request)
    {
        $uploadedFile = $request->files->get('file');

        $newFile = new File();
        $newFile->setName($uploadedFile->getClientOriginalName());

        $fileName = md5(uniqid()) . "." . $uploadedFile->guessExtension();

        // Move the file to the directory where brochures are stored
        $uploadsDir = "./uploads/";

        $uploadedFile->move($uploadsDir, $fileName);

        $newFile->setPath($fileName);

        $app['em']->persist($newFile);
        $app['em']->flush();

        return $app->json([
            'success' => true,
            'file' => $app['serializer']->normalize($newFile)
        ]);
    }
}