<?php

namespace Baikal\Controller;

final class IndexController extends Controller
{
    /**
     * @return string
     */
    function indexAction()
    {
        return $this->render('index');
    }
}
