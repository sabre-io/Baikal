<?php

namespace Baikal\Domain\User;

final class DisplayName
{
    private $displayName;

    private function __construct($displayName)
    {
        // TODO: Implement guards if required
        $this->displayName = $displayName;
    }

    static function fromString($displayName)
    {
        return new self($displayName);
    }

    function __toString()
    {
        return $this->displayName;
    }
}
