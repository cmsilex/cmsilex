<?php

namespace CMSilex\Entities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 */
class Post extends BlogItem
{
    /** @ManyToMany(targetEntity="CMSilex\Entities\Category", inversedBy="posts", cascade={"persist"}) */
    protected $categories;

    public function __construct()
    {
        parent::__construct();
        $this->categories = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param mixed $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    public function addCategory(Category $category)
    {
        $this->categories->add($category);
    }

    public function removeCategory(Category $category)
    {
        $this->categories->removeElement($category);
    }
}