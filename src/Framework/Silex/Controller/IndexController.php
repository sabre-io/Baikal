<?php

namespace Baikal\Framework\Silex\Controller;

use Baikal\Framework\Silex\TwigTemplate;

class IndexController
{
    use TwigTemplate;

    function indexAction()
    {
        return 'Hello world!';
    }
}
