<?php

class Sabre_VObject_Element_MultiDateTimeTest extends PHPUnit_Framework_TestCase {

    function testSetDateTime() {

        $tz = new DateTimeZone('Europe/Amsterdam');
        $dt1 = new DateTime('1985-07-04 01:30:00', $tz);
        $dt2 = new DateTime('1986-07-04 01:30:00', $tz);
        $dt1->setTimeZone($tz);
        $dt2->setTimeZone($tz);

        $elem = new Sabre_VObject_Element_MultiDateTime('DTSTART');
        $elem->setDateTimes(array($dt1,$dt2));

        $this->assertEquals('19850704T013000,19860704T013000', $elem->value);
        $this->assertEquals('Europe/Amsterdam', (string)$elem['TZID']);
        $this->assertEquals('DATE-TIME', (string)$elem['VALUE']);

    }

    function testSetDateTimeLOCAL() {

        $tz = new DateTimeZone('Europe/Amsterdam');
        $dt1 = new DateTime('1985-07-04 01:30:00', $tz);
        $dt2 = new DateTime('1986-07-04 01:30:00', $tz);
        $dt1->setTimeZone($tz);
        $dt2->setTimeZone($tz);

        $elem = new Sabre_VObject_Element_MultiDateTime('DTSTART');
        $elem->setDateTimes(array($dt1,$dt2), Sabre_VObject_Element_DateTime::LOCAL);

        $this->assertEquals('19850704T013000,19860704T013000', $elem->value);
        $this->assertNull($elem['TZID']);
        $this->assertEquals('DATE-TIME', (string)$elem['VALUE']);

    }

    function testSetDateTimeUTC() {

        $tz = new DateTimeZone('GMT');
        $dt1 = new DateTime('1985-07-04 01:30:00', $tz);
        $dt2 = new DateTime('1986-07-04 01:30:00', $tz);
        $dt1->setTimeZone($tz);
        $dt2->setTimeZone($tz);

        $elem = new Sabre_VObject_Element_MultiDateTime('DTSTART');
        $elem->setDateTimes(array($dt1,$dt2), Sabre_VObject_Element_DateTime::UTC);

        $this->assertEquals('19850704T013000Z,19860704T013000Z', $elem->value);
        $this->assertNull($elem['TZID']);
        $this->assertEquals('DATE-TIME', (string)$elem['VALUE']);

    }

    function testSetDateTimeLOCALTZ() {

        $tz = new DateTimeZone('Europe/Amsterdam');
        $dt1 = new DateTime('1985-07-04 01:30:00', $tz);
        $dt2 = new DateTime('1986-07-04 01:30:00', $tz);
        $dt1->setTimeZone($tz);
        $dt2->setTimeZone($tz);

        $elem = new Sabre_VObject_Element_MultiDateTime('DTSTART');
        $elem->setDateTimes(array($dt1,$dt2), Sabre_VObject_Element_DateTime::LOCALTZ);

        $this->assertEquals('19850704T013000,19860704T013000', $elem->value);
        $this->assertEquals('Europe/Amsterdam', (string)$elem['TZID']);
        $this->assertEquals('DATE-TIME', (string)$elem['VALUE']);

    }

    function testSetDateTimeDATE() {

        $tz = new DateTimeZone('Europe/Amsterdam');
        $dt1 = new datetime('1985-07-04 01:30:00', $tz);
        $dt2 = new datetime('1986-07-04 01:30:00', $tz);
        $dt1->settimezone($tz);
        $dt2->settimezone($tz);

        $elem = new Sabre_VObject_Element_MultiDateTime('DTSTART');
        $elem->setDateTimes(array($dt1,$dt2), Sabre_VObject_Element_DateTime::DATE);

        $this->assertEquals('19850704,19860704', $elem->value);
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

        $elem = new Sabre_VObject_Element_MultiDateTime('DTSTART');
        $elem->setDateTimes(array($dt), 7);

    }

    function testGetDateTimeCached() {

        $tz = new DateTimeZone('Europe/Amsterdam');
        $dt1 = new datetime('1985-07-04 01:30:00', $tz);
        $dt2 = new datetime('1986-07-04 01:30:00', $tz);
        $dt1->settimezone($tz);
        $dt2->settimezone($tz);

        $elem = new Sabre_VObject_Element_MultiDateTime('DTSTART');
        $elem->setDateTimes(array($dt1,$dt2));

        $this->assertEquals($elem->getDateTimes(), array($dt1,$dt2));

    }

    function testGetDateTimeDateNULL() {

        $elem = new Sabre_VObject_Element_MultiDateTime('DTSTART');
        $dt = $elem->getDateTimes();

        $this->assertNull($dt);
        $this->assertNull($elem->getDateType());

    }

    function testGetDateTimeDateDATE() {

        $elem = new Sabre_VObject_Element_MultiDateTime('DTSTART','19850704,19860704');
        $dt = $elem->getDateTimes();

        $this->assertEquals('1985-07-04 00:00:00', $dt[0]->format('Y-m-d H:i:s'));
        $this->assertEquals('1986-07-04 00:00:00', $dt[1]->format('Y-m-d H:i:s'));
        $this->assertEquals(Sabre_VObject_Element_DateTime::DATE, $elem->getDateType());

    }

    function testGetDateTimeDateDATEReverse() {

        $elem = new Sabre_VObject_Element_MultiDateTime('DTSTART','19850704,19860704');

        $this->assertEquals(Sabre_VObject_Element_DateTime::DATE, $elem->getDateType());

        $dt = $elem->getDateTimes();
        $this->assertEquals('1985-07-04 00:00:00', $dt[0]->format('Y-m-d H:i:s'));
        $this->assertEquals('1986-07-04 00:00:00', $dt[1]->format('Y-m-d H:i:s'));

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

}

?>
