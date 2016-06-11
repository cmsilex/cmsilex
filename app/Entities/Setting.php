<?php

namespace CMSilex\Entities;


/**
 * @Entity
 */
class Setting
{
    /** @Id @Column */
    protected $att;

    /** @Column */
    protected $val;

    public function __construct($att = null, $val = null)
    {
        $this->setAtt($att);
        $this->setVal($val);
    }

    public function __toString()
    {
        return $this->getVal();
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