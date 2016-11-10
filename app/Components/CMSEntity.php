<?php

namespace CMSilex\Components;

class CMSEntity
{
    protected $class;
    protected $columns;
    protected $formType;
    protected $isEditable;
    protected $isCreatable;
    protected $isDeletable;
    protected $defaultPageLimit;

    public function __construct($class, $formType, $isEditable = true, $isCreatable = true, $isDeletable = true, $defaultPageLimit = 15)
    {
        $this->class = $class;
        $this->columns = [];
        $this->formType = $formType;
        $this->isEditable = $isEditable;
        $this->isCreatable = $isCreatable;
        $this->isDeletable = $isDeletable;
        $this->defaultPageLimit = $defaultPageLimit;
    }

    /**
     * @return mixed
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param mixed $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    public function addColumn($label, $attr)
    {
        $this->columns[$label] = $attr;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getFormType()
    {
        return $this->formType;
    }

    /**
     * @return mixed
     */
    public function getIsEditable()
    {
        return $this->isEditable;
    }

    /**
     * @param mixed $isEditable
     */
    public function setIsEditable($isEditable)
    {
        $this->isEditable = $isEditable;
    }

    /**
     * @return mixed
     */
    public function getIsCreatable()
    {
        return $this->isCreatable;
    }

    /**
     * @param mixed $isCreatable
     */
    public function setIsCreatable($isCreatable)
    {
        $this->isCreatable = $isCreatable;
    }

    /**
     * @return mixed
     */
    public function getIsDeletable()
    {
        return $this->isDeletable;
    }

    /**
     * @param mixed $isDeletable
     */
    public function setIsDeletable($isDeletable)
    {
        $this->isDeletable = $isDeletable;
    }

    /**
     * @return int
     */
    public function getDefaultPageLimit()
    {
        return $this->defaultPageLimit;
    }

    /**
     * @param int $defaultPageLimit
     */
    public function setDefaultPageLimit($defaultPageLimit)
    {
        $this->defaultPageLimit = $defaultPageLimit;
    }

    public function __toString()
    {
        $namespaceArray = explode('\\', $this->getClass());
        $entityName = array_pop($namespaceArray);
        return strtolower($entityName);
    }
}