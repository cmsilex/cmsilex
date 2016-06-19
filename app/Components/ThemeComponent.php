<?php

namespace CMSilex\Components;

use Doctrine\ORM\EntityManager;

class ThemeComponent {
    
    protected $em;
    protected $twig;
    
    public function __construct(EntityManager $em, \Twig_Environment $twig)
    {
        $this->em = $em;
        $this->twig = $twig;
    }

    public function menu ($menuName) {
        $menu = $this->em->getRepository('CMSilex\Entities\Menu')->findOneByName($menuName);
        return $this->twig->render('theme-component/menu.html.twig', [
            'menu' => $menu
        ]);
    }
}