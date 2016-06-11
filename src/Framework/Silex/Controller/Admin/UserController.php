<?php

namespace Baikal\Framework\Silex\Controller\Admin;

use Baikal\Framework\Silex\Controller;

final class UserController extends Controller
{
    function indexAction()
    {
        return $this->render('Admin/users', [
            'users' => [],
            'messages' => '',
            'form' => '',
        ]);
    }
}
