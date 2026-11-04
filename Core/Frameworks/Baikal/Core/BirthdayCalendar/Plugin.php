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

use Sabre\CalDAV;
use Sabre\DAV;
use Sabre\DAVACL;

/**
 * Keeps clients and the scheduling plugin from writing to birthday calendars
 * in ways the ACL of ReadOnlyCalendar does not cover.
 */
class Plugin extends DAV\ServerPlugin {
    const PROPERTY_DEFAULT_CALENDAR = "{" . CalDAV\Plugin::NS_CALDAV . "}schedule-default-calendar-URL";

    /**
     * @var DAV\Server
     */
    protected $server;

    function initialize(DAV\Server $server) {
        $this->server = $server;

        # The marker of birthday calendars is managed by BirthdayService only.
        # Clients removing it would make the calendar writable, clients
        # setting it would let the next rebuild delete their events.
        $server->protectedProperties[] = BirthdayService::PROPERTY_REFERENCE_YEAR;

        # Runs after Sabre\CalDAV\Schedule\Plugin computed the default calendar
        $server->on("propFind", [$this, "propFindDefaultCalendar"], 110);
    }

    function getPluginName() {
        return "baikal-birthday-calendar";
    }

    /**
     * sabre/dav uses the first calendar of the home supporting VEVENT as
     * default calendar, which may be the birthday calendar. Invitations are
     * delivered there bypassing the ACL and would be removed by the next
     * rebuild, so the next suitable calendar is used instead.
     *
     * @return void
     */
    function propFindDefaultCalendar(DAV\PropFind $propFind, DAV\INode $node) {
        if (!$node instanceof DAVACL\IPrincipal) {
            return;
        }

        $href = $propFind->get(self::PROPERTY_DEFAULT_CALENDAR);
        if (!$href instanceof DAV\Xml\Property\Href || !$this->isBirthdayCalendar($href->getHref())) {
            return;
        }

        $propFind->set(self::PROPERTY_DEFAULT_CALENDAR, $this->findDefaultCalendar(dirname($href->getHref()), $href->getHref()));
    }

    /**
     * Returns the first calendar in the home that sabre/dav would accept as
     * default calendar, skipping the birthday calendar.
     *
     * @param string $homePath
     * @param string $birthdayPath
     *
     * @return DAV\Xml\Property\LocalHref|null
     */
    protected function findDefaultCalendar($homePath, $birthdayPath) {
        $sccs = "{" . CalDAV\Plugin::NS_CALDAV . "}supported-calendar-component-set";

        $result = $this->server->getPropertiesIteratorForPath($homePath, [
            "{DAV:}resourcetype",
            "{DAV:}share-access",
            $sccs,
        ], 1);

        foreach ($result as $child) {
            if ($child["href"] === $birthdayPath) {
                continue;
            }
            if (!isset($child[200]["{DAV:}resourcetype"]) || !$child[200]["{DAV:}resourcetype"]->is("{" . CalDAV\Plugin::NS_CALDAV . "}calendar")) {
                continue;
            }
            if (isset($child[200]["{DAV:}share-access"])) {
                $shareAccess = $child[200]["{DAV:}share-access"]->getValue();
                if ($shareAccess !== DAV\Sharing\Plugin::ACCESS_NOTSHARED && $shareAccess !== DAV\Sharing\Plugin::ACCESS_SHAREDOWNER) {
                    continue;
                }
            }
            if (!isset($child[200][$sccs]) || in_array("VEVENT", $child[200][$sccs]->getValue())) {
                return new DAV\Xml\Property\LocalHref($child["href"]);
            }
        }

        return null;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    protected function isBirthdayCalendar($path) {
        if (basename($path) !== BirthdayService::CALENDAR_URI) {
            return false;
        }

        try {
            return $this->server->tree->getNodeForPath($path) instanceof ReadOnlyCalendar;
        } catch (DAV\Exception\NotFound $e) {
            return false;
        }
    }
}
