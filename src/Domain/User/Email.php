<?php

namespace Baikal\Domain\User;

use InvalidArgumentException;

final class Email
{
    /**
     * @var string
     */
    private $email;

    /**
     * @param string $email
     */
    private function __construct($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            throw new InvalidArgumentException("Invalid email '{$email}'");
        }
        $this->email = $email;
    }

    /**
     * @param string $email
     * @return Email
     */
    public static function fromString($email)
    {
        return new self($email);
    }

    public function __toString()
    {
        return $this->email;
    }
}