<?php

namespace CMSilex\ServiceProviders;

use CMSilex\Entities\Menu;
use Doctrine\ORM\EntityNotFoundException;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Request;

class ConverterServiceProvider implements ServiceProviderInterface
{
    protected $em;

    public function register(Application $app)
    {
        $this->em = $app['em'];
        $app['converter'] = $this;
    }

    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
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