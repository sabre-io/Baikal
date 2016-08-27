<?php

namespace Baikal\Service;

use Baikal\Domain\User;
use Generator;
use PDO;
use Sabre\CalDAV\Backend\BackendInterface as CalBackend;
use Sabre\CardDAV\Backend\BackendInterface as CardBackend;

/**
 * UserRepository implementation using PDO
 */
class UserService {

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
     * @var CalBackend
     */
    private $calBackend;

    /**
     * @var CardBackend
     */
    private $cardBackend;

    /**
     * @param PDO $pdo
     * @param string $authRealm
     */
    function __construct(PDO $pdo, $authRealm, CalBackend $calBackend, CardBackend $cardBackend) {

        $this->pdo = $pdo;
        $this->authRealm = $authRealm;
        $this->calBackend = $calBackend;
        $this->cardBackend = $cardBackend;

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
                $user->userName,
            ]);
        }

        $this->pdo->commit();
    }

    /**
     * @param User $user
     */
    function remove(User $user) {

        $this->pdo->beginTransaction();

        $principalUri = $user->getPrincipalUri();
        // Delete all calendars
        foreach ($this->calBackend->getCalendarsForUser($principalUri) as $calendarInfo) {
            $this->calBackend->deleteCalendar($calendarInfo['id']);
        }
        // Delete all addressbooks
        foreach ($this->cardBackend->getAddressBooksForUser($principalUri) as $addressBookInfo) {
            $this->cardBackend->deleteAddressBook($addressBookInfo['id']);
        }

        // Get a list of principal ids
        $relevantPrincipalsStmt = $this->pdo->prepare('SELECT id FROM principals WHERE uri = ? OR uri = ? OR uri = ?');

        $relevantPrincipalsStmt->execute([
            $principalUri,
            $principalUri . '/calendar-proxy-read',
            $principalUri . '/calendar-proxy-write',
        ]);
        $relevantPrincipals = $relevantPrincipalsStmt->fetchAll(\PDO::FETCH_COLUMN);

        // Delete principal and group membership information
        $prinDelStmt = $this->pdo->prepare('DELETE FROM principals WHERE id = ?');
        $memDelStmt = $this->pdo->prepare('DELETE FROM groupmembers WHERE principal_id = ? OR member_id = ?');
        foreach ($relevantPrincipals as $relevantPrincipal) {
            $memDelStmt->execute([$relevantPrincipal, $relevantPrincipal]);
            $prinDelStmt->execute([$relevantPrincipal]);
        }

        // Delete user record
        $userDelStmt = $this->pdo->prepare('DELETE FROM users WHERE username = ?');
        $userDelStmt->execute([$user->userName]);

        $this->pdo->commit();

    }
}
