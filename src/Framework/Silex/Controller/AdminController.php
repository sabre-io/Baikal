<?php

namespace Baikal\Framework\Silex\Controller;

use Baikal\Framework\Silex\Controller;

final class AdminController extends Controller
{
    function logoutAction()
    {
        return $this->redirect('dashboard');
    }
}
