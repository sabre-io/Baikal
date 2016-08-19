<?php

namespace Baikal\Domain;

/**
 * Domain model for Calendar within the Baikal system
 */
final class Calendar extends DomainObject {

    /**
     * Unique ID
     *
     * @var mixed
     */
    public $id;

    /**
     * @var string
     */
    public $uri;

    /**
     * @var string
     */
    public $displayName;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $calendarColor;
}
