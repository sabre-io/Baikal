<?php

namespace Baikal\Core;

/**
 * Turns an IANA timezone identifier into a VTIMEZONE iCalendar object.
 *
 * The caldav:calendar-timezone property must contain a VCALENDAR with exactly
 * one VTIMEZONE (RFC 4791, section 5.2.2). Storing a bare identifier such as
 * "Europe/Berlin" makes SabreDAV throw a ParseException as soon as a client
 * sends a calendar-query with the <C:expand> element, because it tries to read
 * that property as iCalendar data. The request fails with HTTP 500, which in
 * turn breaks every integration relying on expansion: cal.com, Home Assistant
 * and the ICS export with ?expand=1.
 *
 * @see https://github.com/sabre-io/Baikal/issues/1241
 */
class TimeZone {
    private const PRODID = "-//Baikal//NONSGML Baikal//EN";

    /** Years of transition data inspected to detect a repeating rule. */
    private const LOOKBACK_YEARS = 6;

    /**
     * Years of transitions written out when no repeating rule can be derived.
     * Clients that resolve TZID themselves do not need this, but the ones
     * reading the observances should not run out of data too early.
     */
    private const LOOKAHEAD_YEARS = 10;

    /**
     * Returns a VCALENDAR holding a single VTIMEZONE for the given identifier,
     * or null when the identifier is not a valid timezone.
     */
    public static function toVTimezone(string $tzid): ?string {
        $tzid = trim($tzid);
        if ($tzid === "") {
            return null;
        }

        try {
            $timezone = new \DateTimeZone($tzid);
        } catch (\Throwable $e) {
            return null;
        }

        return "BEGIN:VCALENDAR\r\n"
            . "VERSION:2.0\r\n"
            . "PRODID:" . self::PRODID . "\r\n"
            . "CALSCALE:GREGORIAN\r\n"
            . "BEGIN:VTIMEZONE\r\n"
            . "TZID:" . $tzid . "\r\n"
            . self::observances($timezone)
            . "END:VTIMEZONE\r\n"
            . "END:VCALENDAR\r\n";
    }

    /**
     * Renders the STANDARD and DAYLIGHT observances of a timezone.
     */
    private static function observances(\DateTimeZone $timezone): string {
        $now = new \DateTimeImmutable("now", new \DateTimeZone("UTC"));
        $transitions = $timezone->getTransitions(
            $now->modify("-" . self::LOOKBACK_YEARS . " years")->getTimestamp(),
            $now->modify("+" . self::LOOKAHEAD_YEARS . " years")->getTimestamp()
        );

        $initial = $transitions[0] ?? null;
        $byType = ["DAYLIGHT" => [], "STANDARD" => []];
        $previousOffset = null;

        foreach ($transitions as $transition) {
            $offset = (int) $transition["offset"];
            if ($previousOffset === null) {
                // First entry describes the state at the start of the range.
                $previousOffset = $offset;
                continue;
            }

            // Wall clock time right before the change, as required by DTSTART.
            $local = (new \DateTimeImmutable("now", new \DateTimeZone("UTC")))
                ->setTimestamp((int) $transition["ts"] + $previousOffset);

            $byType[empty($transition["isdst"]) ? "STANDARD" : "DAYLIGHT"][] = [
                "local" => $local,
                "from" => $previousOffset,
                "to" => $offset,
                "abbr" => (string) $transition["abbr"],
            ];

            $previousOffset = $offset;
        }

        $rendered = "";
        foreach ($byType as $name => $list) {
            if ($list !== []) {
                $rendered .= self::observance($name, $list);
            }
        }

        if ($rendered !== "") {
            return $rendered;
        }

        // Timezones without transitions (UTC, Asia/Kolkata, ...): a single
        // observance that applies at all times.
        $offset = $initial !== null ? (int) $initial["offset"] : 0;
        $abbr = $initial !== null ? (string) $initial["abbr"] : $timezone->getName();

        return "BEGIN:STANDARD\r\n"
            . "DTSTART:19700101T000000\r\n"
            . "TZOFFSETFROM:" . self::formatOffset($offset) . "\r\n"
            . "TZOFFSETTO:" . self::formatOffset($offset) . "\r\n"
            . "TZNAME:" . $abbr . "\r\n"
            . "END:STANDARD\r\n";
    }

