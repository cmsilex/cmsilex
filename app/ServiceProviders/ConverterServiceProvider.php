<?php

namespace CMSilex\ServiceProviders;

use CMSilex\Entities\Menu;
use Doctrine\ORM\EntityNotFoundException;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class ConverterServiceProvider implements ServiceProviderInterface
{
    protected $em;

    public function register(Container $container)
    {
        $this->em = $container['em'];
        $container['converter'] = $this;
    }

    public function convertEntity($menu, Request $request) {
        $id = $request->get('id');
        if (!is_null($id))
        {
            $menu = $this->em->find('CMSilex\Entities\Menu', $id);
            if ($menu)
            {
                return $menu;
            } else {
                throw new EntityNotFoundException();
            }
        } else {
            return new Menu();
        }
    }
}