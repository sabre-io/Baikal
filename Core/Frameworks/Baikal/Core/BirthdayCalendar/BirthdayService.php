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
use Sabre\CardDAV;
use Sabre\DAV;

/**
 * Maintains the birthday calendar of a principal.
 *
 * A principal has a birthday calendar if it owns a calendar with the URI
 * CALENDAR_URI that was created by this service, which is marked by the
 * PROPERTY_REFERENCE_YEAR property. Calendars with the same URI that were
 * created otherwise (e.g. migrated from another server) are left alone.
 * For every contact with a birthday in any of the principal's address
 * books, the calendar contains one event. All changes are written
 * through the sabre/dav CalDAV backend, so sync tokens are kept up to date
 * and clients pick up changes through WebDAV-Sync.
 */
class BirthdayService {
    const CALENDAR_URI = "contact_birthdays";
    const CALENDAR_DISPLAYNAME = "Birthdays";
    const CALENDAR_DESCRIPTION = "Birthdays of your contacts";

    /**
     * Property storing the reference year the events were generated for.
     * Its existence marks a calendar as birthday calendar.
     */
    const PROPERTY_REFERENCE_YEAR = "{http://sabre.io/baikal}birthday-reference-year";

    /**
     * Maximum number of cards loaded with one query.
     */
    const CARD_CHUNK_SIZE = 500;

    /**
     * @var \PDO
     */
    protected $pdo;

    /**
     * @var CalDAV\Backend\PDO
     */
    protected $calendarBackend;

    /**
     * @var CardDAV\Backend\PDO
     */
    protected $cardBackend;

    /**
     * @var DAV\PropertyStorage\Backend\PDO
     */
    protected $propertyBackend;

    /**
     * @var EventBuilder
     */
    protected $eventBuilder;

    /**
     * Calendar ids by principal URI (false if there is no birthday calendar).
     *
     * @var array
     */
    protected $aCalendarIds = [];

    /**
     * @param EventBuilder|null $eventBuilder defaults to a builder for the current year
     */
    function __construct(\PDO $pdo, ?EventBuilder $eventBuilder = null) {
        $this->pdo = $pdo;
        $this->calendarBackend = new CalDAV\Backend\PDO($pdo);
        $this->cardBackend = new CardDAV\Backend\PDO($pdo);
        $this->propertyBackend = new DAV\PropertyStorage\Backend\PDO($pdo);
        $this->eventBuilder = $eventBuilder ?? new EventBuilder();
    }

    /**
     * Returns true if the principal has a birthday calendar.
     *
     * @param string $principalUri
     *
     * @return bool
     */
    function hasCalendar($principalUri) {
        return $this->getCalendarId($principalUri) !== null;
    }

    /**
     * Creates the birthday calendar of the principal, if it does not exist yet.
     *
     * The calendar is created empty; call syncPrincipal() to fill it.
     *
     * @param string $principalUri
     *
     * @return array calendar id as used by the CalDAV backend
     *
     * @throws \Exception if the principal has another calendar with the URI CALENDAR_URI
     */
    function ensureCalendarExists($principalUri) {
        if (($calendarId = $this->getCalendarId($principalUri)) !== null) {
            return $calendarId;
        }

        if ($this->findCalendarInstance($principalUri) !== null) {
            throw new \Exception("A calendar with the URI '" . self::CALENDAR_URI . "' already exists.");
        }

        $calendarId = $this->calendarBackend->createCalendar($principalUri, self::CALENDAR_URI, [
            "{DAV:}displayname"                                                  => self::CALENDAR_DISPLAYNAME,
            "{" . CalDAV\Plugin::NS_CALDAV . "}calendar-description"             => self::CALENDAR_DESCRIPTION,
            "{" . CalDAV\Plugin::NS_CALDAV . "}supported-calendar-component-set" => new CalDAV\Xml\Property\SupportedCalendarComponentSet(["VEVENT"]),
        ]);

        # Year 0 marks the calendar as birthday calendar, with events still to be generated
        $this->setReferenceYear($principalUri, 0);
        $this->aCalendarIds[$principalUri] = $calendarId;

        return $calendarId;
    }

    /**
     * Deletes the birthday calendar of the principal, including all events.
     *
     * @param string $principalUri
     *
     * @return void
     */
    function deleteCalendar($principalUri) {
        if (($calendarId = $this->getCalendarId($principalUri)) === null) {
            return;
        }

        $this->calendarBackend->deleteCalendar($calendarId);
        $this->propertyBackend->delete($this->getCalendarPath($principalUri));
        $this->aCalendarIds[$principalUri] = false;
    }

