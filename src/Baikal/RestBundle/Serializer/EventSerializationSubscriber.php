<?php

namespace Baikal\RestBundle\Serializer;

use JMS\Serializer\EventDispatcher\EventSubscriberInterface,
    JMS\Serializer\EventDispatcher\ObjectEvent,
    JMS\Serializer\EventDispatcher\Events as SerializerEvents;

use Sabre\VObject;

use Baikal\CoreBundle\Services\MainConfigService,
    Baikal\DavServicesBundle\Service\Helper\DavTimeZoneHelper;

class EventSerializationSubscriber implements EventSubscriberInterface {
    
    protected $mainconfig;

    public function __construct(
        MainConfigService $mainconfig,
        DavTimeZoneHelper $timezonehelper
    ) {
        $this->mainconfig = $mainconfig;
        $this->timezonehelper = $timezonehelper;
    }

    public static function getSubscribedEvents() {
        return array(
            array(
                'event' => SerializerEvents::POST_SERIALIZE,
                'class' => 'Baikal\ModelBundle\Entity\Event',
                'method' => 'onPostSerialize_Event'
            ),
        );
    }

    public function onPostSerialize_Event(ObjectEvent $event) {
        
        $expanded = (
            $event->getContext()->attributes->containsKey('expanded') &&
            $event->getContext()->attributes->get('expanded')->get()
        );

        $davevent = $event->getObject();
        $vobject = $davevent->getVObject();

        $davcalendar = $davevent->getCalendar();
        $calendartimezone = $this->timezonehelper->extractTimeZoneFromDavString($davcalendar->getTimezone());

        $event->getVisitor()->addData(
           'title',
           (string)$vobject->VEVENT->SUMMARY
        );

        $event->getVisitor()->addData(
            'busy',
            isset($vobject->VEVENT->TRANSP) && (
                strtoupper((string)$vobject->VEVENT->TRANSP) === 'OPAQUE'
            )
        );

        $recurrenceRule = $this->getRecurrenceRule($vobject->VEVENT);
        foreach($recurrenceRule as $key => $value) {
            $event->getVisitor()->addData(
                'recurrence_' . $key,
                $value
            );
        }

        if($expanded) {

            $occurences = array();
            $start = $event->getContext()->attributes->get('start')->get();
            $end = $event->getContext()->attributes->get('end')->get();

            $vobject->expand($start, $end);

            if(isset($vobject->VEVENT)) {
                foreach($vobject->VEVENT as $vobjevent) {
                    $occurences[] = $this->transformEvent($vobjevent, $calendartimezone);
                }
            }

            $event->getVisitor()->addData(
                'occurences',
                $occurences
            );

        } else {
            $vevent = $vobject->VEVENT;
            $veventstart = $vevent->DTSTART->getDateTime();
            $veventend = $vevent->DTEND->getDateTime();

            $event->getVisitor()->addData(
                'start',
                $veventstart->format(\DateTime::ISO8601)
            );

            $event->getVisitor()->addData(
                'end',
                $veventend->format(\DateTime::ISO8601)
            );

            #$event->getVisitor()->addData(
            #    'allday',
            #    (!$vevent->DTSTART->hasTime() && !$vevent->DTEND->hasTime())
            #);
        }
    }

    protected function transformEvent($vevent, $calendartimezone) {

        $veventstart = $vevent->DTSTART->getDateTime();
        $veventend = $vevent->DTEND->getDateTime();

        $veventstart->setTimezone($calendartimezone);
        $veventend->setTimezone($calendartimezone);

        return array(
            'start' => $veventstart->format(\DateTime::ISO8601),
            'end' => $veventend->format(\DateTime::ISO8601),
            'allday' => (!$vevent->DTSTART->hasTime() && !$vevent->DTEND->hasTime()),
        );
    }

