<?php

namespace Baikal\Domain\User;

final class Username
{
    /**
     * @var string
     */
    private $username;

    /**
     * @param string $username
     */
    private function __construct($username)
    {
        // TODO: Implement guards if required
        $this->username = $username;
    }

    /**
     * @param string $username
     * @return Username
     */
    public static function fromString($username)
    {
        return new self($username);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->username;
    }
}