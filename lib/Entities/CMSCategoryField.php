<?php

namespace CMSilex\Entities;

/** @Entity */
class CMSCategoryField extends CMSField
{
    /** @ManyToOne(targetEntity="CMSilex\Entities\Category") */
    protected $category;

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }
}