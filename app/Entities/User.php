<?php

namespace CMSilex\Entities;

use Symfony\Component\Security\Core\User\AdvancedUserInterface;

/** @Entity */
class User extends AdvancedUserInterface
{
    public function isAccountNonExpired() {

    }
}