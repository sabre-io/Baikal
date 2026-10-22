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
 * Calendar home that serves the principal's birthday calendar read-only.
 *
 * Before the birthday calendar is accessed, it is rebuilt if its events
 * were generated for an earlier year. All other calendars are served
 * unchanged.
 */
class CalendarHome extends \Sabre\CalDAV\CalendarHome {
    /**
     * @var BirthdayService
     */
    protected $birthdayService;

    /**
     * @param array $principalInfo
     */
    function __construct(\Sabre\CalDAV\Backend\BackendInterface $caldavBackend, $principalInfo, BirthdayService $birthdayService) {
        parent::__construct($caldavBackend, $principalInfo);
        $this->birthdayService = $birthdayService;
    }

    function getChild($name) {
        if ($name === BirthdayService::CALENDAR_URI) {
            $this->refreshBirthdayCalendar();
        }

        return $this->toBirthdayCalendar(parent::getChild($name));
    }

    function getChildren() {
        $this->refreshBirthdayCalendar();

        return array_map([$this, "toBirthdayCalendar"], parent::getChildren());
    }

    /**
     * @return void
     */
    protected function refreshBirthdayCalendar() {
        try {
            $this->birthdayService->refreshIfOutdated($this->principalInfo["uri"]);
        } catch (\Throwable $e) {
            # Also catches PHP errors, so the other calendars are still served
            error_log("Error refreshing birthday calendar: " . $e);
        }
    }

    /**
     * Returns the read-only variant of the node if it is the principal's
     * birthday calendar, the unchanged node otherwise.
     *
     * @param \Sabre\DAV\INode $node
     *
     * @return \Sabre\DAV\INode
     */
    protected function toBirthdayCalendar($node) {
        if (!$node instanceof \Sabre\CalDAV\SharedCalendar || $node->getName() !== BirthdayService::CALENDAR_URI) {
            return $node;
        }

        try {
            if ($this->birthdayService->hasCalendar($this->principalInfo["uri"])) {
                return ReadOnlyCalendar::fromCalendar($node);
            }
        } catch (\Throwable $e) {
            # Also catches PHP errors, so the calendar home can still be listed
            error_log("Error checking birthday calendar: " . $e);
        }

        return $node;
    }
}
