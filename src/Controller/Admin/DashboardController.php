<?php

namespace Baikal\Controller\Admin;

use Baikal\Repository\UserRepository;
use Baikal\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Environment;

final class DashboardController extends Controller
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    function __construct(Twig_Environment $twig, UrlGeneratorInterface $urlGenerator, UserRepository $userRepository)
    {
        parent::__construct($twig, $urlGenerator);
        $this->userRepository = $userRepository;
    }

    function indexAction()
    {
        return $this->render('Admin/dashboard', [
            'users'               => $this->userRepository->count(),
            'nbcalendars'         => 42,
            'nbevents'            => 42,
            'nbbooks'             => 42,
            'nbcontacts'          => 42,
        ]);
    }
}
