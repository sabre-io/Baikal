<?php

namespace Baikal\Controller;

use Baikal\Controller;

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
