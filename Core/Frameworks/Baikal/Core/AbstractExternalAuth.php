<?php

namespace Baikal\Core;

/**
 * This is an abstract authentication, that allows to create external
 * authentication backends. User are automatic created, when the does not exists
 * in baikal (can disabled).
 *
 * @author Sascha Kuehndel (InuSasha) <dev@inusasha.de>
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
abstract class AbstractExternalAuth extends \Sabre\DAV\Auth\Backend\AbstractBasic {

    /**
     * enable autocreation of user
     *
     * @var PDO
     */
    protected $enableAutoCreation;

    /**
     * Reference to PDO connection
     *
     * @var PDO
     */
    private $pdo;

    /**
     * PDO table name we'll be using
     *
     * @var string
     */
    private $tableName;

    /**
     * Creates the backend object.
     *
     * If the filename argument is passed in, it will parse out the specified file fist.
     *
     * @param PDO $pdo
     * @param string $realm
     * @param string $tableName The PDO table name to use
     */
    public function __construct(\PDO $pdo, $realm = 'BaikalDAV', $tableName = 'users') {

        $this->pdo = $pdo;
        $this->tableName = $tableName;
        $this->enableAutoCreation = true;
    }

    /**
     * Validates a username and password
     *
     * This method should return true or false depending on if login
     * succeeded.
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function validateUserPass($username, $password) {

        /* auth user agains backend */
        if (!$this->validateUserPassExternal($username, $password))
             return false;

        /* check user exists already */
        $stmt = $this->pdo->prepare('SELECT username FROM '.$this->tableName.' WHERE username = ?');
        $stmt->execute(array($username));
        $result = $stmt->fetchAll();
        if( count($result) == 1) {
             $this->currentUser = $username;
             return true;
        }

        /* failed login, when new user should not create */
        if( !BAIKAL_DAV_AUTO_CREATE_USER || !$this->enableAutoCreation)
            return false;

        /* create user */
        $this->autoUserCreation($username);
        $this->currentUser = $username;
        return true;
    }

    /**
     * Validates a username and password agains external backend
     *
     * This method should return true or false depending on if login
     * succeeded.
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public abstract function validateUserPassExternal($username, $password);

    /**
     * return the displayname and email from the external Backend
     *
     * @param string $username
     * @return array ('displayname' => string, 'email' => string)
     */
    public function getAccountValues($username) {

        return array();
    }

    /**
     * create an internal user, when user not exists
     *
     * @param string $username
     */
    private function autoUserCreation($username) {
        
        /* get account values from backend */
        $values = $this->getAccountValues($username);
        if (!isset($values['displayname']) OR strlen($values['displayname']) === 0)
             $values['displayname'] = $username;
        if (!isset($values['email']) OR strlen($values['email']) === 0) {
             if(filter_var($username, FILTER_VALIDATE_EMAIL))
                 $values['email'] = $username;
             else
                 $values['email'] = 'unset-mail';
        }

        /* create user */
        $user = new \Baikal\Model\User();
        $user->set('username', $username);
        $user->set('displayname', $values['displayname']);
        $user->set('email', $values['email']);
        $user->persist();
    }

}
