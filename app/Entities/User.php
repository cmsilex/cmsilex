<?php

namespace CMSilex\Entities;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/** @Entity */
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

    /** @Column */
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
        return $this->salt;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

}