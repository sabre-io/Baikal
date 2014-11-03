<?php

namespace Baikal\RestBundle\Controller;

# Rest
use Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpKernel\Exception\HttpException,
    Symfony\Component\Security\Core\SecurityContextInterface,
    Doctrine\ORM\EntityManagerInterface,
    Symfony\Bundle\FrameworkBundle\Routing\Router,
    Symfony\Component\Form\FormFactoryInterface,
    FOS\RestBundle\View\View,
    FOS\RestBundle\View\ViewHandlerInterface,
    FOS\RestBundle\Controller\Annotations\QueryParam;

use Sabre\VObject,
    Sabre\VObject\UUIDUtil,
    Sabre\CalDAV\Backend\BackendInterface as CalDAVBackendInterface;

use Baikal\ModelBundle\Entity\Repository\EventRepository,
    Baikal\ModelBundle\Entity\Calendar,
    Baikal\ModelBundle\Entity\Event,
    Baikal\ModelBundle\Form\Type\Calendar\EventType,
    Baikal\CoreBundle\Services\MainConfigService,
    Baikal\RestBundle\Validator\Constraints\DateISO8601 as DateISO8601Constraint,
    Baikal\DavServicesBundle\Service\Helper\DavTimeZoneHelper;

class EventController extends AbstractEventController {

    protected $em;
    protected $router;
    protected $formFactory;
    protected $viewhandler;
    protected $securityContext;
    protected $eventRepo;
    protected $mainconfig;
    protected $timezonehelper;

    public function __construct(
        EntityManagerInterface $em,
        Router $router,
        FormFactoryInterface $formFactory,
        ViewHandlerInterface $viewhandler,
        SecurityContextInterface $securityContext,
        EventRepository $eventRepo,
        MainConfigService $mainconfig,
        DavTimeZoneHelper $timezonehelper,
        CalDAVBackendInterface $davbackend
    ) {
        $this->em = $em;
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->viewhandler = $viewhandler;
        $this->securityContext = $securityContext;
        $this->eventRepo = $eventRepo;
        $this->mainconfig = $mainconfig;
        $this->timezonehelper = $timezonehelper;
        $this->davbackend = $davbackend;
    }

