<?php

namespace Sabre\VObject\Component;

use Sabre\VObject\Component;

class VJournalTest extends \PHPUnit_Framework_TestCase {

    /**
     * @dataProvider timeRangeTestData
     */
    public function testInTimeRange(VJournal $vtodo,$start,$end,$outcome) {

        $this->assertEquals($outcome, $vtodo->isInTimeRange($start, $end));

    }

    public function timeRangeTestData() {

        $tests = array();

        $vjournal = Component::create('VJOURNAL');
        $vjournal->DTSTART = '20111223T120000Z';
        $tests[] = array($vjournal, new \DateTime('2011-01-01'), new \DateTime('2012-01-01'), true);
        $tests[] = array($vjournal, new \DateTime('2011-01-01'), new \DateTime('2011-11-01'), false);

        $vjournal2 = Component::create('VJOURNAL');
        $vjournal2->DTSTART = '20111223';
        $vjournal2->DTSTART['VALUE'] = 'DATE';
        $tests[] = array($vjournal2, new \DateTime('2011-01-01'), new \DateTime('2012-01-01'), true);
        $tests[] = array($vjournal2, new \DateTime('2011-01-01'), new \DateTime('2011-11-01'), false);

        $vjournal3 = Component::create('VJOURNAL');
        $tests[] = array($vjournal3, new \DateTime('2011-01-01'), new \DateTime('2012-01-01'), false);
        $tests[] = array($vjournal3, new \DateTime('2011-01-01'), new \DateTime('2011-11-01'), false);

        return $tests;
    }

}

