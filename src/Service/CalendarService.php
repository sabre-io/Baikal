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

}
