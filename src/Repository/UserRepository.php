<?php

namespace Baikal\Repository;

use Baikal\Domain\User;
use Generator;
use PDO;

/**
 * UserRepository implementation using PDO
 */
final class UserRepository
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * The authentication realm. Used to hash the users' password.
     *
     * @var string
     */
    private $authRealm;

    /**
     * @param PDO $pdo
     * @param string $authRealm
     */
    function __construct(PDO $pdo, $authRealm) {
        $this->pdo = $pdo;
        $this->authRealm = $authRealm;
    }

    /**
     * @return User[]|Generator
     */
    function all()
    {
        if ($this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') {
            $userQuery = $this->pdo->query("SELECT username as userName, email, displayname as displayName FROM users LEFT JOIN principals ON principals.uri = CONCAT('principals/', username)");
        } else {
            $userQuery = $this->pdo->query("SELECT username as userName, email, displayname as displayName FROM users LEFT JOIN principals ON principals.uri = ('principals/' || username)");
        }

        $users = [];
        foreach ($userQuery->fetchAll(\PDO::FETCH_ASSOC) as $userData) {
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
     * @param string $userName
     * @throw \InvalidArgumentException
     * @return User
     */
    function getByUserName($userName) {

        $userQuery = $this->pdo->query("SELECT email, displayname as displayName, ? as userName FROM principals WHERE uri = ?");
        $userQuery->execute([
            $userName,
            'principals/' . $userName
        ]);

        $userData = $userQuery->fetch(PDO::FETCH_ASSOC);
        if ($userData === false) {
            throw new \InvalidArgumentException('User with name: ' . $userName . ' not found');
        }

        return User::fromArray($userData);
    }

    /**
     * Creates a new User
     */
    function create(User $user) {
       
        $user->validateForCreate();

        $this->pdo->beginTransaction();

        $insert = "INSERT INTO principals (uri, email, displayname) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($insert);

        $stmt->execute([
            $user->getPrincipalUri(),
            $user->email,
            $user->displayName
        ]);

        // proxy principals
        $stmt->execute([$user->getPrincipalUri() . '/calendar-proxy-read', null, null]);
        $stmt->execute([$user->getPrincipalUri() . '/calendar-proxy-write', null, null]);

        $insert2 = "INSERT INTO users (username, digesta1) VALUES (?, ?)";
        $stmt2 = $this->pdo->prepare($insert2);

        $stmt2->execute([
            $user->userName,
            md5($user->userName . ':' . $this->authRealm . ':' . $user->password)
        ]);

        $this->pdo->commit();

    }

    /**
     * @param User $user
     * @return void
     */
    function update(User $user) {

        $user->validateForUpdate();

        $this->pdo->beginTransaction();

        $update = "UPDATE principals SET email = ?, displayname = ? WHERE uri = ?";
        $stmt = $this->pdo->prepare($update);
        $stmt->execute([
            $user->email,
            $user->displayName,
            $user->getPrincipalUri()
        ]);

        if ($user->password) {
            $update3 = "UPDATE users SET digesta1 = ? WHERE username = ?";
            $stmt3 = $this->pdo->prepare($update3);
            $stmt3->execute([
                md5($user->userName . ':' . $this->authRealm . ':' . $user->password),
                $user->username,
            ]);
        }

        $this->pdo->commit();
    }

    /**
     * @param User $user
     */
    function remove(User $user) {
        throw new \Exception('Not implemented!');
    }
}
