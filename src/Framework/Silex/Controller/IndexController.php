<?php

namespace Baikal\Framework\Silex\Controller;

use Baikal\Framework\Silex\Controller;

final class IndexController extends Controller
{
    /**
     * @return string
     */
    function indexAction()
    {
        return $this->render('index', []);
    }
}
