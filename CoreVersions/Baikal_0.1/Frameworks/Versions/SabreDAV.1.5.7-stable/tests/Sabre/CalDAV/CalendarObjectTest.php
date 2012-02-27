<?php

require_once 'Sabre/CalDAV/TestUtil.php';
require_once 'Sabre/DAV/Auth/MockBackend.php';
require_once 'Sabre/DAVACL/MockPrincipalBackend.php';
require_once 'Sabre/CalDAV/Backend/Mock.php';

class Sabre_CalDAV_CalendarObjectTest extends PHPUnit_Framework_TestCase {

    protected $backend;
    protected $calendar;
    protected $principalBackend;

    function setup() {

        if (!SABRE_HASSQLITE) $this->markTestSkipped('SQLite driver is not available');
        $this->backend = Sabre_CalDAV_TestUtil::getBackend();
        $this->principalBackend = new Sabre_DAVACL_MockPrincipalBackend;

        $calendars = $this->backend->getCalendarsForUser('principals/user1');
        $this->assertEquals(1,count($calendars));
        $this->calendar = new Sabre_CalDAV_Calendar($this->principalBackend,$this->backend, $calendars[0]);

    }

    function teardown() {

        unset($this->calendar);
        unset($this->backend);

    }

    function testSetup() {

        $children = $this->calendar->getChildren();
        $this->assertTrue($children[0] instanceof Sabre_CalDAV_CalendarObject);
        
        $this->assertInternalType('string',$children[0]->getName());
        $this->assertInternalType('string',$children[0]->get());
        $this->assertInternalType('string',$children[0]->getETag());
        $this->assertEquals('text/calendar', $children[0]->getContentType());

    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testInvalidArg1() {

        $obj = new Sabre_CalDAV_CalendarObject(
            new Sabre_CalDAV_Backend_Mock(array()),
            array(),
            array()
        );

    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testInvalidArg2() {

        $obj = new Sabre_CalDAV_CalendarObject(
            new Sabre_CalDAV_Backend_Mock(array()),
            array(),
            array('calendarid' => '1')
        );

    }

    /**
     * @depends testSetup
     */
    function testPut() {

        $children = $this->calendar->getChildren();
        $this->assertTrue($children[0] instanceof Sabre_CalDAV_CalendarObject);
        $newData = Sabre_CalDAV_TestUtil::getTestCalendarData();

        $children[0]->put($newData);
        $this->assertEquals($newData, $children[0]->get());

    }

    /**
     * @depends testSetup
     */
    function testDelete() {

        $children = $this->calendar->getChildren();
        $this->assertTrue($children[0] instanceof Sabre_CalDAV_CalendarObject);
        
        $obj = $children[0];
        $obj->delete();

        $children2 =  $this->calendar->getChildren();
        $this->assertEquals(count($children)-1, count($children2));

    }

    /**
     * @depends testSetup
     */
    function testGetLastModified() {

        $children = $this->calendar->getChildren();
        $this->assertTrue($children[0] instanceof Sabre_CalDAV_CalendarObject);
        
        $obj = $children[0];

        $lastMod = $obj->getLastModified();
        $this->assertTrue(is_int($lastMod) || ctype_digit($lastMod));

    }

    /**
     * @depends testSetup
     */
    function testGetSize() {

        $children = $this->calendar->getChildren();
        $this->assertTrue($children[0] instanceof Sabre_CalDAV_CalendarObject);
        
        $obj = $children[0];

        $size = $obj->getSize();
        $this->assertInternalType('int', $size);

    }

    function testGetOwner() {

        $children = $this->calendar->getChildren();
        $this->assertTrue($children[0] instanceof Sabre_CalDAV_CalendarObject);
        
        $obj = $children[0];
        $this->assertEquals('principals/user1', $obj->getOwner());

    }

    function testGetGroup() {

        $children = $this->calendar->getChildren();
        $this->assertTrue($children[0] instanceof Sabre_CalDAV_CalendarObject);
        
        $obj = $children[0];
        $this->assertNull($obj->getGroup());

    }

    function testGetACL() {

        $expected = array(
            array(
                'privilege' => '{DAV:}read',
                'principal' => 'principals/user1',
                'protected' => true,
            ),
            array(
                'privilege' => '{DAV:}write',
                'principal' => 'principals/user1',
                'protected' => true,
            ),
            array(
                'privilege' => '{DAV:}read',
                'principal' => 'principals/user1/calendar-proxy-write',
                'protected' => true,
            ),
            array(
                'privilege' => '{DAV:}write',
                'principal' => 'principals/user1/calendar-proxy-write',
                'protected' => true,
            ),
            array(
                'privilege' => '{DAV:}read',
                'principal' => 'principals/user1/calendar-proxy-read',
                'protected' => true,
            ),
        );

        $children = $this->calendar->getChildren();
        $this->assertTrue($children[0] instanceof Sabre_CalDAV_CalendarObject);
        
        $obj = $children[0];
        $this->assertEquals($expected, $obj->getACL());

    }

    /**
     * @expectedException Sabre_DAV_Exception_MethodNotAllowed
     */
    function testSetACL() {

        $children = $this->calendar->getChildren();
        $this->assertTrue($children[0] instanceof Sabre_CalDAV_CalendarObject);
        
        $obj = $children[0];
        $obj->setACL(array());

    }

    function testGet() {

        $children = $this->calendar->getChildren();
        $this->assertTrue($children[0] instanceof Sabre_CalDAV_CalendarObject);
        
        $obj = $children[0];

            $expected = "BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//Apple Inc.//iCal 4.0.1//EN
CALSCALE:GREGORIAN
BEGIN:VTIMEZONE
TZID:Asia/Seoul
BEGIN:DAYLIGHT
TZOFFSETFROM:+0900
RRULE:FREQ=YEARLY;UNTIL=19880507T150000Z;BYMONTH=5;BYDAY=2SU
DTSTART:19870510T000000
TZNAME:GMT+09:00
TZOFFSETTO:+1000
END:DAYLIGHT
BEGIN:STANDARD
TZOFFSETFROM:+1000
DTSTART:19881009T000000
TZNAME:GMT+09:00
TZOFFSETTO:+0900
END:STANDARD
END:VTIMEZONE
BEGIN:VEVENT
CREATED:20100225T154229Z
UID:39A6B5ED-DD51-4AFE-A683-C35EE3749627
TRANSP:TRANSPARENT
SUMMARY:Something here
DTSTAMP:20100228T130202Z
DTSTART;TZID=Asia/Seoul:20100223T060000
DTEND;TZID=Asia/Seoul:20100223T070000
ATTENDEE;PARTSTAT=NEEDS-ACTION:mailto:lisa@example.com
SEQUENCE:2
END:VEVENT
END:VCALENDAR";



        $this->assertEquals($expected, $obj->get());

    }

    function testGetRefetch() {

        $backend = new Sabre_CalDAV_Backend_Mock(array(
            1 => array(
                'foo' => array(
                    'calendardata' => 'foo',
                    'uri' => 'foo'
                ),
            )
        ));
        $obj = new Sabre_CalDAV_CalendarObject($backend, array(), array('calendarid' => 1, 'uri' => 'foo'));

        $this->assertEquals('foo', $obj->get());

    }

    function testGetEtag1() {

        $objectInfo = array(
            'calendardata' => 'foo',
            'uri' => 'foo',
            'etag' => 'bar',
            'calendarid' => 1
        );

        $backend = new Sabre_CalDAV_Backend_Mock(array());
        $obj = new Sabre_CalDAV_CalendarObject($backend, array(), $objectInfo);

        $this->assertEquals('bar', $obj->getETag());

    }

    function testGetEtag2() {

        $objectInfo = array(
            'calendardata' => 'foo',
            'uri' => 'foo',
            'calendarid' => 1
        );

        $backend = new Sabre_CalDAV_Backend_Mock(array());
        $obj = new Sabre_CalDAV_CalendarObject($backend, array(), $objectInfo);

        $this->assertEquals('"' . md5('foo') . '"', $obj->getETag());

    }
}
