<?php

namespace CMSilex\Events\Listeners;

use CMSilex\Entities\BlogItem;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class BlogItemEventListener
{
    public function prePersist(BlogItem $blogItem)
    {
        $blogItem->setCreated(new \DateTime());
    }

}