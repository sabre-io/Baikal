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
     * Creates a new Calendar for a new User
     */
    function provision(User $user) {

        $this->calBackend->createCalendar($user->getPrincipalUri(), UUIDUtil::getUUID() , [
            '{DAV:}displayname'                                               => 'HOME',
            '{urn:ietf:params:xml:ns:caldav}calendar-description'             => 'Default calendar for your private events',
            '{urn:ietf:params:xml:ns:caldav}supported-calendar-component-set' => new SupportedCalendarComponentSet(['VEVENT'])
        ]);
        $this->calBackend->createCalendar($user->getPrincipalUri(), UUIDUtil::getUUID(), [
            '{DAV:}displayname'                                               => 'WORK',
            '{urn:ietf:params:xml:ns:caldav}calendar-description'             => 'Default calendar for your businesslike events',
            '{urn:ietf:params:xml:ns:caldav}supported-calendar-component-set' => new SupportedCalendarComponentSet(['VEVENT'])
        ]);
        $this->calBackend->createCalendar($user->getPrincipalUri(), UUIDUtil::getUUID(), [
            '{DAV:}displayname'                                               => 'TASKS',
            '{urn:ietf:params:xml:ns:caldav}calendar-description'             => 'Default Storage for your tasks',
            '{urn:ietf:params:xml:ns:caldav}supported-calendar-component-set' => new SupportedCalendarComponentSet(['VTODO'])
        ]);

    }

}
