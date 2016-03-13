<?php

namespace CMSilex\Entities;
use Doctrine\Common\Collections\ArrayCollection;

/** @Entity */
class Page
{
    /** @Column(type="integer") @Id @GeneratedValue */
    protected $id;

    /** @Column */
    protected $title;

    /** @Column */
    protected $slug;

    /** @ManyToOne(targetEntity="CMSilex\Entities\Page", inversedBy="childPages") */
    protected $parentPage;

    /** @OneToMany(targetEntity="CMSilex\Entities\Page", mappedBy="parentPage", cascade={"all"}) */
    protected $childPages;

    public function __construct()
    {
        $this->childPages = new ArrayCollection();
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getParentPage()
    {
        return $this->parentPage;
    }

    /**
     * @param mixed $parentPage
     */
    public function setParentPage($parentPage)
    {
        $this->parentPage = $parentPage;
    }

    /**
     * @return mixed
     */
    public function getChildPages()
    {
        return $this->childPages;
    }

    /**
     * @param mixed $childPages
     */
    public function setChildPages($childPages)
    {
        $this->childPages = $childPages;
    }

    public function addChildPage(Page $page)
    {
        $this->childPages->add($page);
        $page->setParentPage($this);
    }

    public function removeChildPage(Page $page)
    {
        $this->childPages->remove($page);
        $page->setParentPage(null);
    }
}