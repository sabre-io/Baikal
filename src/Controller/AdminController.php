<?php

namespace Baikal\Controller;

final class AdminController extends Controller
{
    function logoutAction()
    {
        return $this->redirect('dashboard');
    }
}
