<?php

namespace Baikal\RestBundle\Controller;

use Baikal\ModelBundle\Entity\Calendar;

abstract class AbstractEventController {

    protected abstract function getTimezoneHelper();

    protected function getRequestedDates(Calendar $calendar, $start, $end) {

        $calendartimezone = $this->getTimezoneHelper()->extractTimeZoneFromDavString($calendar->getTimezone());
        $interval1Y = new \DateInterval('P1Y');

        if(is_null($start)) {
            $dtStart = new \DateTime(); $dtStart->setTimezone($calendartimezone);
            $dtStart->sub($interval1Y)->setTime(0, 0, 0);
        } else {
            $dtStart = new \DateTime($start);
        }

        if(is_null($end)) {
            $dtEnd = clone $dtStart;
            $dtEnd->add($interval1Y);
            if(is_null($start)) {
                $dtEnd->add($interval1Y)->setTime(0, 0, 0);
            }
        } else {
            $dtEnd = new \DateTime($end);
        }

        return array(
            'start' => $dtStart,
            'end' => $dtEnd,
            'calendartimezone' => $calendartimezone
        );
    }
}
