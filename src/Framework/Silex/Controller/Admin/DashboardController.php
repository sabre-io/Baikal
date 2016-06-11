<?php

namespace Baikal\Framework\Silex\Controller\Admin;

use Baikal\Framework\Silex\Controller;

final class DashboardController extends Controller
{
    function indexAction()
    {
        return $this->render('Admin/dashboard', [
            'nbusers'             => 42,
            'nbcalendars'         => 42,
            'nbevents'            => 42,
            'nbbooks'             => 42,
            'nbcontacts'          => 42,
        ]);
    }
}
