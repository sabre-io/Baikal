<?php

namespace Sabre\VObject;

use DateTime;
use DateTimeZone;
use DateInterval;

class DateTimeParserTest extends \PHPUnit_Framework_TestCase {

    function testParseICalendarDuration() {

        $this->assertEquals('+1 weeks', DateTimeParser::parseDuration('P1W',true));
        $this->assertEquals('+5 days',  DateTimeParser::parseDuration('P5D',true));
        $this->assertEquals('+5 days 3 hours 50 minutes 12 seconds', DateTimeParser::parseDuration('P5DT3H50M12S',true));
        $this->assertEquals('-1 weeks 50 minutes', DateTimeParser::parseDuration('-P1WT50M',true));
        $this->assertEquals('+50 days 3 hours 2 seconds', DateTimeParser::parseDuration('+P50DT3H2S',true));
        $this->assertEquals(new DateInterval('PT0S'), DateTimeParser::parseDuration('PT0S'));

    }

    function testParseICalendarDurationDateInterval() {

        $expected = new DateInterval('P7D');
        $this->assertEquals($expected, DateTimeParser::parseDuration('P1W'));
        $this->assertEquals($expected, DateTimeParser::parse('P1W'));

        $expected = new DateInterval('PT3M');
        $expected->invert = true;
        $this->assertEquals($expected, DateTimeParser::parseDuration('-PT3M'));

    }

    /**
     * @expectedException LogicException
     */
    function testParseICalendarDurationFail() {

        DateTimeParser::parseDuration('P1X',true);

    }

    function testParseICalendarDateTime() {

        $dateTime = DateTimeParser::parseDateTime('20100316T141405');

        $compare = new DateTime('2010-03-16 14:14:05',new DateTimeZone('UTC'));

        $this->assertEquals($compare, $dateTime);

    }

    /**
     * @depends testParseICalendarDateTime
     * @expectedException LogicException
     */
    function testParseICalendarDateTimeBadFormat() {

        $dateTime = DateTimeParser::parseDateTime('20100316T141405 ');

    }

    /**
     * @depends testParseICalendarDateTime
     */
    function testParseICalendarDateTimeUTC() {

        $dateTime = DateTimeParser::parseDateTime('20100316T141405Z');

        $compare = new DateTime('2010-03-16 14:14:05',new DateTimeZone('UTC'));
        $this->assertEquals($compare, $dateTime);

    }

    /**
     * @depends testParseICalendarDateTime
     */
    function testParseICalendarDateTimeUTC2() {

        $dateTime = DateTimeParser::parseDateTime('20101211T160000Z');

        $compare = new DateTime('2010-12-11 16:00:00',new DateTimeZone('UTC'));
        $this->assertEquals($compare, $dateTime);

    }

    /**
     * @depends testParseICalendarDateTime
     */
    function testParseICalendarDateTimeCustomTimeZone() {

        $dateTime = DateTimeParser::parseDateTime('20100316T141405', new DateTimeZone('Europe/Amsterdam'));

        $compare = new DateTime('2010-03-16 13:14:05',new DateTimeZone('UTC'));
        $this->assertEquals($compare, $dateTime);

    }

    function testParseICalendarDate() {

        $dateTime = DateTimeParser::parseDate('20100316');

        $expected = new DateTime('2010-03-16 00:00:00',new DateTimeZone('UTC'));

        $this->assertEquals($expected, $dateTime);

        $dateTime = DateTimeParser::parse('20100316');
        $this->assertEquals($expected, $dateTime);

    }

    /**
     * TCheck if a date with year > 4000 will not throw an exception. iOS seems to use 45001231 in yearly recurring events
     */
    function testParseICalendarDateGreaterThan4000() {

        $dateTime = DateTimeParser::parseDate('45001231');

        $expected = new DateTime('4500-12-31 00:00:00',new DateTimeZone('UTC'));

        $this->assertEquals($expected, $dateTime);

        $dateTime = DateTimeParser::parse('45001231');
        $this->assertEquals($expected, $dateTime);

    }

    /**
     * Check if a datetime with year > 4000 will not throw an exception. iOS seems to use 45001231T235959 in yearly recurring events
     */
    function testParseICalendarDateTimeGreaterThan4000() {

        $dateTime = DateTimeParser::parseDateTime('45001231T235959');

        $expected = new DateTime('4500-12-31 23:59:59',new DateTimeZone('UTC'));

        $this->assertEquals($expected, $dateTime);

        $dateTime = DateTimeParser::parse('45001231T235959');
        $this->assertEquals($expected, $dateTime);

    }

    /**
     * @depends testParseICalendarDate
     * @expectedException LogicException
     */
    function testParseICalendarDateBadFormat() {

        $dateTime = DateTimeParser::parseDate('20100316T141405');

    }
}