    /**
     * Rebuilds all events of the principal's birthday calendar from the
     * principal's address books. Events of contacts that no longer exist or
     * no longer have a birthday are removed.
     *
     * @param string $principalUri
     *
     * @return void
     */
    function syncPrincipal($principalUri) {
        if (($calendarId = $this->getCalendarId($principalUri)) === null) {
            return;
        }

        # All existing events are loaded at once instead of one query per card
        $aExistingEvents = [];
        $aObjectUris = array_column($this->calendarBackend->getCalendarObjects($calendarId), "uri");
        foreach ($this->calendarBackend->getMultipleCalendarObjects($calendarId, $aObjectUris) as $aObject) {
            $aExistingEvents[$aObject["uri"]] = $aObject["calendardata"];
        }

        $aExpectedUris = [];
        foreach ($this->cardBackend->getAddressBooksForUser($principalUri) as $aAddressBook) {
            $aCardUris = array_column($this->cardBackend->getCards($aAddressBook["id"]), "uri");

            # Cards are loaded in chunks to stay below the limit of bound
            # parameters of the database (999 on older SQLite versions)
            foreach (array_chunk($aCardUris, self::CARD_CHUNK_SIZE) as $aChunk) {
                foreach ($this->cardBackend->getMultipleCards($aAddressBook["id"], $aChunk) as $aCard) {
                    if ($this->writeEvent($calendarId, $aAddressBook["id"], $aCard["uri"], $aCard["carddata"], $aExistingEvents)) {
                        $aExpectedUris[$this->getEventUri($aAddressBook["id"], $aCard["uri"])] = true;
                    }
                }
            }
        }

        foreach (array_keys($aExistingEvents) as $sEventUri) {
            if (!isset($aExpectedUris[$sEventUri])) {
                $this->calendarBackend->deleteCalendarObject($calendarId, (string) $sEventUri);
            }
        }

        $this->setReferenceYear($principalUri, $this->eventBuilder->getReferenceYear());
    }

    /**
     * Rebuilds the birthday calendar if its events were generated for an
     * earlier year, so the window of titles with age moves along.
     *
     * @param string $principalUri
     *
     * @return void
     */
    function refreshIfOutdated($principalUri) {
        if (!$this->hasCalendar($principalUri)) {
            return;
        }

        if ($this->getReferenceYear($principalUri) !== $this->eventBuilder->getReferenceYear()) {
            $this->syncPrincipal($principalUri);
        }
    }

    /**
     * Creates, updates or removes the event of a card that was created or updated.
     *
     * @param int $addressBookId
     * @param string $cardUri
     * @param string $cardData
     *
     * @return void
     */
    function onCardChanged($addressBookId, $cardUri, $cardData) {
        $principalUri = $this->getPrincipalUriForAddressBook($addressBookId);
        if ($principalUri === null || ($calendarId = $this->getCalendarId($principalUri)) === null) {
            return;
        }

        $this->writeEvent($calendarId, $addressBookId, $cardUri, $cardData);
    }

    /**
     * Removes the event of a card that was deleted.
     *
     * @param int $addressBookId
     * @param string $cardUri
     *
     * @return void
     */
    function onCardDeleted($addressBookId, $cardUri) {
        $principalUri = $this->getPrincipalUriForAddressBook($addressBookId);
        if ($principalUri === null || ($calendarId = $this->getCalendarId($principalUri)) === null) {
            return;
        }

        $this->deleteEvent($calendarId, $this->getEventUri($addressBookId, $cardUri));
    }

    /**
     * Removes the events of all cards of an address book that was deleted.
     *
     * As the address book no longer exists, its owner has to be passed in.
     *
     * @param string $principalUri
     * @param int $addressBookId
     *
     * @return void
     */
    function onAddressBookDeleted($principalUri, $addressBookId) {
        if (($calendarId = $this->getCalendarId($principalUri)) === null) {
            return;
        }

        $sPrefix = $addressBookId . "-";
        foreach ($this->calendarBackend->getCalendarObjects($calendarId) as $aObject) {
            if (strpos($aObject["uri"], $sPrefix) === 0) {
                $this->calendarBackend->deleteCalendarObject($calendarId, $aObject["uri"]);
            }
        }
    }

    /**
     * Returns the URI of the event generated for a card.
     *
     * The address book id is kept readable as prefix, so all events of an
     * address book can be found without knowing its cards.
     *
     * @param int $addressBookId
     * @param string $cardUri
     *
     * @return string
     */
    function getEventUri($addressBookId, $cardUri) {
        return $addressBookId . "-" . md5($cardUri) . ".ics";
    }

