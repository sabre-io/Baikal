<?php

namespace Baikal\Framework\Silex;

use Silex\ControllerResolver as BaseControllerResolver;

final class ControllerResolver extends BaseControllerResolver
{
    /**
     * @param string $controller
     * @return array|callable
     */
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

        return array($this->instantiateController($this->app[$service]), $method);
    }

    /**
     * Returns an instantiated controller.
     *
     * @param string $class A class name
     *
     * @return object
     */
    protected function instantiateController($class)
    {
        return new $class($this->app['twig'], $this->app['url_generator']);
    }
}
