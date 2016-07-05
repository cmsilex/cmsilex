<?php

namespace CMSilex\Entities;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\PreUpdateEventArgs;

/**
 * @Entity
 * @InheritanceType("SINGLE_TABLE")
 */
class CMSField
{
    /** @ManyToOne(targetEntity="CMSilex\Entities\BlogItem") @Id */
    protected $blogItem;

    /** @Column @Id */
    protected $att;

    /** @Column(nullable=true, type="text") */
    protected $val;

    public function __toString()
    {
        return $this->getVal();
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
    public function getAtt()
    {
        return $this->att;
    }

    /**
     * @param mixed $att
     */
    public function setAtt($att)
    {
        $this->att = $att;
    }

    /**
     * @return mixed
     */
    public function getVal()
    {
        return $this->val;
    }

    /**
     * @param mixed $val
     */
    public function setVal($val)
    {
        $this->val = $val;
    }
}