<?php

namespace Baikal\Service;

use Baikal\Domain\User;
use Sabre\CalDAV\Backend\BackendInterface as CalBackend;
use Sabre\CalDAV\Xml\Property\SupportedCalendarComponentSet;
use Sabre\DAV\UUIDUtil;

class CalendarService {

    /**
     * @var CalBackend
     */
    private $calBackend;

    function __construct(CalBackend $calBackend) {

        $this->calBackend = $calBackend;
    }

    /**
     * Creates the three default calendars for a new user
     */
    function provision(User $user) {

        $this->calBackend->createCalendar($user->getPrincipalUri(), UUIDUtil::getUUID(), [
            '{DAV:}displayname'                                               => 'Home',
            '{urn:ietf:params:xml:ns:caldav}supported-calendar-component-set' => new SupportedCalendarComponentSet(['VEVENT'])
        ]);
        $this->calBackend->createCalendar($user->getPrincipalUri(), UUIDUtil::getUUID(), [
            '{DAV:}displayname'                                               => 'Work',
            '{urn:ietf:params:xml:ns:caldav}supported-calendar-component-set' => new SupportedCalendarComponentSet(['VEVENT'])
        ]);
        $this->calBackend->createCalendar($user->getPrincipalUri(), UUIDUtil::getUUID(), [
            '{DAV:}displayname'                                               => 'Tasks',
            '{urn:ietf:params:xml:ns:caldav}supported-calendar-component-set' => new SupportedCalendarComponentSet(['VTODO'])
        ]);
    }

    /**
     * Creates a new calendar for a user
     */
    function createCalendar(User $user, $displayName, $calendarDescription) {

        $this->calBackend->createCalendar($user->getPrincipalUri(), UUIDUtil::getUUID(), [
            '{DAV:}displayname'                                               => $displayName,
            '{urn:ietf:params:xml:ns:caldav}calendar-description'             => $calendarDescription,
            '{urn:ietf:params:xml:ns:caldav}supported-calendar-component-set' => new SupportedCalendarComponentSet(['VEVENT'])
        ]);
    }    

    function getByUserNameAndCalendarId($userName, $calendarId) {
        $calendars = $this->calBackend->getCalendarsForUser('principals/' . $userName);
        foreach ($calendars as $calendar) {
            if ($calendar['id'] == $calendarId) {
                $calendar['path'] = 'calendars/' . $userName . '/' . $calendar['uri'] . '/';
                return $calendar;
            }
        }
    }

}
