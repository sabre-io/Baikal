<?php

namespace Baikal\Service;

use Baikal\Domain\User;
use Generator;
use PDO;
use Sabre\CalDAV\Backend\BackendInterface as CalBackend;
#use Sabre\CardDAV\Backend\BackendInterface as CardBackend;

/**
 * UserRepository implementation using PDO
 */
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
    function createDefault(User $user) {

        $this->calBackend->createCalendar($user->getPrincipalUri(), 'default', [
            '{DAV:}displayname'                                     => 'Default Calender',
            '{urn:ietf:params:xml:ns:caldav}calendar-description'   => 'Default Description',
            '{urn:ietf:params:xml:ns:caldav}calendar-timezone'      => '???',
            '{http://apple.com/ns/ical/}calendar-order'             => '1',
            '{http://apple.com/ns/ical/}calendar-color'             => '#1D9BF6FF',
        ]);
    }

}
