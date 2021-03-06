<?php

namespace CMSilex\Entities;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\PreUpdateEventArgs;

/**
 * @Entity
 * @InheritanceType("SINGLE_TABLE")
 * @HasLifecycleCallbacks
 * @EntityListeners({"CMSilex\Events\Listeners\BlogItemEventListener"})
 */
class BlogItem
{
    /** @Column(type="integer") @Id @GeneratedValue */
    protected $id;

    /** @Column */
    protected $title;

    /** @Column() */
    protected $slug;

    /** @Column(nullable=true) */
    protected $subtitle;

    /** @Column(type="text", nullable=true) */
    protected $content;

    /** @Column(type="boolean",options={"default":false}) */
    protected $deleted;

    /** @ManyToOne(targetEntity="CMSilex\Entities\Page", inversedBy="childPages") */
    protected $parentPage;

    /** @OneToMany(targetEntity="CMSilex\Entities\Page", mappedBy="parentPage", cascade={"all"}) */
    protected $childPages;

    /** @Column(type="datetime") */
    protected $created;

    /** @Column */
    protected $template;

    /**
     * @OneToMany(targetEntity="CMSilex\Entities\CMSField", mappedBy="blogItem", indexBy="att", fetch="EAGER", cascade={"all"}, orphanRemoval=true)
     */
    protected $fields;

    public function __construct()
    {
        $this->childPages = new ArrayCollection();
        $this->fields = new ArrayCollection();
        $this->deleted = false;
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
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param mixed $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
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
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param mixed $subtitle
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    public function isDeleted()
    {
        return $this->deleted;
    }

    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
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

    public function addField(CMSField $field)
    {
        $this->fields[$field->getAtt()] = $field;
        $field->setBlogItem($this);
    }

    public function removeField(CMSField $field)
    {
        $field->setBlogItem(null);
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getField($att)
    {
        return $this->fields->get($att);
    }

    /**
     * @return mixed
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }
}