<?php

namespace Baikal\RestBundle\Controller;

use Symfony\Component\Security\Core\SecurityContextInterface,
    Symfony\Component\HttpKernel\Exception\HttpException;

use FOS\RestBundle\View\View,
    FOS\RestBundle\View\ViewHandlerInterface;

use Sabre\VObject;

use Baikal\ModelBundle\Entity\Repository\CalendarRepository,
    Baikal\ModelBundle\Entity\Calendar,
    Baikal\CoreBundle\Services\MainConfigService;

class CalendarController {

    protected $securityContext;
    protected $calendarRepo;
    protected $mainconfig;

    public function __construct(
        ViewHandlerInterface $viewhandler,
        SecurityContextInterface $securityContext,
        CalendarRepository $calendarRepo,
        MainConfigService $mainconfig
    ) {
        $this->viewhandler = $viewhandler;
        $this->securityContext = $securityContext;
        $this->calendarRepo = $calendarRepo;
        $this->mainconfig = $mainconfig;
    }
    
    public function getCalendarsAction() {

        if(!$this->securityContext->isGranted('rest.api')) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        $calendars = $this->calendarRepo->findByUser(
            $this->securityContext->getToken()->getUser()
        );

        return $this->viewhandler->handle(
            View::create([
                'calendar' => $calendars,
                'meta' => [
                    'total' => count($calendars),
                ]
            ], 200)
        );
    }

    public function getCalendarAction(Calendar $calendar) {

        if(!$this->securityContext->isGranted('dav.read', $calendar)) {
            throw new HttpException(401, 'Unauthorized access.');
        }

        return $this->viewhandler->handle(
            View::create([
                'calendar' => $calendar,
            ], 200)
        );
    }
}
