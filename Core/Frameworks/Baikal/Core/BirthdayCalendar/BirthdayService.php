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
    const CALENDAR_COLOR = "#c061cb";

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
    protected $calendarIds = [];

    /**
     * Reference years by principal URI (null if there is none), as read or
     * written during this request.
     *
     * @var array
     */
    protected $referenceYears = [];

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
            "{http://apple.com/ns/ical/}calendar-color"                          => self::CALENDAR_COLOR,
            "{" . CalDAV\Plugin::NS_CALDAV . "}supported-calendar-component-set" => new CalDAV\Xml\Property\SupportedCalendarComponentSet(["VEVENT"]),
        ]);

        # Year 0 marks the calendar as birthday calendar, with events still to be generated
        $this->setReferenceYear($principalUri, 0);
        $this->calendarIds[$principalUri] = $calendarId;

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
        $this->calendarIds[$principalUri] = false;
        $this->referenceYears[$principalUri] = null;
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

        # Writing all events in one transaction is much faster than one
        # transaction per statement, especially on SQLite
        $this->runInTransaction(function () use ($principalUri, $calendarId) {
            $this->rebuildEvents($principalUri, $calendarId);
        });
    }

    /**
     * @param string $principalUri
     * @param array $calendarId
     *
     * @return void
     */
    protected function rebuildEvents($principalUri, $calendarId) {
        # All existing events are loaded at once instead of one query per card
        $existingEvents = [];
        $objectUris = array_column($this->calendarBackend->getCalendarObjects($calendarId), "uri");
        foreach ($this->calendarBackend->getMultipleCalendarObjects($calendarId, $objectUris) as $object) {
            $existingEvents[$object["uri"]] = $object["calendardata"];
        }

        $expectedUris = [];
        foreach ($this->cardBackend->getAddressBooksForUser($principalUri) as $addressBook) {
            $cardUris = array_column($this->cardBackend->getCards($addressBook["id"]), "uri");

            # Cards are loaded in chunks to stay below the limit of bound
            # parameters of the database (999 on older SQLite versions)
            foreach (array_chunk($cardUris, self::CARD_CHUNK_SIZE) as $chunk) {
                foreach ($this->cardBackend->getMultipleCards($addressBook["id"], $chunk) as $card) {
                    if ($this->writeEvent($calendarId, $addressBook["id"], $card["uri"], $card["carddata"], $existingEvents)) {
                        $expectedUris[$this->getEventUri($addressBook["id"], $card["uri"])] = true;
                    }
                }
            }
        }

        foreach (array_keys($existingEvents) as $eventUri) {
            if (!isset($expectedUris[$eventUri])) {
                $this->calendarBackend->deleteCalendarObject($calendarId, (string) $eventUri);
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

        $previousYear = $this->getReferenceYear($principalUri);
        $year = $this->eventBuilder->getReferenceYear();
        if ($previousYear === null || $previousYear === $year) {
            return;
        }

        # Several clients may sync right after New Year; only the request
        # that moves the reference year forward rebuilds the calendar
        if (!$this->claimReferenceYear($principalUri, $previousYear, $year)) {
            return;
        }

        try {
            $this->syncPrincipal($principalUri);
        } catch (\Throwable $e) {
            # Lets the next request try again
            $this->setReferenceYear($principalUri, $previousYear);

            throw $e;
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

        $prefix = $addressBookId . "-";
        $this->runInTransaction(function () use ($calendarId, $prefix) {
            foreach ($this->calendarBackend->getCalendarObjects($calendarId) as $object) {
                if (strpos($object["uri"], $prefix) === 0) {
                    $this->calendarBackend->deleteCalendarObject($calendarId, $object["uri"]);
                }
            }
        });
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
     * @param array|null $existingEvents data of all events in the calendar by URI, if already
     *                                    loaded; otherwise the event is looked up
     *
     * @return bool true if the card has an event
     */
    protected function writeEvent($calendarId, $addressBookId, $cardUri, $cardData, ?array $existingEvents = null) {
        $eventUri = $this->getEventUri($addressBookId, $cardUri);
        $eventData = $this->eventBuilder->build($cardData, "baikal-birthday-" . basename($eventUri, ".ics"));

        if ($existingEvents === null) {
            $existing = $this->calendarBackend->getCalendarObject($calendarId, $eventUri);
            $existingData = $existing === null ? null : $existing["calendardata"];
        } else {
            $existingData = $existingEvents[$eventUri] ?? null;
        }

        if ($eventData === null) {
            if ($existingData !== null) {
                $this->calendarBackend->deleteCalendarObject($calendarId, $eventUri);
            }

            return false;
        }

        if ($existingData === null) {
            $this->calendarBackend->createCalendarObject($calendarId, $eventUri, $eventData);
        } elseif ($this->withoutTimestamps($existingData) !== $this->withoutTimestamps($eventData)) {
            $this->calendarBackend->updateCalendarObject($calendarId, $eventUri, $eventData);
        }

        return true;
    }

    /**
     * @param array $calendarId
     * @param string $eventUri
     *
     * @return void
     */
    protected function deleteEvent($calendarId, $eventUri) {
        if ($this->calendarBackend->getCalendarObject($calendarId, $eventUri) !== null) {
            $this->calendarBackend->deleteCalendarObject($calendarId, $eventUri);
        }
    }

    /**
     * Runs the given function in a transaction, unless one is already active.
     * The transaction is rolled back if the function throws.
     *
     * @return void
     */
    protected function runInTransaction(callable $fn) {
        if ($this->pdo->inTransaction()) {
            $fn();

            return;
        }

        $this->pdo->beginTransaction();
        try {
            $fn();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();

            throw $e;
        }
        $this->pdo->commit();
    }

    /**
     * Removes DTSTAMP lines, which change on every build.
     *
     * @param string $calendarData
     *
     * @return string
     */
    protected function withoutTimestamps($calendarData) {
        return preg_replace('/^DTSTAMP:[^\r\n]*\r?\n/m', '', (string) $calendarData);
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
        if (!array_key_exists($principalUri, $this->calendarIds)) {
            $calendarId = $this->findCalendarInstance($principalUri);
            if ($calendarId !== null && $this->getReferenceYear($principalUri) === null) {
                # Not created by this service
                $calendarId = null;
            }

            $this->calendarIds[$principalUri] = $calendarId ?? false;
        }

        return $this->calendarIds[$principalUri] === false ? null : $this->calendarIds[$principalUri];
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
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);

        return $row === false ? null : [intval($row["calendarid"]), intval($row["id"])];
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
        $principalUri = $stmt->fetchColumn();

        return $principalUri === false ? null : $principalUri;
    }

    /**
     * Returns the path of the birthday calendar, as used by the property storage.
     *
     * @param string $principalUri
     *
     * @return string
     */
    protected function getCalendarPath($principalUri) {
        # The calendar home is named like in Sabre\CalDAV\CalendarHome::getName()
        [, $principalName] = \Sabre\Uri\split($principalUri);

        return CalDAV\Plugin::CALENDAR_ROOT . "/" . $principalName . "/" . self::CALENDAR_URI;
    }

    /**
     * @param string $principalUri
     *
     * @return int|null
     */
    protected function getReferenceYear($principalUri) {
        # The calendar home may be accessed several times per request
        if (!array_key_exists($principalUri, $this->referenceYears)) {
            $path = $this->getCalendarPath($principalUri);
            $propFind = new DAV\PropFind($path, [self::PROPERTY_REFERENCE_YEAR]);
            $this->propertyBackend->propFind($path, $propFind);
            $value = $propFind->get(self::PROPERTY_REFERENCE_YEAR);

            $this->referenceYears[$principalUri] = $value === null ? null : intval($value);
        }

        return $this->referenceYears[$principalUri];
    }

    /**
     * Changes the reference year only if it still has the expected value.
     *
     * The value is bound as LOB like in the property storage backend, so it
     * compares equal to the stored value on all databases.
     *
     * @param string $principalUri
     * @param int $expectedYear
     * @param int $year
     *
     * @return bool true if the reference year was changed by this call
     */
    protected function claimReferenceYear($principalUri, $expectedYear, $year) {
        $path = $this->getCalendarPath($principalUri);
        $name = self::PROPERTY_REFERENCE_YEAR;
        $value = strval($year);
        $expectedValue = strval($expectedYear);

        $stmt = $this->pdo->prepare("UPDATE " . $this->propertyBackend->tableName . " SET value = :value WHERE path = :path AND name = :name AND value = :expected");
        $stmt->bindParam("value", $value, \PDO::PARAM_LOB);
        $stmt->bindParam("path", $path, \PDO::PARAM_STR);
        $stmt->bindParam("name", $name, \PDO::PARAM_STR);
        $stmt->bindParam("expected", $expectedValue, \PDO::PARAM_LOB);
        $stmt->execute();

        if ($stmt->rowCount() !== 1) {
            # Changed by another request, so the cached value is outdated
            unset($this->referenceYears[$principalUri]);

            return false;
        }

        $this->referenceYears[$principalUri] = $year;

        return true;
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
        $this->referenceYears[$principalUri] = $year;
    }
}
