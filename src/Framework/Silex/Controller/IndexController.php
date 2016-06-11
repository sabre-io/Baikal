<?php

namespace Baikal\Framework\Silex\Controller;

use Baikal\Framework\Silex\Controller;
use Psr\Http\Message\RequestInterface;

final class IndexController extends Controller
{
    /**
     * @param RequestInterface $request
     * @return string
     */
    function indexAction(RequestInterface $request)
    {
        return 'Hello world! <pre>' . var_export($request, true) . '</pre>';
    }
}
