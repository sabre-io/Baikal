<?php

namespace Baikal\Domain;

/**
 * Domain model for User within the Baikal system
 */
final class User extends DomainObject {

    /**
     * Unique ID
     *
     * @var mixed
     */
    public $id;

    /**
     * @var string
     */
    public $userName;

    /**
     * @var string
     */
    public $displayName;

    /**
     * @var string
     */
    public $email;

    /**
     * Password. If null, it will not be updated/set
     *
     * @var string|null
     */
    public $password;

    /**
     * Returns the principal URI
     *
     * @return string
     */
    function getPrincipalUri() {

        return 'principals/' . $this->userName;

    }

    /**
     * Validator
     *
     * Called before insert and update operations
     *
     * Should throw an \InvalidArgumentException if there's a validation problem.
     * @return void
     */
    function validate() {

        parent::validate();
        if (!$this->userName) {
            throw new \InvalidArgumentException('Username MUST be set');
        }
        if (!$this->email) {
            throw new \InvalidArgumentException('Email MUST be set');
        }
        if (!$this->displayName) {
            throw new \InvalidArgumentException('DisplayName MUST be set');
        }
        if (strpos($this->email, '@') === false) {
            throw new \InvalidArgumentException('Email MUST be valid');
        }
    }

    /**
     * Validator function, specific for insert operations
     */
    function validateForCreate() {

        parent::validateForCreate();
        if (!$this->password) {
            throw new \InvalidArgumentException('Password MUST be set');
        }
        if (strlen($this->userName) > 20) {
            throw new \InvalidArgumentException('Username should be no longer than 20 characters');
        }
        if (!preg_match('|^[A-Za-z0-9]+$|', $this->userName)) {
            throw new \InvalidArgumentException('Username may only contain A-Z, a-z and 0-9');
        }

    }
}
