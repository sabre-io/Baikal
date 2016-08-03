<?php

namespace Baikal\Controller\Admin;

use Baikal\Repository\UserRepository;
use Baikal\Repository\CalendarRepository;
use Baikal\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Environment;

final class DashboardController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var CalendarRepository
     */
    private $calendarRepository;

    function __construct(Twig_Environment $twig, UrlGeneratorInterface $urlGenerator, UserRepository $userRepository, CalendarRepository $calendarRepository)
    {
        parent::__construct($twig, $urlGenerator);
        $this->userRepository = $userRepository;
        $this->calendarRepository = $calendarRepository;
    }

    function indexAction()
    {
        return $this->render('Admin/dashboard', [
            'users'               => $this->userRepository->count(),
            'nbcalendars'         => $this->calendarRepository->countAllCalendars(),
            'nbevents'            => $this->calendarRepository->countAllEvents(),
            'nbbooks'             => 42,
            'nbcontacts'          => 42,
        ]);
    }
}