    protected function getRecurrenceRule($vevent) {

        $serializedRule = array();

        # Serializing recurrence rules
        # TODO: support multiple recurrences for an event
        if(isset($vevent->RRULE)) {

            ###############################################################
            # Examples:
            ###############################################################

            # Every 1 day, never ends
            # {"freq":"DAILY","interval":"1"}

            # Every 16 days, 5 times
            # {"freq":"DAILY","interval":"1","count":"5"}

            # Every 16 days, until 2015-02-20
            # {"freq":"DAILY","interval":"16","until":"20150220T225959Z"}

            # Every 16 days, until 2015-02-20
            # {"freq":"DAILY","interval":"16","until":"20150220T225959Z"}

            # Every week on monday, until 2015-02-20, week starts on monday
            # {"freq":"WEEKLY","interval":"1","until":"20150220T225959Z","byday":"MO","wkst":"MO"}

            # Every 5 weeks on monday, wednesday and saturday, week starts on monday, 20 times
            # {"freq":"WEEKLY","interval":"5","count":"20","byday":["MO","WE","SA"],"wkst":"MO"}

            # Every month, until 2015-02-20
            # {"freq":"MONTHLY","interval":"1","until":"20150220T225959Z"}

            # Every month, on the 1st, 4th and 29th day, 20 times
            # {"freq":"MONTHLY","interval":"1","count":"20","bymonthday":["1","4","29"]}

            # Every month, on the first sunday, 20 times
            # {"freq":"MONTHLY","interval":"1","count":"20","byday":"1SU"}

            # Every month, on the second wednesday, 20 times
            # {"freq":"MONTHLY","interval":"1","count":"20","byday":"2WE"}

            # Every month, on the last friday of the month, 20 times
            # {"freq":"MONTHLY","interval":"1","count":"20","byday":"-1FR"}

            # Every year, until 2022-02-20
            # {"freq":"YEARLY","interval":"1","until":"20220220T225959Z"}

            # Every year in february and november, 20 times
            # {"freq":"YEARLY","interval":"1","count":"20","bymonth":["2","11"]}

            ###############################################################
            # Rules:
            ###############################################################

            # * Common rules (applies to all frequencies)
            #     * freq: one of ['DAILY', 'WEEKLY', 'MONTHLY']
            #     * interval: [0-9]+
            #     * count: [0-9]+; excludes 'until'
            #     * until: ISO-8601 date; excludes 'count'

            # * freq=WEEKLY
            #     * wkst: one of ['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU']
            #     * byday: array of ['MO', 'TU', 'WE', 'TH', 'FR', 'SA']

            # * freq=MONTHLY
            #     * wkst: one of ['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU']
            #     * bymonthday: array of [0-9]+ (day numbers in the month, indexed by 1); excludes byday
            #     * byday: number (1-4) + weekday (one of ['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU']); if number negative, couting starts at the end of the month; excludes bymonthday

            # * freq=YEARLY
            #     * bymonth: array of month numbers, indexed by 1

            # var_dump(json_encode());

            $weekdays = array('MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU');
            $rule = $vevent->RRULE->getJsonValue()[0];

            if(isset($rule['freq'])) {

                $serializedRule = array();

                $serializedRule['freq'] = strtoupper($rule['freq']);

                if(isset($rule['interval']) && preg_match('/[0-9]+/', $rule['interval']) && $rule['interval'] > 0) {
                    $serializedRule['interval'] = intval($rule['interval']);
                }

                if(isset($rule['count']) && preg_match('/[0-9]+/', $rule['count']) && $rule['count'] > 0) {
                    $serializedRule['count'] = intval($rule['count']);
                } elseif(isset($rule['until'])) {
                    $serializedRule['until'] = $rule['until'];
                }

                switch($serializedRule['freq']) {
                    case 'WEEKLY': {

                        # * freq=WEEKLY
                        #     * wkst: one of ['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU']
                        #     * byday: array of ['MO', 'TU', 'WE', 'TH', 'FR', 'SA']

                        #if(isset($rule['wkst']) && in_array(strtoupper($rule['wkst']), $weekdays)) {
                        #    $serializedRule['wkst'] = strtoupper($rule['wkst']);
                        #}

                        if(isset($rule['byday'])) {
                            
                            if(is_string($rule['byday'])) {
                                $byday = array($rule['byday']);
                            } else {
                                $byday = $rule['byday'];
                            }

                            if(is_array($byday)) {

                                $bydayFiltered = array();

                                reset($byday);
                                foreach($byday as $day) {
                                    if(in_array(strtoupper($day), $weekdays)) {
                                        $bydayFiltered[] = strtoupper($day);
                                    }
                                }

                                if(count($bydayFiltered) > 0) {
                                    $serializedRule['weekly_byday'] = $bydayFiltered;
                                }
                            }
                        }

                        break;
                    }

                    case 'MONTHLY': {

                        # * freq=MONTHLY
                        #     * wkst: one of ['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU']
                        #     * bymonthday: array of [0-9]+ (day numbers in the month, indexed by 1); excludes byday
                        #     * byday: number (1-4) + weekday (one of ['MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU']); if number negative, couting starts at the end of the month; excludes bymonthday

                        #if(isset($rule['wkst']) && in_array(strtoupper($rule['wkst']), $weekdays)) {
                        #    $serializedRule['wkst'] = $rule['wkst'];
                        #}

                        if(isset($rule['bymonthday'])) {

                            $bymonthdayFiltered = array();

                            if(is_string($rule['bymonthday']) || is_int($rule['bymonthday'])) {
                                $bymonthday = array($rule['bymonthday']);
                            } else {
                                $bymonthday = $rule['bymonthday'];
                            }

                            if(is_array($bymonthday)) {
                                reset($bymonthday);
                                foreach($bymonthday as $day) {
                                    $day = intval($day);
                                    
                                    if($day >= 1 && $day <= 31) {
                                        $bymonthdayFiltered[] = $day;
                                    }
                                }
                            }

                            if(count($bymonthdayFiltered) > 0) {
                                $serializedRule['monthly_bymonthday'] = $bymonthdayFiltered;
                                sort($serializedRule['monthly_bymonthday'], SORT_NUMERIC);
                            }
                        } elseif(isset($rule['byday']) && is_string($rule['byday'])) {

                            $parts = array();

                            if(preg_match('/(?P<number>\-?[1-4]+)(?P<weekday>' . implode('|', $weekdays) . ')/', $rule['byday'], $parts)) {
                                $serializedRule['monthly_byweekday_day'] = $parts['weekday'];
                                $serializedRule['monthly_byweekday_nth'] = abs(intval($parts['number']));
                                $serializedRule['monthly_byweekday_fromend'] = (intval($parts['number']) < 0);
                            }
                        }

                        break;
                    }

                    case 'YEARLY': {

                        # * freq=YEARLY
                        #     * bymonth: array of month numbers, indexed by 1

                        if(isset($rule['bymonth']) && is_array($rule['bymonth'])) {

                            $bymonthFiltered = array();

                            reset($rule['bymonth']);
                            foreach($rule['bymonth'] as $month) {

                                $month = intval($month);
                                if($month >= 1 && $month <= 12) {
                                    $bymonthFiltered[] = $month;
                                }
                            }

                            if(count($bymonthFiltered) > 0) {
                                $serializedRule['yearly_bymonth'] = $bymonthFiltered;
                                sort($serializedRule['yearly_bymonth'], SORT_NUMERIC);
                            }
                        }

                        break;
                    }
                }
            }
        }

        return $serializedRule;
    }
}