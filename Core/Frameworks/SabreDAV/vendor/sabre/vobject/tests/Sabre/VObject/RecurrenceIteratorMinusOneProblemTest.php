<?php

namespace Sabre\VObject;

class RecurrenceIteratorMinusOneProblemTest extends \PHPUnit_Framework_TestCase {

    function testMinusOne() {

        $ics = <<<ICS
BEGIN:VCALENDAR
BEGIN:VEVENT
DTSTAMP:20120314T203127Z
UID:foo
SUMMARY:foo
RRULE:FREQ=YEARLY;UNTIL=20120314
DTSTART;VALUE=DATE:20120315
DTEND;VALUE=DATE:20120316
SEQUENCE:1
END:VEVENT
END:VCALENDAR
ICS;

        $vObject = Reader::read($ics);
        $it = new RecurrenceIterator($vObject, (string)$vObject->VEVENT->UID);

        $this->assertTrue($it->valid());

    }

}