    /**
     * @QueryParam(name="start", requirements=@DateISO8601Constraint, strict=true, default=null, nullable=true)
     * @QueryParam(name="end", requirements=@DateISO8601Constraint, strict=true, default=null, nullable=true)
     */
    public function getEventsAction(Calendar $calendar, $start = null, $end = null) {

        if(!$this->securityContext->isGranted('dav.read', $calendar)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        $requestedDates = $this->getRequestedDates($calendar, $start, $end);

        $events = $this->eventRepo->findByCalendarAndTimeRange(
            $calendar,
            $requestedDates['start'],
            $requestedDates['end']
        );

        return $this->viewhandler->handle(
            View::create([
                'event' => $events,
                'meta' => [
                    'total' => count($events),
                    'start' => $requestedDates['start'],
                    'end' => $requestedDates['end'],
                ]
            ], 200)
        );
    }

    public function getEventAction(Calendar $calendar, Event $event) {
        if(!$this->securityContext->isGranted('dav.read', $calendar)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        return $this->viewhandler->handle(
            View::create([
                'event' => $event,
            ], 200)
        );
    }

    public function postEventAction(Request $request, Calendar $calendar) {

        throw new HttpException(501, 'Not implemented.');
        
        if(!$this->securityContext->isGranted('dav.write', $calendar)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        $data = json_decode($request->getContent(), TRUE);
        
        $event = new Event();
        $uid = strtoupper(UUIDUtil::getUUID());
        $event->setUri($uid . '.ics');
        $event->setCalendar($calendar);
        $event->getVObject()->VEVENT->UID = $uid;

        $this->updateEventFromEventDTO($event, $data);

        $this->em->persist($event);
        $this->em->flush();

        # Updating the sync-state for the calendar
        $this->davbackend->publicAddChange(
            $calendar->getId(),
            $event->getUri(),
            1   # 1: Creation
        );

        return new RedirectResponse(
            $this->router->generate('get_calendar_event', array(
                'calendar' => $calendar->getId(),
                'event' => $event->getId(),
            )),
            Response::HTTP_CREATED
        );
    }

    public function putEventAction(Request $request, Calendar $calendar, Event $event) {

        throw new HttpException(501, 'Not implemented.');

        if(!$this->securityContext->isGranted('dav.write', $calendar)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        return $this->patchEventAction($request, $calendar, $event);
    }

    public function patchEventAction(Request $request, Calendar $calendar, Event $event) {

        throw new HttpException(501, 'Not implemented.');
        
        if(!$this->securityContext->isGranted('dav.write', $calendar)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        $data = json_decode($request->getContent(), TRUE);
        $this->updateEventFromEventDTO($event, $data);

        $this->em->persist($event);
        $this->em->flush();

        $this->davbackend->publicAddChange(
            $calendar->getId(),
            $event->getUri(),
            2   # 2: Update
        );

        return Response::create()->setStatusCode(Response::HTTP_ACCEPTED);
    }

    protected function updateEventFromEventDTO(Event &$event, $data) {

        $calendartimezone = $this->timezonehelper->extractTimeZoneFromDavString($event->getCalendar()->getTimezone());
        $vobject = $event->getVObject();
        $vevent = $vobject->VEVENT;
        
        if(isset($data['title'])) {
            $vevent->SUMMARY = $data['title'];
        }

        if(isset($data['busy'])) {
            $vevent->TRANSP = $data['busy'] ? 'OPAQUE' : 'TRANSPARENT';
        } else {
            $vevent->TRANSP = 'OPAQUE';
        }

        if(isset($data['start'])) {
            $start = new \Datetime($data['start']);
            $start->setTimezone($calendartimezone);

            if($start->format('H:i:s') === '00:00:00') {
                $vevent->remove('DTSTART');

                $elem = $vobject->createProperty('DTSTART');
                $elem['VALUE'] = 'DATE';
                $elem->setDateTime($start);

                $vevent->add($elem);
            } else {
                $vevent->DTSTART->setDatetime($start);
            }
        }

        if(isset($data['end'])) {
            $end = new \Datetime($data['end']);
            $end->setTimezone($calendartimezone);

            if($end->format('H:i:s') === '00:00:00') {
                $vevent->remove('DTEND');
                
                $elem = $vobject->createProperty('DTEND');
                $elem['VALUE'] = 'DATE';
                $elem->setDateTime($end);

                $vevent->add($elem);
            } else {
                $vevent->DTEND->setDatetime($end);
            }
        }

        $rrule = $this->getRRULEFromEventDTO($data);
        if(!is_null($rrule)) {
            $vevent->RRULE = $rrule;
        }

        $event->setCalendardata($vobject->serialize());
        $denormalized = $this->davbackend->publicGetDenormalizedData($event->getCalendardata());

        $event->setSize($denormalized['size']);
        $event->setEtag($denormalized['etag']);
        $event->setFirstOccurence($denormalized['firstOccurence']);     #
        $event->setLastoccurence($denormalized['lastOccurence']);       # useful in calendarQuery, for time-range based queries
        $event->setComponenttype($denormalized['componentType']);

        unset($denormalized);
    }

    protected function getRRULEFromEventDTO($data) {

        if(isset($data['recurrence_freq'])) {
            $rrule = array('FREQ=' . strtoupper($data['recurrence_freq']));

            if(isset($data['recurrence_interval'])) {
                $rrule[] = 'INTERVAL=' . $data['recurrence_interval'];
            }

            if(isset($data['recurrence_count'])) {
                $rrule[] = 'COUNT=' . $data['recurrence_count'];
            }

            if(isset($data['recurrence_until'])) {
                $rrule[] = 'UNTIL=' . $data['recurrence_until'];
            }

            if(isset($data['recurrence_weekly_byday'])) {
                
                $weekly_byday = null;
                
                if(!is_array($data['recurrence_weekly_byday'])) {
                    $weekly_byday = array($data['recurrence_weekly_byday']);
                } else {
                    $weekly_byday = $data['recurrence_weekly_byday'];
                }

                $rrule[] = 'BYDAY=' . implode(',', $weekly_byday);
            }

            if(
                isset($data['recurrence_monthly_byweekday_day']) &&
                isset($data['monthly_byweekday_nth'])
            ) {
                $dayindex = abs(intval($data['monthly_byweekday_nth']));
                if(isset($data['monthly_byweekday_fromend']) && $data['monthly_byweekday_fromend'] === TRUE) {
                    $dayindex = $dayindex * -1;
                }

                $rrule[] = 'BYDAY=' . $dayindex . strtoupper($data['recurrence_monthly_byweekday_day']);
            }

            if(isset($data['recurrence_monthly_bymonthday'])) {
                $monthly_bymonthday = null;
                
                if(!is_array($data['recurrence_monthly_bymonthday'])) {
                    $monthly_bymonthday = array($data['recurrence_monthly_bymonthday']);
                } else {
                    $monthly_bymonthday = $data['recurrence_monthly_bymonthday'];
                }

                $rrule[] = 'BYMONTHDAY=' . implode(',', $monthly_bymonthday);
            }

            if(isset($data['recurrence_yearly_bymonth'])) {
                $yearly_bymonth = null;
                
                if(!is_array($data['recurrence_yearly_bymonth'])) {
                    $yearly_bymonth = array($data['recurrence_yearly_bymonth']);
                } else {
                    $yearly_bymonth = $data['recurrence_yearly_bymonth'];
                }

                $rrule[] = 'BYMONTH=' . implode(',', $yearly_bymonth);
            }

            return implode(';', $rrule);
        }

        return null;
    }

    # Implementing abstract method on AbstractEventController
    protected function getTimezoneHelper() {
        return $this->timezonehelper;
    }
}
