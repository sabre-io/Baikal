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

use Sabre\VObject;

/**
 * Converts the BDAY of a vCard into a yearly recurring all-day event.
 *
 * The recurring event is titled "🎂 <name>". If the year of birth is known,
 * every occurrence within a window around the reference year is overridden
 * with the age in its title, e.g. "🎂 Max Mustermann (36)". Occurrences
 * outside of that window fall back to the plain title.
 */
class EventBuilder {
    const PRODID = "-//Baikal//Birthday Calendar//EN";
    const SUMMARY_PREFIX = "🎂 ";

    /**
     * Number of years before the reference year with the age in the title.
     */
    const YEARS_BEFORE = 1;

    /**
     * Number of years after the reference year with the age in the title.
     */
    const YEARS_AFTER = 5;

    /**
     * Year placeholder for birthdays without a known year (leap year, so
     * February 29th is a valid date).
     */
    const PLACEHOLDER_YEAR = 1972;

    /**
     * The year the age window is centered on.
     *
     * @var int
     */
    protected $referenceYear;

    /**
     * Timestamp written to DTSTAMP.
     *
     * @var \DateTimeInterface
     */
    protected $now;

    /**
     * @param \DateTimeInterface|null $now defaults to the current time
     */
    function __construct(?\DateTimeInterface $now = null) {
        $this->now = $now ?? new \DateTimeImmutable("now", new \DateTimeZone("UTC"));
        $this->referenceYear = intval($this->now->format("Y"));
    }

    /**
     * Returns the year the age window is centered on.
     *
     * @return int
     */
    function getReferenceYear() {
        return $this->referenceYear;
    }

    /**
     * Builds the iCalendar data for the birthday of the given vCard.
     *
     * @param string $cardData serialized vCard
     * @param string $uid UID of the generated event
     *
     * @return string|null serialized iCalendar, or null if the vCard has no usable birthday
     */
    function build($cardData, $uid) {
        try {
            $vCard = VObject\Reader::read($cardData, VObject\Reader::OPTION_FORGIVING);
        } catch (\Exception $e) {
            return null;
        }

        if (!$vCard instanceof VObject\Component\VCard) {
            return null;
        }

        $birthday = $this->parseBirthday($vCard);
        $name = $this->getName($vCard);
        if ($birthday === null || $name === "") {
            return null;
        }

        [$year, $month, $day] = $birthday;
        $isLeapDay = ($month === 2 && $day === 29);
        $startYear = $year ?? self::PLACEHOLDER_YEAR;

        $vCalendar = new VObject\Component\VCalendar();
        $vCalendar->PRODID = self::PRODID;

        $rrule = $isLeapDay ? "FREQ=YEARLY;BYMONTH=2;BYMONTHDAY=-1" : "FREQ=YEARLY";
        $vEvent = $this->addEvent($vCalendar, $uid, $startYear, $month, $day, self::SUMMARY_PREFIX . $name);
        $vEvent->add("RRULE", $rrule);

        if ($year !== null) {
            $firstYear = max($year + 1, $this->referenceYear - self::YEARS_BEFORE);
            $lastYear = $this->referenceYear + self::YEARS_AFTER;

            for ($iYear = $firstYear; $iYear <= $lastYear; ++$iYear) {
                # In non leap years, February 29th birthdays occur on February 28th
                $iDay = ($isLeapDay && !checkdate(2, 29, $iYear)) ? 28 : $day;

                $vOverride = $this->addEvent(
                    $vCalendar,
                    $uid,
                    $iYear,
                    $month,
                    $iDay,
                    self::SUMMARY_PREFIX . $name . " (" . ($iYear - $year) . ")"
                );
                $vOverride->add("RECURRENCE-ID", $this->formatDate($iYear, $month, $iDay), ["VALUE" => "DATE"]);
            }
        }

        return $vCalendar->serialize();
    }

    /**
     * Adds an all-day VEVENT to the given calendar.
     *
     * @param string $uid
     * @param int $year
     * @param int $month
     * @param int $day
     * @param string $summary
     *
     * @return VObject\Component\VEvent
     */
    protected function addEvent(VObject\Component\VCalendar $vCalendar, $uid, $year, $month, $day, $summary) {
        $start = new \DateTimeImmutable(sprintf("%04d-%02d-%02d", $year, $month, $day));
        $end = $start->modify("+1 day");

        return $vCalendar->add("VEVENT", [
            "UID"     => $uid,
            "DTSTAMP" => $this->now,
            "DTSTART" => $vCalendar->createProperty("DTSTART", $start->format("Ymd"), ["VALUE" => "DATE"]),
            "DTEND"   => $vCalendar->createProperty("DTEND", $end->format("Ymd"), ["VALUE" => "DATE"]),
            "SUMMARY" => $summary,
            "TRANSP"  => "TRANSPARENT",
        ]);
    }

    /**
     * Returns [year|null, month, day] of the BDAY property, or null if it is
     * missing or invalid.
     *
     * @return array|null
     */
    protected function parseBirthday(VObject\Component\VCard $vCard) {
        if (!isset($vCard->BDAY)) {
            return null;
        }

        $sValue = trim((string) $vCard->BDAY->getValue());
        if ($sValue === "") {
            return null;
        }

        try {
            $aParts = VObject\DateTimeParser::parseVCardDateTime($sValue);
        } catch (\Exception $e) {
            return null;
        }

        if ($aParts["month"] === null || $aParts["date"] === null) {
            return null;
        }

        $year = $aParts["year"] === null ? null : intval($aParts["year"]);
        $month = intval($aParts["month"]);
        $day = intval($aParts["date"]);

        # Apple clients store birthdays without year as e.g. "1604-03-15"
        # with the parameter X-APPLE-OMIT-YEAR=1604
        if ($year !== null && isset($vCard->BDAY["X-APPLE-OMIT-YEAR"])
            && intval((string) $vCard->BDAY["X-APPLE-OMIT-YEAR"]) === $year) {
            $year = null;
        }

        if (!checkdate($month, $day, $year ?? self::PLACEHOLDER_YEAR)) {
            return null;
        }

        return [$year, $month, $day];
    }

    /**
     * Returns the display name of the contact, falling back to the
     * structured name if FN is empty.
     *
     * @return string
     */
    protected function getName(VObject\Component\VCard $vCard) {
        if (isset($vCard->FN)) {
            $sName = trim((string) $vCard->FN->getValue());
            if ($sName !== "") {
                return $sName;
            }
        }

        if (isset($vCard->N)) {
            # N: family;given;additional;prefix;suffix
            $aParts = $vCard->N->getParts();
            $aName = array_filter(
                [$aParts[1] ?? "", $aParts[0] ?? ""],
                function ($sPart) { return trim($sPart) !== ""; }
            );

            return trim(implode(" ", $aName));
        }

        return "";
    }

    /**
     * @param int $year
     * @param int $month
     * @param int $day
     *
     * @return string
     */
    protected function formatDate($year, $month, $day) {
        return sprintf("%04d%02d%02d", $year, $month, $day);
    }
}
