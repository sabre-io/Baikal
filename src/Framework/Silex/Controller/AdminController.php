<?php

namespace Baikal\Framework\Silex\Controller;

use Baikal\Framework\Silex\TwigTemplate;

class AdminController
{
    use TwigTemplate;

    function logoutAction()
    {
        $this->redirect('admin');
    }
}
