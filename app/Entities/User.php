<?php

namespace CMSilex\Entities;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/**
 * @Entity
 */
class User implements AdvancedUserInterface
{
    /** @Column(type="integer") @Id @GeneratedValue */
    protected $id;

    /** @Column(type="boolean") */
    protected $accountNonExpired;

    /** @Column(type="boolean") */
    protected $accountNonLocked;

    /** @Column(type="boolean") */
    protected $credentialsNonExpired;

    /** @Column(type="boolean") */
    protected $enabled;

    /** @Column(type="simple_array") */
    protected $roles;

    /** @Column */
    protected $password;

    /** @Column(nullable=true) */
    protected $salt;

    /** @Column(unique=true) */
    protected $username;

    public function isAccountNonExpired()
    {
        return $this->accountNonExpired;
    }

    public function isAccountNonLocked()
    {
        return $this->accountNonLocked;
    }

    public function isCredentialsNonExpired()
    {
        return $this->credentialsNonExpired;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @param mixed $accountNonExpired
     */
    public function setAccountNonExpired($accountNonExpired)
    {
        $this->accountNonExpired = $accountNonExpired;
    }

    /**
     * @param mixed $accountNonLocked
     */
    public function setAccountNonLocked($accountNonLocked)
    {
        $this->accountNonLocked = $accountNonLocked;
    }

    /**
     * @param mixed $credentialsNonExpired
     */
    public function setCredentialsNonExpired($credentialsNonExpired)
    {
        $this->credentialsNonExpired = $credentialsNonExpired;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param mixed $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }



}