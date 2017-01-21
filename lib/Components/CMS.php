<?php

namespace CMSilex\Components;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CMS
{
    protected $entities;
    protected $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->entities = new ArrayCollection();
    }

    public function addCMSEntity(CMSEntity $cmsEntity) {
        $this->entities[(string) $cmsEntity] = $cmsEntity;
    }

    public function getCMSEntity($key)
    {
        if ($this->entities->containsKey($key)) {
            return $this->entities->get($key);
        } else {
            throw new NotFoundHttpException();
        }
    }

    public function getCMSEntities ()
    {
        return $this->entities;
    }

    public function convertEntity ($entity, Request $request) {
        $entityName = $request->get('entityName');
        $id = $request->get('id');

        $cmsEntity = $this->getCMSEntity($entityName);
        $entityClass = $cmsEntity->getClass();

        $entity = is_null($id) ? new $entityClass : $this->em->find($entityClass, $id);
        return $entity;
    }

    public function convertCMSEntity ($cmsEntity, Request $request) {
        $entityName = $request->get('entityName');
        $cmsEntity = $this->getCMSEntity($entityName);
        return $cmsEntity;
    }
}