<?php

namespace Baikal;

class StatsService {

    /**
     * @var PDO
     */
    protected $pdo;

    /**
     * Creates the service object
     */
    function __construct(\PDO $pdo) {

        $this->pdo = $pdo;

    }

    /**
     * Returns the total number of users
     */
    function users() {

        return $this->pdo->query("SELECT COUNT(1) FROM users")->fetchColumn();

    }

    /**
     * Returns the total number of calendars
     */
    function calendars() {

        return $this->pdo->query("SELECT COUNT(1) FROM calendars")->fetchColumn();

    }

    /**
     * Returns the total number of events
     */
    function events() {

        return $this->pdo->query("SELECT COUNT(1) FROM calendarobjects WHERE componenttype = 'VEVENT'")->fetchColumn();


    }

    /**
     * Returns the total number of tasks
     */
    function tasks() {

        return $this->pdo->query("SELECT COUNT(1) FROM calendarobjects WHERE componenttype = 'VTODO'")->fetchColumn();


    }

    /**
     * Returns the total number of addressbooks
     */
    function addressBooks() {

        return $this->pdo->query("SELECT COUNT(1) FROM addressbooks")->fetchColumn();

    }

    /**
     * Returns the total number of cards
     */
    function cards() {

        return $this->pdo->query("SELECT COUNT(1) FROM cards")->fetchColumn();

    }
}
