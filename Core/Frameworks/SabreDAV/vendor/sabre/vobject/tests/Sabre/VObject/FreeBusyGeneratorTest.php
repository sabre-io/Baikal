<?php

namespace Sabre\VObject;

class FreeBusyGeneratorTest extends \PHPUnit_Framework_TestCase {

    function getInput() {

        // shows up
$blob1 = <<<ICS
BEGIN:VCALENDAR
BEGIN:VEVENT
DTSTART:20110101T120000Z
DTEND:20110101T130000Z
END:VEVENT
END:VCALENDAR
ICS;

    // opaque, shows up
$blob2 = <<<ICS
BEGIN:VCALENDAR
BEGIN:VEVENT
TRANSP:OPAQUE
DTSTART:20110101T130000Z
DTEND:20110101T140000Z
END:VEVENT
END:VCALENDAR
ICS;

    // transparent, hidden
$blob3 = <<<ICS
BEGIN:VCALENDAR
BEGIN:VEVENT
TRANSP:TRANSPARENT
DTSTART:20110101T140000Z
DTEND:20110101T150000Z
END:VEVENT
END:VCALENDAR
ICS;

    // cancelled, hidden
$blob4 = <<<ICS
BEGIN:VCALENDAR
BEGIN:VEVENT
STATUS:CANCELLED
DTSTART:20110101T160000Z
DTEND:20110101T170000Z
END:VEVENT
END:VCALENDAR
ICS;

    // tentative, shows up
$blob5 = <<<ICS
BEGIN:VCALENDAR
BEGIN:VEVENT
STATUS:TENTATIVE
DTSTART:20110101T180000Z
DTEND:20110101T190000Z
END:VEVENT
END:VCALENDAR
ICS;

    // outside of time-range, hidden
$blob6 = <<<ICS
BEGIN:VCALENDAR
BEGIN:VEVENT
DTSTART:20110101T090000Z
DTEND:20110101T100000Z
END:VEVENT
END:VCALENDAR
ICS;

    // outside of time-range, hidden
$blob7 = <<<ICS
BEGIN:VCALENDAR
BEGIN:VEVENT
DTSTART:20110104T090000Z
DTEND:20110104T100000Z
END:VEVENT
END:VCALENDAR
ICS;

    // using duration, shows up
$blob8 = <<<ICS
BEGIN:VCALENDAR
BEGIN:VEVENT
DTSTART:20110101T190000Z
DURATION:PT1H
END:VEVENT
END:VCALENDAR
ICS;

    // Day-long event, shows up
$blob9 = <<<ICS
BEGIN:VCALENDAR
BEGIN:VEVENT
DTSTART;TYPE=DATE:20110102
END:VEVENT
END:VCALENDAR
ICS;

// No duration, does not show up
$blob10 = <<<ICS
BEGIN:VCALENDAR
BEGIN:VEVENT
DTSTART:20110101T200000Z
END:VEVENT
END:VCALENDAR
ICS;

// encoded as object, shows up
$blob11 = <<<ICS
BEGIN:VCALENDAR
BEGIN:VEVENT
DTSTART:20110101T210000Z
DURATION:PT1H
END:VEVENT
END:VCALENDAR
ICS;

// Freebusy. Some parts show up
$blob12 = <<<ICS
BEGIN:VCALENDAR
BEGIN:VFREEBUSY
FREEBUSY:20110103T010000Z/20110103T020000Z
FREEBUSY;FBTYPE=FREE:20110103T020000Z/20110103T030000Z
FREEBUSY:20110103T030000Z/20110103T040000Z,20110103T040000Z/20110103T050000Z
FREEBUSY:20120101T000000Z/20120101T010000Z
FREEBUSY:20110103T050000Z/PT1H
END:VFREEBUSY
END:VCALENDAR
ICS;

// Yearly recurrence rule, shows up
$blob13 = <<<ICS
BEGIN:VCALENDAR
BEGIN:VEVENT
DTSTART:20100101T220000Z
DTEND:20100101T230000Z
RRULE:FREQ=YEARLY
END:VEVENT
END:VCALENDAR
ICS;

// Yearly recurrence rule + duration, shows up
$blob14 = <<<ICS
BEGIN:VCALENDAR
BEGIN:VEVENT
DTSTART:20100101T230000Z
DURATION:PT1H
RRULE:FREQ=YEARLY
END:VEVENT
END:VCALENDAR
ICS;


        return array(
            $blob1,
            $blob2,
            $blob3,
            $blob4,
            $blob5,
            $blob6,
            $blob7,
            $blob8,
            $blob9,
            $blob10,
            Reader::read($blob11),
            $blob12,
            $blob13,
            $blob14,
        );

    }

    function testGenerator() {

        $gen = new FreeBusyGenerator(
            new \DateTime('20110101T110000Z', new \DateTimeZone('UTC')),
            new \DateTime('20110103T110000Z', new \DateTimeZone('UTC')),
            $this->getInput()
        );

        $result = $gen->getResult();

        $expected = array(
            '20110101T120000Z/20110101T130000Z',
            '20110101T130000Z/20110101T140000Z',
            '20110101T180000Z/20110101T190000Z',
            '20110101T190000Z/20110101T200000Z',
            '20110102T000000Z/20110103T000000Z',
            '20110101T210000Z/20110101T220000Z',

            '20110103T010000Z/20110103T020000Z',
            '20110103T030000Z/20110103T040000Z',
            '20110103T040000Z/20110103T050000Z',
            '20110103T050000Z/20110103T060000Z',

            '20110101T220000Z/20110101T230000Z',
            '20110101T230000Z/20110102T000000Z',
        );

        foreach($result->VFREEBUSY->FREEBUSY as $fb) {

            $this->assertContains((string)$fb, $expected);

            $k = array_search((string)$fb, $expected);
            unset($expected[$k]);

        }
        if (count($expected)>0) {
            $this->fail('There were elements in the expected array that were not found in the output: ' . "\n"  . print_r($expected,true) . "\n" . $result->serialize());

        }

    }

    function testGeneratorBaseObject() {

        $obj = new Component('VCALENDAR');
        $obj->METHOD = 'PUBLISH';

        $gen = new FreeBusyGenerator();
        $gen->setObjects(array());
        $gen->setBaseObject($obj);

        $result = $gen->getResult();

        $this->assertEquals('PUBLISH', $result->METHOD->value);

    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testInvalidArg() {

        $gen = new FreeBusyGenerator(
            new \DateTime('2012-01-01'),
            new \DateTime('2012-12-31'),
            new \StdClass()
        );

    }

}
