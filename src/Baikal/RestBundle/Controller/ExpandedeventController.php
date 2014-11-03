<?php

namespace Baikal\RestBundle\Controller;

use Symfony\Component\Security\Core\SecurityContextInterface,
    Symfony\Component\HttpKernel\Exception\HttpException;

use FOS\RestBundle\View\View,
    FOS\RestBundle\View\ViewHandlerInterface,
    FOS\RestBundle\Controller\Annotations\QueryParam,
    FOS\RestBundle\Request\ParamFetcher;

use JMS\Serializer\SerializationContext;

use Sabre\VObject;

use Baikal\ModelBundle\Entity\Repository\EventRepository,
    Baikal\ModelBundle\Entity\Calendar,
    Baikal\ModelBundle\Entity\Event,
    Baikal\CoreBundle\Services\MainConfigService,
    Baikal\RestBundle\Validator\Constraints\DateISO8601 as DateISO8601Constraint,
    Baikal\DavServicesBundle\Service\Helper\DavTimeZoneHelper;

class ExpandedeventController extends AbstractEventController {

    protected $viewhandler;
    protected $securityContext;
    protected $eventRepo;
    protected $mainconfig;
    protected $timezonehelper;

    public function __construct(
        ViewHandlerInterface $viewhandler,
        SecurityContextInterface $securityContext,
        EventRepository $eventRepo,
        MainConfigService $mainconfig,
        DavTimeZoneHelper $timezonehelper
    ) {
        $this->viewhandler = $viewhandler;
        $this->securityContext = $securityContext;
        $this->eventRepo = $eventRepo;
        $this->mainconfig = $mainconfig;
        $this->timezonehelper = $timezonehelper;
    }

    /**
     * @QueryParam(name="start", requirements=@DateISO8601Constraint, strict=true, default=null, nullable=true)
     * @QueryParam(name="end", requirements=@DateISO8601Constraint, strict=true, default=null, nullable=true)
     */
    public function getExpandedeventsAction(Calendar $calendar, $start, $end) {

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
                'expandedevent' => $events,
                'meta' => [
                    'total' => count($events),
                    'start' => $requestedDates['start'],
                    'end' => $requestedDates['end'],
                ]
            ], 200)
                ->setSerializationContext(
                    SerializationContext::create()
                        ->setAttribute('expanded', TRUE)
                        ->setAttribute('start', $requestedDates['start'])
                        ->setAttribute('end', $requestedDates['end'])
                )
        );
    }

    /**
     * @QueryParam(name="start", requirements=@DateISO8601Constraint, strict=true, default=null, nullable=true)
     * @QueryParam(name="end", requirements=@DateISO8601Constraint, strict=true, default=null, nullable=true)
     */
    public function getExpandedeventAction(Calendar $calendar, Event $event, $start, $end) {

        if(!$this->securityContext->isGranted('dav.read', $calendar)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        $requestedDates = $this->getRequestedDates($calendar, $start, $end);

        return $this->viewhandler->handle(
            View::create([
                'expandedevent' => $event,
                'meta' => [
                    'start' => $requestedDates['start'],
                    'end' => $requestedDates['end'],
                ]
            ], 200)
                ->setSerializationContext(
                    SerializationContext::create()
                        ->setAttribute('expanded', TRUE)
                        ->setAttribute('start', $requestedDates['start'])
                        ->setAttribute('end', $requestedDates['end'])
                )
        );
    }

    # Implementing abstract method on AbstractEventController
    protected function getTimezoneHelper() {
        return $this->timezonehelper;
    }
}
