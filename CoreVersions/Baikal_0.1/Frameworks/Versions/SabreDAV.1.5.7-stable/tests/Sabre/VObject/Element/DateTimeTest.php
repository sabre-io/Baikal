<?php

class Sabre_VObject_Element_DateTimeTest extends PHPUnit_Framework_TestCase {

    function testSetDateTime() {

        $tz = new DateTimeZone('Europe/Amsterdam');
        $dt = new DateTime('1985-07-04 01:30:00', $tz);
        $dt->setTimeZone($tz);

        $elem = new Sabre_VObject_Element_DateTime('DTSTART');
        $elem->setDateTime($dt);

        $this->assertEquals('19850704T013000', $elem->value);
        $this->assertEquals('Europe/Amsterdam', (string)$elem['TZID']);
        $this->assertEquals('DATE-TIME', (string)$elem['VALUE']);

    }

    function testSetDateTimeLOCAL() {

        $tz = new DateTimeZone('Europe/Amsterdam');
        $dt = new DateTime('1985-07-04 01:30:00', $tz);
        $dt->setTimeZone($tz);

        $elem = new Sabre_VObject_Element_DateTime('DTSTART');
        $elem->setDateTime($dt, Sabre_VObject_Element_DateTime::LOCAL);

        $this->assertEquals('19850704T013000', $elem->value);
        $this->assertNull($elem['TZID']);
        $this->assertEquals('DATE-TIME', (string)$elem['VALUE']);

    }

    function testSetDateTimeUTC() {

        $tz = new DateTimeZone('GMT');
        $dt = new DateTime('1985-07-04 01:30:00', $tz);
        $dt->setTimeZone($tz);

        $elem = new Sabre_VObject_Element_DateTime('DTSTART');
        $elem->setDateTime($dt, Sabre_VObject_Element_DateTime::UTC);

        $this->assertEquals('19850704T013000Z', $elem->value);
        $this->assertNull($elem['TZID']);
        $this->assertEquals('DATE-TIME', (string)$elem['VALUE']);

    }

    function testSetDateTimeLOCALTZ() {

        $tz = new DateTimeZone('Europe/Amsterdam');
        $dt = new DateTime('1985-07-04 01:30:00', $tz);
        $dt->setTimeZone($tz);

        $elem = new Sabre_VObject_Element_DateTime('DTSTART');
        $elem->setDateTime($dt, Sabre_VObject_Element_DateTime::LOCALTZ);

        $this->assertEquals('19850704T013000', $elem->value);
        $this->assertEquals('Europe/Amsterdam', (string)$elem['TZID']);
        $this->assertEquals('DATE-TIME', (string)$elem['VALUE']);

    }

    function testSetDateTimeDATE() {

        $tz = new DateTimeZone('Europe/Amsterdam');
        $dt = new DateTime('1985-07-04 01:30:00', $tz);
        $dt->setTimeZone($tz);

        $elem = new Sabre_VObject_Element_DateTime('DTSTART');
        $elem->setDateTime($dt, Sabre_VObject_Element_DateTime::DATE);

        $this->assertEquals('19850704', $elem->value);
        $this->assertNull($elem['TZID']);
        $this->assertEquals('DATE', (string)$elem['VALUE']);

    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testSetDateTimeInvalid() {

        $tz = new DateTimeZone('Europe/Amsterdam');
        $dt = new DateTime('1985-07-04 01:30:00', $tz);
        $dt->setTimeZone($tz);

        $elem = new Sabre_VObject_Element_DateTime('DTSTART');
        $elem->setDateTime($dt, 7);

    }

    function testGetDateTimeCached() {

        $tz = new DateTimeZone('Europe/Amsterdam');
        $dt = new DateTime('1985-07-04 01:30:00', $tz);
        $dt->setTimeZone($tz);

        $elem = new Sabre_VObject_Element_DateTime('DTSTART');
        $elem->setDateTime($dt);

        $this->assertEquals($elem->getDateTime(), $dt);

    }

    function testGetDateTimeDateNULL() {

        $elem = new Sabre_VObject_Element_DateTime('DTSTART');
        $dt = $elem->getDateTime();

        $this->assertNull($dt);
        $this->assertNull($elem->getDateType());

    }

    function testGetDateTimeDateDATE() {

        $elem = new Sabre_VObject_Element_DateTime('DTSTART','19850704');
        $dt = $elem->getDateTime();

        $this->assertInstanceOf('DateTime', $dt);
        $this->assertEquals('1985-07-04 00:00:00', $dt->format('Y-m-d H:i:s'));
        $this->assertEquals(Sabre_VObject_Element_DateTime::DATE, $elem->getDateType());

    }


    function testGetDateTimeDateLOCAL() {

        $elem = new Sabre_VObject_Element_DateTime('DTSTART','19850704T013000');
        $dt = $elem->getDateTime();

        $this->assertInstanceOf('DateTime', $dt);
        $this->assertEquals('1985-07-04 01:30:00', $dt->format('Y-m-d H:i:s'));
        $this->assertEquals(Sabre_VObject_Element_DateTime::LOCAL, $elem->getDateType());

    }

    function testGetDateTimeDateUTC() {

        $elem = new Sabre_VObject_Element_DateTime('DTSTART','19850704T013000Z');
        $dt = $elem->getDateTime();

        $this->assertInstanceOf('DateTime', $dt);
        $this->assertEquals('1985-07-04 01:30:00', $dt->format('Y-m-d H:i:s'));
        $this->assertEquals('UTC', $dt->getTimeZone()->getName());
        $this->assertEquals(Sabre_VObject_Element_DateTime::UTC, $elem->getDateType());

    }

    function testGetDateTimeDateLOCALTZ() {

        $elem = new Sabre_VObject_Element_DateTime('DTSTART','19850704T013000');
        $elem['TZID'] = 'Europe/Amsterdam';

        $dt = $elem->getDateTime();

        $this->assertInstanceOf('DateTime', $dt);
        $this->assertEquals('1985-07-04 01:30:00', $dt->format('Y-m-d H:i:s'));
        $this->assertEquals('Europe/Amsterdam', $dt->getTimeZone()->getName());
        $this->assertEquals(Sabre_VObject_Element_DateTime::LOCALTZ, $elem->getDateType());

    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testGetDateTimeDateInvalid() {

        $elem = new Sabre_VObject_Element_DateTime('DTSTART','bla');
        $dt = $elem->getDateTime();

    }

    function testGetDateTimeWeirdTZ() {

        $elem = new Sabre_VObject_Element_DateTime('DTSTART','19850704T013000');
        $elem['TZID'] = '/freeassociation.sourceforge.net/Tzfile/Europe/Amsterdam';


        $event = new Sabre_VObject_Component('VEVENT');
        $event->add($elem);

        $timezone = new Sabre_VObject_Component('VTIMEZONE');
        $timezone->TZID = '/freeassociation.sourceforge.net/Tzfile/Europe/Amsterdam';
        $timezone->{'X-LIC-LOCATION'} = 'Europe/Amsterdam';

        $calendar = new Sabre_VObject_Component('VCALENDAR');
        $calendar->add($event);
        $calendar->add($timezone);

        $dt = $elem->getDateTime();

        $this->assertInstanceOf('DateTime', $dt);
        $this->assertEquals('1985-07-04 01:30:00', $dt->format('Y-m-d H:i:s'));
        $this->assertEquals('Europe/Amsterdam', $dt->getTimeZone()->getName());
        $this->assertEquals(Sabre_VObject_Element_DateTime::LOCALTZ, $elem->getDateType());

    }

}

?>
