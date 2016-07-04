<?php

namespace Baikal\Domain;

use Baikal\Domain\User\Username;

interface UserRepository
{
    /**
     * @return User[]
     */
    function all();

    /**
     * @return int
     */
    function count();

    /**
     * @param Username $username
     * @return User|null
     */
    function getByUsername(Username $username);

    /**
     * @param User $user
     * @return void
     */
    function persist(User $user);

    /**
     * @param User $user
     * @return void
     */
    function remove(User $user);
}
