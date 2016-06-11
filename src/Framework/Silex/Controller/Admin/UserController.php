<?php

namespace Baikal\Framework\Silex\Controller\Admin;

use Baikal\Framework\Silex\Controller;

final class UserController extends Controller
{
    function indexAction()
    {
        return $this->render('Admin/users', [
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

            'users' => [],
            'messages' => '',
            'form' => '',
        ]);
    }
}
