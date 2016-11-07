<?php

namespace CMSilex\ServiceProviders;

use CMSilex\Components\CMS;
use CMSilex\Components\CMSEntity;
use CMSilex\Entities\Category;
use CMSilex\Entities\Menu;
use CMSilex\Entities\Page;
use CMSilex\Entities\Post;
use CMSilex\Forms\Types\CategoryType;
use CMSilex\Forms\Types\MenuType;
use CMSilex\Forms\Types\PageType;
use CMSilex\Forms\Types\PostType;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;


class CMSServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['cms'] = function () use ($container) {
            $cms = new CMS($container['em']);

            $postCmsEntity = new CMSEntity(Post::class, PostType::class);
            $postCmsEntity->addColumn('title', 'title');
            $postCmsEntity->addColumn('slug', 'slug');
            $cms->addCMSEntity($postCmsEntity);

            $pageCmsEntity = new CMSEntity(Page::class, PageType::class);
            $pageCmsEntity->addColumn('title', 'title');
            $pageCmsEntity->addColumn('slug', 'slug');
            $cms->addCMSEntity($pageCmsEntity);
            
            $menuCMSEntity = new CMSEntity(Menu::class, MenuType::class);
            $menuCMSEntity->addColumn('name', 'name');
            $cms->addCMSEntity($menuCMSEntity);

            $categoryCmsEntity = new CMSEntity(Category::class, CategoryType::class);
            $categoryCmsEntity->addColumn('Name', 'name');
            $cms->addCMSEntity($categoryCmsEntity);

            return $cms;
        };
    }
}