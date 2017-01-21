<?php

namespace CMSilex\ControllerProviders;

use CMSilex\Components\CMSEntity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function listEntityAction (CMSEntity $cmsEntity, Application $app, Request $request){
        $pageNumber = $request->query->has('page') ? $request->query->get('page') : 1;
        $limit = $request->query->has('limit') ? $request->query->get('limit') : $cmsEntity->getDefaultPageLimit();

        $qb = $app['em']->getRepository($cmsEntity->getClass())->createQueryBuilder('e');

        if ($limit && $limit >= 0) {
            $qb->setMaxResults($limit);
            $qb->setFirstResult(($pageNumber - 1) * $limit);
        } else {
            $limit = null;
            $pageNumber = 1;
        }

        $paginator = new Paginator($qb);

        $entities = [];

        foreach ($paginator as $entity)
        {
            $entities[] = $entity;
        }

        $resultCount = count($paginator);
        $totalPages = $limit && $limit <= $resultCount ? ceil($resultCount/$limit) : 1;

        return $app->render('admin/list.html.twig', [
            'columns' => $cmsEntity->getColumns(),
            'items' => $entities,
            'cmsEntity' => $cmsEntity,
            'heading' => ucwords( $cmsEntity ),
            'resultCount' => $resultCount,
            'currentPage' => $pageNumber,
            'limit' => $limit,
            'totalPages' => $totalPages
        ]);
    }
}