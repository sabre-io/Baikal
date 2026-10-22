<?php

#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://sabre.io/baikal
#
#  This script is part of the Baïkal Server project. The Baïkal
#  Server project is free software; you can redistribute it
#  and/or modify it under the terms of the GNU General Public
#  License as published by the Free Software Foundation; either
#  version 2 of the License, or (at your option) any later version.
#
#  The GNU General Public License can be found at
#  http://www.gnu.org/copyleft/gpl.html.
#
#  This script is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  This copyright notice MUST APPEAR in all copies of the script!
#################################################################

namespace Baikal\Core\BirthdayCalendar;

/**
 * The birthday calendar as seen by clients.
 *
 * Its events are generated from the address books, so clients may not
 * create, change, delete or share them. Calendar properties like the
 * display name or color can still be changed.
 */
class ReadOnlyCalendar extends \Sabre\CalDAV\SharedCalendar {
    /**
     * Privileges removed from the ACL of the calendar and its events.
     */
    const DENIED_PRIVILEGES = ["{DAV:}write", "{DAV:}share"];

    /**
     * Creates a read-only calendar from the calendar node built by sabre/dav.
     *
     * @return ReadOnlyCalendar
     */
    static function fromCalendar(\Sabre\CalDAV\SharedCalendar $calendar) {
        return new self($calendar->caldavBackend, $calendar->calendarInfo);
    }

    function getACL() {
        return $this->withoutDeniedPrivileges(parent::getACL());
    }

    function getChildACL() {
        return $this->withoutDeniedPrivileges(parent::getChildACL());
    }

    /**
     * @return array
     */
    protected function withoutDeniedPrivileges(array $aAcl) {
        return array_values(array_filter($aAcl, function ($aAce) {
            return !in_array($aAce["privilege"], self::DENIED_PRIVILEGES, true);
        }));
    }
}
