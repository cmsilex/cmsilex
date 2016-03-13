<?php

namespace CMSilex\Entities;

/** @Entity */
class Page
{
    /** @Column(type="integer") @Id @GeneratedValue */
    protected $id;

    /** @Column */
    protected $title;

    /** @Column */
    protected $slug;

    /** @ManyToOne(targetEntity="CMSilex\Entities\Page", mappedBy="childPages") */
    protected $parentPage;

    /** @OneToMany(targetEntity="CMSilex\Entities\Page", inversedBy="parentPage", cascade={"all"}) */
    protected $childPages;
}