    /**
     * Renders one observance type. A yearly rule is used when all collected
     * transitions follow the same pattern, otherwise every transition is
     * written out explicitly.
     */
    private static function observance(string $name, array $list): string {
        $first = $list[0];
        $rule = self::yearlyRule($list);

        if ($rule !== null) {
            return "BEGIN:" . $name . "\r\n"
                . "DTSTART:" . $first["local"]->format("Ymd\THis") . "\r\n"
                . "TZOFFSETFROM:" . self::formatOffset($first["from"]) . "\r\n"
                . "TZOFFSETTO:" . self::formatOffset($first["to"]) . "\r\n"
                . "RRULE:FREQ=YEARLY;BYMONTH=" . $first["local"]->format("n")
                . ";BYDAY=" . $rule . "\r\n"
                . "TZNAME:" . $first["abbr"] . "\r\n"
                . "END:" . $name . "\r\n";
        }

        $rendered = "";
        foreach ($list as $entry) {
            $rendered .= "BEGIN:" . $name . "\r\n"
                . "DTSTART:" . $entry["local"]->format("Ymd\THis") . "\r\n"
                . "TZOFFSETFROM:" . self::formatOffset($entry["from"]) . "\r\n"
                . "TZOFFSETTO:" . self::formatOffset($entry["to"]) . "\r\n"
                . "TZNAME:" . $entry["abbr"] . "\r\n"
                . "END:" . $name . "\r\n";
        }

        return $rendered;
    }

    /**
     * Returns the BYDAY value of a yearly rule when all transitions of one type
     * follow the same pattern, or null when they do not.
     *
     * Both ways of counting are tried, because the same rule can be the n-th
     * weekday of a month from the start (2SU) or from the end (-1SU) depending
     * on the month length.
     */
    private static function yearlyRule(array $list): ?string {
        if (count($list) < 2) {
            return null;
        }

        $expected = self::signature($list[0]);
        foreach ($list as $entry) {
            if (self::signature($entry) !== $expected) {
                return null;
            }
        }

        foreach ([false, true] as $fromEnd) {
            $ordinals = array_unique(array_map(
                fn (array $entry) => self::ordinal($entry["local"], $fromEnd),
                $list
            ));
            if (count($ordinals) === 1) {
                return reset($ordinals);
            }
        }

        return null;
    }

    /**
     * Properties that have to match for a yearly rule to be correct.
     */
    private static function signature(array $entry): string {
        return implode("|", [
            $entry["local"]->format("n"),
            $entry["local"]->format("w"),
            $entry["from"],
            $entry["to"],
            $entry["abbr"],
        ]);
    }

    /**
     * Builds a BYDAY value such as -1SU or 2SU.
     */
    private static function ordinal(\DateTimeImmutable $local, bool $fromEnd): string {
        $weekdays = ["SU", "MO", "TU", "WE", "TH", "FR", "SA"];
        $weekday = $weekdays[(int) $local->format("w")];
        $day = (int) $local->format("j");

        $ordinal = $fromEnd
            ? -1 - intdiv((int) $local->format("t") - $day, 7)
            : intdiv($day - 1, 7) + 1;

        return $ordinal . $weekday;
    }

    private static function formatOffset(int $seconds): string {
        $sign = $seconds < 0 ? "-" : "+";
        $seconds = abs($seconds);

        return sprintf("%s%02d%02d", $sign, intdiv($seconds, 3600), intdiv($seconds % 3600, 60));
    }
}
