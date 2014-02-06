<?php

namespace Baikal\Core;

/**
 * This is an authentication backend that uses a database to manage passwords.
 *
 * Format of the database tables must match to the one of \Sabre\DAV\Auth\Backend\PDO
 *
 * @copyright Copyright (C) 2013 Lukasz Janyst. All rights reserved.
 * @author Lukasz Janyst <ljanyst@buggybrain.net>
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
abstract class AbstractExternalAuth extends \Sabre\DAV\Auth\Backend\AbstractBasic {

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
     * @param string $tableName The PDO table name to use
     */
    public function __construct(\PDO $pdo, $tableName = NULL) {

        $this->pdo = $pdo;
        if ($tableName == NULL)
             $this->tableName = $tableName;
        else
             $this->tableName = 'users';
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

        if (!$this->validateUserPassExternal($username, $password))
             return false;

        $this->currentUser = $username;
        $this->autoUserCreation($username);
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
    public abstract function getAccountValues($username);

    /**
     * create an internal user, when user not exists
     *
     * @param string $username
     */
    private function autoUserCreation($username) {
        
        /* search user in DB and do nothing, when user exists */
        $stmt = $this->pdo->prepare('SELECT username FROM '.$this->tableName.' WHERE username = ?');
        $stmt->execute(array($username));
        $result = $stmt->fetchAll();
        if (count($result))
             return;

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