    /**
     * Writes the event of a card. Removes the event if the card has no
     * (longer a) usable birthday. Unchanged events are not rewritten, to
     * avoid needless sync traffic.
     *
     * @param array $calendarId
     * @param int $addressBookId
     * @param string $cardUri
     * @param string $cardData
     * @param array|null $aExistingEvents data of all events in the calendar by URI, if already
     *                                    loaded; otherwise the event is looked up
     *
     * @return bool true if the card has an event
     */
    protected function writeEvent($calendarId, $addressBookId, $cardUri, $cardData, ?array $aExistingEvents = null) {
        $sEventUri = $this->getEventUri($addressBookId, $cardUri);
        $sEventData = $this->eventBuilder->build($cardData, "baikal-birthday-" . basename($sEventUri, ".ics"));

        if ($aExistingEvents === null) {
            $aExisting = $this->calendarBackend->getCalendarObject($calendarId, $sEventUri);
            $sExistingData = $aExisting === null ? null : $aExisting["calendardata"];
        } else {
            $sExistingData = $aExistingEvents[$sEventUri] ?? null;
        }

        if ($sEventData === null) {
            if ($sExistingData !== null) {
                $this->calendarBackend->deleteCalendarObject($calendarId, $sEventUri);
            }

            return false;
        }

        if ($sExistingData === null) {
            $this->calendarBackend->createCalendarObject($calendarId, $sEventUri, $sEventData);
        } elseif ($this->withoutTimestamps($sExistingData) !== $this->withoutTimestamps($sEventData)) {
            $this->calendarBackend->updateCalendarObject($calendarId, $sEventUri, $sEventData);
        }

        return true;
    }

    /**
     * @param array $calendarId
     * @param string $sEventUri
     *
     * @return void
     */
    protected function deleteEvent($calendarId, $sEventUri) {
        if ($this->calendarBackend->getCalendarObject($calendarId, $sEventUri) !== null) {
            $this->calendarBackend->deleteCalendarObject($calendarId, $sEventUri);
        }
    }

    /**
     * Removes DTSTAMP lines, which change on every build.
     *
     * @param string $sCalendarData
     *
     * @return string
     */
    protected function withoutTimestamps($sCalendarData) {
        return preg_replace('/^DTSTAMP:[^\r\n]*\r?\n/m', '', (string) $sCalendarData);
    }

    /**
     * Returns the calendar id of the principal's birthday calendar, as used
     * by the CalDAV backend ([calendarid, instanceid]), or null if there is none.
     *
     * @param string $principalUri
     *
     * @return array|null
     */
    protected function getCalendarId($principalUri) {
        if (!array_key_exists($principalUri, $this->aCalendarIds)) {
            $calendarId = $this->findCalendarInstance($principalUri);
            if ($calendarId !== null && $this->getReferenceYear($principalUri) === null) {
                # Not created by this service
                $calendarId = null;
            }

            $this->aCalendarIds[$principalUri] = $calendarId ?? false;
        }

        return $this->aCalendarIds[$principalUri] === false ? null : $this->aCalendarIds[$principalUri];
    }

    /**
     * Returns the calendar id of the principal's calendar with the URI
     * CALENDAR_URI, whether it is a birthday calendar or not.
     *
     * @param string $principalUri
     *
     * @return array|null
     */
    protected function findCalendarInstance($principalUri) {
        $stmt = $this->pdo->prepare("SELECT calendarid, id FROM calendarinstances WHERE principaluri = ? AND uri = ?");
        $stmt->execute([$principalUri, self::CALENDAR_URI]);
        $aRow = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $aRow === false ? null : [intval($aRow["calendarid"]), intval($aRow["id"])];
    }

    /**
     * Returns the owner of an address book.
     *
     * @param int $addressBookId
     *
     * @return string|null
     */
    function getPrincipalUriForAddressBook($addressBookId) {
        $stmt = $this->pdo->prepare("SELECT principaluri FROM addressbooks WHERE id = ?");
        $stmt->execute([$addressBookId]);
        $sPrincipalUri = $stmt->fetchColumn();

        return $sPrincipalUri === false ? null : $sPrincipalUri;
    }

    /**
     * Returns the path of the birthday calendar, as used by the property storage.
     *
     * @param string $principalUri
     *
     * @return string
     */
    protected function getCalendarPath($principalUri) {
        return "calendars/" . basename($principalUri) . "/" . self::CALENDAR_URI;
    }

    /**
     * @param string $principalUri
     *
     * @return int|null
     */
    protected function getReferenceYear($principalUri) {
        $sPath = $this->getCalendarPath($principalUri);
        $propFind = new DAV\PropFind($sPath, [self::PROPERTY_REFERENCE_YEAR]);
        $this->propertyBackend->propFind($sPath, $propFind);
        $sYear = $propFind->get(self::PROPERTY_REFERENCE_YEAR);

        return $sYear === null ? null : intval($sYear);
    }

    /**
     * @param string $principalUri
     * @param int $year
     *
     * @return void
     */
    protected function setReferenceYear($principalUri, $year) {
        $propPatch = new DAV\PropPatch([self::PROPERTY_REFERENCE_YEAR => strval($year)]);
        $this->propertyBackend->propPatch($this->getCalendarPath($principalUri), $propPatch);
        $propPatch->commit();
    }
}
