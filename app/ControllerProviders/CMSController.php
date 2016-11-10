<?php

namespace CMSilex\ControllerProviders;

use CMSilex\Components\CMSEntity;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class CMSController implements ControllerProviderInterface
{
    public function connect(Application $app) {
        $controller = $app['controllers_factory'];

        $controller->match('/{entityName}/edit/{id}', 'CMSilex\ControllerProviders\CMSController::editEntityAction')
        ->value('id', null)
        ->bind('cms_edit')
        ->method('POST|GET')
        ->convert('entity', 'cms:convertEntity')
        ->convert('cmsEntity', 'cms:convertCMSEntity')
        ;

        $controller->post('/{entityName}/delete/{id}', 'CMSilex\ControllerProviders\CMSController::deleteEntityAction')
        ->bind('cms_delete')
        ->convert('entity', 'cms:convertEntity')
        ;

        $controller->get('/{entityName}/list', 'CMSilex\ControllerProviders\CMSController::listEntityAction')
        ->bind('cms_list')
        ->convert('cmsEntity', 'cms:convertCMSEntity')
        ;

        return $controller;
    }
    
    public function editEntityAction (CMSEntity $cmsEntity, $entity, Application $app, Request $request){

        $entityFormType = $cmsEntity->getFormType();
        $form = $app['form.factory']->create($entityFormType, $entity);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $isNewEntity = ($entity->getId() == null);
            $entity = $form->getData();

            $app['em']->persist($entity);
            $app['em']->flush();

            if ($isNewEntity)
            {
                return $app->redirect($app->url('cms_list', ['entityName' => $cmsEntity]));
            } else {
                return $app->redirect($app->url('cms_edit', ['entityName' => $cmsEntity, 'id' => $entity->getId()]));
            }
        }

        return $app->render('admin/edit.html.twig', [
            'form' => $form->createView(),
            'heading' => ucwords( $cmsEntity )
        ]);
    }

    public function deleteEntityAction ($entityName, $entity, Application $app, Request $request)
    {
        $app['em']->remove($entity);
        $app['em']->flush();
        return $app->redirect($app->url('cms_list', ['entityName' => $entityName]));
    }

    public function listEntityAction (CMSEntity $cmsEntity, Application $app){
        $entities = $app['em']->getRepository($cmsEntity->getClass())->findAll();

        return $app->render('admin/list.html.twig', [
            'columns' => $cmsEntity->getColumns(),
            'items' => $entities,
            'cmsEntity' => $cmsEntity,
            'heading' => ucwords( $cmsEntity )
        ]);
    }
}