<?php

namespace Baikal\Repository;

use Baikal\Domain\Calendar;
use Generator;
use PDO;

/**
 * UserRepository implementation using PDO
 */
final class CalendarRepository
{
    /**
     * @var PDO
     */
    private $pdo;

    /**
     * @param PDO $pdo
     * @param string $authRealm
     */
    function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    /**
     * @return Calendar[]|Generator
     */
    function allCalendarsByUserName($userName)
    {
        $calendarQuery = $this->pdo->query("SELECT id, uri, calendarcolor as calendarColor, displayname as displayName, description FROM calendars WHERE principaluri = ?");
        
        $calendarQuery->execute([
            'principals/' . $userName
        ]);

        $calendars = [];
        foreach ($calendarQuery->fetchAll(\PDO::FETCH_ASSOC) as $calendarData) {
            $calendars[] = Calendar::fromArray($calendarData);
        }
        return $calendars;
    }

    /**
     * @return int
     */
    function countAllCalendars()
    {
        $statement = $this->pdo->query("SELECT COUNT(1) FROM calendars");
        return (int)$statement->fetchColumn();
    }

    /**
     * @return int
     */
    function countAllEvents()
    {
        $statement = $this->pdo->query("SELECT COUNT(1) FROM calendarobjects");
        return (int)$statement->fetchColumn();
    }

    /**
     * @param string $userName
     * @param string $calendarId
     * @throw \InvalidArgumentException
     * @return Calendar
     */
    function getByCalendarId($userName, $calendarId) {

        $calendarQuery = $this->pdo->query("SELECT id, uri, calendarcolor as calendarColor, displayname as displayName, description FROM calendars WHERE principaluri = ? AND id = ?");
        $calendarQuery->execute([
            'principals/' . $userName,
            $calendarId
        ]);

        $calendarData = $calendarQuery->fetch(PDO::FETCH_ASSOC);
        if ($calendarData === false) {
            throw new \InvalidArgumentException('Calendar with id: ' . $calendarId . ' for user ' . $userName . ' not found');
        }

        return Calendar::fromArray($calendarData);
    }

    /**
     * Creates a new Calendar
     */
    function create(Calendar $calendar) {
        throw new \Exception('Not implemented!');
    }

    /**
     * @param Calendar $calendar
     * @return void
     */
    function update(User $user) {
        throw new \Exception('Not implemented!');
    }

    /**
     * @param Calendar $calendar
     */
    function remove(User $user) {
        throw new \Exception('Not implemented!');
    }
}
