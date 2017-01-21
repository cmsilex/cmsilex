<?php

namespace CMSilex\Components;

use CMSilex\Loaders\YamlFileLoader;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;

class ThemeComponent {
    
    protected $em;
    protected $twig;
    protected $theme;
    protected $themeDir;
    protected $themes;
    
    public function __construct(EntityManager $em, \Twig_Environment $twig, $themeDir)
    {
        $this->em = $em;
        $this->twig = $twig;
        $this->themeDir = $themeDir;

        $locator = new FileLocator($this->themeDir);

        $loaderResolver = new LoaderResolver([new YamlFileLoader($locator)]);

        $delegatingLoader = new DelegatingLoader($loaderResolver);

        $themeConfig = $delegatingLoader->load('theme.yml');

        $this->templates = $themeConfig['templates'];
    }

    public function menu ($menuName) {
        $menu = $this->em->getRepository('CMSilex\Entities\Menu')->findOneByName($menuName);
        return $this->twig->render('theme-component/menu.html.twig', [
            'menu' => $menu
        ]);
    }

    public function getTemplates ()
    {
        return $this->templates;
    }

    public function getTemplate ($key)
    {
        return $this->templates[$key];
    }
}