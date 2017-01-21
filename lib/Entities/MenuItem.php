<?php

namespace CMSilex\Entities;

/** @Entity */
class MenuItem
{
    /** @Column(type="integer") @GeneratedValue @Id */
    protected $id;

    /** @Column */
    protected $label;
    
    /** @ManyToOne(targetEntity="CMSilex\Entities\Menu", inversedBy="menuItems") */
    protected $menu;

    /** @ManyToOne(targetEntity="CMSilex\Entities\BlogItem") */
    protected $blogItem;

    /** @Column(type="integer") */
    protected $position;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return mixed
     */
    public function getBlogItem()
    {
        return $this->blogItem;
    }

    /**
     * @param mixed $blogItem
     */
    public function setBlogItem($blogItem)
    {
        $this->blogItem = $blogItem;
    }

    /**
     * @return mixed
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * @param mixed $menu
     */
    public function setMenu($menu)
    {
        $this->menu = $menu;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param mixed $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }
}