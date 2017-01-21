<?php

namespace CMSilex\Entities;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @Entity
 */
class Category
{
    /** @Column(type="integer") @Id @GeneratedValue */
    protected $id;

    /** @Column(unique=true) */
    protected $name;
    
    /** @ManyToMany(targetEntity="CMSilex\Entities\Post", mappedBy="categories") */
    protected $posts;

    /** @Column(type="boolean") */
    protected $isPrivate;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
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
     * @return mixed
     */
    public function isPrivate()
    {
        return $this->isPrivate;
    }

    /**
     * @param mixed $isPrivate
     */
    public function setIsPrivate($isPrivate)
    {
        $this->isPrivate = $isPrivate;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * @param mixed $posts
     */
    public function setPosts($posts)
    {
        $this->posts = $posts;
    }

    public function addPost(Post $post)
    {
        $this->posts->add($post);
    }

    public function removePost(Post $post)
    {
        $this->posts->removeElement($post);
    }
}