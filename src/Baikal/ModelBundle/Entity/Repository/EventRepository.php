<?php

namespace Baikal\ModelBundle\Entity\Repository;

use Doctrine\ORM\EntityManager;

use Baikal\ModelBundle\Entity\Calendar;

class EventRepository {

    protected $em;
    
    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    public function countAll() {
        $query = $this->em->createQuery('SELECT count(o) FROM BaikalModelBundle:Calendar o');
        return $query->getSingleScalarResult();
    }

    public function findAll() {
        $query = $this->em->createQuery('SELECT o FROM BaikalModelBundle:Event o');
        return $query->getResult();
    }

    public function countAllByCalendar(Calendar $calendar) {

        $query = $this->em->createQuery('SELECT count(o) FROM BaikalModelBundle:Event o WHERE o.calendar = :calendar')
            ->setParameter('calendar', $calendar);

        return $query->getSingleScalarResult();
    }

    public function findByCalendarAndUris(Calendar $calendar, $uris) {

        $query = $this->em->createQuery('SELECT o FROM BaikalModelBundle:Event o WHERE o.calendar = :calendar AND o.uri IN(:uris)')
            ->setParameter('calendar', $calendar)
            ->setParameter('uris', $uris);

        return $query->getResult();
    }

    public function findByCalendarAndTimeRange(Calendar $calendar, \DateTime $start, \DateTime $end) {

        $pdo = $this->em->getConnection()->getWrappedConnection();
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        $davcalendar = new \Sabre\CalDAV\Backend\PDO($pdo);

        $uris = $davcalendar->calendarQuery($calendar->getId(), array(
            'name' => 'VCALENDAR',
            'is-not-defined' => false,
            'time-range' => null,
            'prop-filters' => array(),
            'comp-filters' => array(
                array(
                    'name' => 'VEVENT',
                    'is-not-defined' => false,
                    'time-range' => array(
                        'start' => $start,
                        'end' => $end,
                    ),
                    'prop-filters' => array(),
                    'comp-filters' => array(),
                ),
            ),
        ));

        #print_r($uris);

        return $this->findByCalendarAndUris($calendar, $uris);
    }
}
