<?php

namespace Baikal\Framework\Silex\Controller;

use Baikal\Framework\Silex\Controller;

final class DashboardController extends Controller
{
    function indexAction()
    {
        return $this->render('Admin/dashboard.html.twig', [
            // Navbar
            'homelink'               => '/admin',
            'activehome'             => true,
            'activeusers'            => 42,
            'userslink'              => '',
            'activesettingsstandard' => '',
            'settingsstandardlink'   => '',
            'activesettingssystem'   => '',
            'settingssystemlink'     => '',
            'logoutlink'             => '',

            // Layout
            'pagetitle' => 'Baikal Dashboard',
            'baseurl'   => 'http://localhost:8000',

            // Dashboard
            'BAIKAL_VERSION'      => '0.6.0-dev',
            'BAIKAL_CAL_ENABLED'  => true,
            'BAIKAL_CARD_ENABLED' => true,
            'nbusers'             => 42,
            'nbcalendars'         => 42,
            'nbevents'            => 42,
            'nbbooks'             => 42,
            'nbcontacts'          => 42,
        ]);
    }
}
