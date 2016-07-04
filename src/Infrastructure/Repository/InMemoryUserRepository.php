<?php

namespace Baikal\Infrastructure\Repository;

use Baikal\Domain\User;
use Baikal\Domain\UserRepository;

class InMemoryUserRepository implements UserRepository
{
    /**
     * @var User[]
     */
    private $users;

    /**
     * @return User[]
     */
    function all()
    {
        return $this->users;
    }

    /**
     * @return int
     */
    function count()
    {
        return count($this->users);
    }

    /**
     * @param \Baikal\Domain\User\Username $username
     * @return \Baikal\Domain\User|null
     */
    function getByUsername(\Baikal\Domain\User\Username $username)
    {
        foreach ($this->users as $user) {
            if ($user->username() === $username) {
                return $user;
            }
        }
        return null;
    }

    /**
     * @param User $user
     */
    function persist(User $user)
    {
        $userUpdated = false;
        foreach ($this->users as $index => $memoryUser) {
            if ($memoryUser->username() === $user->username()) {
                $this->users[$index] = $user;
                $userUpdated = true;
            }
        }

        if ($userUpdated === false) {
            $this->users[] = $user;
        }
    }

    /**
     * @param User $user
     */
    function remove(User $user)
    {
        foreach ($this->users as $index => $memoryUser) {
            if ($memoryUser->username() === $user->username()) {
                unset($this->users[$index]);
            }
        }
    }
}
