<?php

namespace CMSilex\Entities;

use Doctrine\Common\Collections\ArrayCollection;

/** @Entity */
class Menu
{
    /** @Column(type="integer") @Id @GeneratedValue */
    protected $id;

    /** @Column */
    protected $name;

    /** @OneToMany(targetEntity="CMSilex\Entities\MenuItem", mappedBy="menu", indexBy="position", cascade={"all"}) */
    protected $menuItems;

    public function __construct()
    {
        $this->menuItems = new ArrayCollection();
    }

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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    public function addMenuItem(MenuItem $menuItem) {
        $position = count($this->menuItems);
        $menuItem->setPosition($position);
        $this->menuItems[$position] = $menuItem;
        $menuItem->setMenu($this);
    }

    public function removeMenuItem(MenuItem $menuItem)
    {
        $this->menuItems->removeElement($menuItem);
        $menuItem->setMenu(null);
    }

    public function getMenuItem($position)
    {
        if (!isset($this->menuItems[$position])) {
            throw new \InvalidArgumentException("No menu item at specified position.");
        }

        return $this->menuItems[$position];
    }

    /**
     * @return mixed
     */
    public function getMenuItems()
    {
        return $this->menuItems;
    }

    /**
     * @param mixed $menuItems
     */
    public function setMenuItems($menuItems)
    {
        $this->menuItems = $menuItems;
    }
}