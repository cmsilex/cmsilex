<?php

namespace CMSilex\ServiceProviders;

use CMSilex\Components\ThemeComponent;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;


class ThemeServiceProvider implements ServiceProviderInterface
{
    protected $em;

    public function register(Container $container)
    {
        $container['dir.theme'] = $container['dir.base'] . "/themes/" . $container['config']['theme'];

        $container['theme'] = function() use ($container) {
            return new ThemeComponent($container['em'], $container['twig'], $container['dir.theme']);
        };

    }
}