<?php

namespace Baikal\Infrastructure\Repository;

use Baikal\Domain\User;
use Baikal\Domain\User\Username;
use Baikal\Domain\UserRepository;
use Generator;
use PDO;

/**
 * UserRepository implementation using PDO
 */
final class PdoUserRepository implements UserRepository
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @param PDO $pdo
     */
    function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return User[]|Generator
     */
    function all()
    {
        $userQuery = $this->pdo->query("SELECT * FROM users");

        if ($userQuery === false) {
            // http://php.net/manual/en/pdo.errorinfo.php#refsect1-pdo.errorinfo-returnvalues
            throw new \PDOException($this->pdo->errorInfo()[2]);
        }

        $users = [];
        foreach ($userQuery->fetchAll() as $userData) {
            $users[] = User::fromArray($userData);
        }
        return $users;
    }

    /**
     * @return int
     */
    function count()
    {
        $statement = $this->pdo->query("SELECT COUNT(1) FROM `users`");
        return (int)$statement->fetchColumn();
    }

    /**
     * @param Username $username
     * @return User|null
     */
    function getByUsername(Username $username)
    {
        $userQuery = $this->pdo->query("SELECT * FROM users WHERE username = :username");
        $userQuery->bindParam('username', $username, PDO::PARAM_STR);
        $userQuery->execute();

        $userObject = $userQuery->fetchObject();
        if ($userObject === false) {
            return null;
        }

        return User::fromArray((array)$userObject);
    }

    /**
     * @param User $user
     * @return void
     */
    function persist(User $user)
    {
        if ($this->getByUsername($user->username()) instanceof User) {
            // TODO: Update
        } else {
            $insert = "INSERT INTO users (
                           username,
                           displayName,
                           email,
                           password
                       ) VALUES (
                           :username,
                           :displayName,
                           :email,
                           :password
                       )";

            $statement = $this->pdo->prepare($insert);
            $statement->bindParam('username', $user->username()->__toString(), PDO::PARAM_STR);
            $statement->bindParam('displayName', $user->displayName()->__toString(), PDO::PARAM_STR);
            $statement->bindParam('email', $user->email()->__toString(), PDO::PARAM_STR);
            $statement->bindParam('password', $user->password()->__toString(), PDO::PARAM_STR);

            $statement->execute();
        }
    }

    /**
     * @param User $user
     */
    function remove(User $user)
    {
        $statement = $this->pdo->prepare("DELETE FROM `users` WHERE `username` = :username LIMIT 1");
        $statement->bindParam('username', $user->username());
        $statement->execute();
    }
}
