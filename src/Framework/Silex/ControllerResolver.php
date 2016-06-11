<?php

namespace Baikal\Framework\Silex;

use Silex\ControllerResolver as BaseControllerResolver;

final class ControllerResolver extends BaseControllerResolver
{
    protected function createController($controller)
    {
        if (false !== strpos($controller, '::')) {
            return parent::createController($controller);
        }

        if (false === strpos($controller, ':')) {
            throw new \LogicException(sprintf('Unable to parse the controller name "%s".', $controller));
        }

        list($service, $method) = explode(':', $controller, 2);

        if (!isset($this->app[$service])) {
            throw new \InvalidArgumentException(sprintf('Service "%s" does not exist.', $controller));
        }

        return [$this->app[$service], $method];
    }
}
