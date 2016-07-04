<?php

namespace Baikal\Framework\Silex;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Environment;

/**
 * Base class for controllers to provide some often used methods
 */
abstract class Controller
{
    /**
     * @var Twig_Environment
     */
    private $twig;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @param Twig_Environment $twig
     * @param UrlGeneratorInterface $urlGenerator
     */
    function __construct(Twig_Environment $twig, UrlGeneratorInterface $urlGenerator)
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @param string $name
     * @param array $context
     * @return string
     */
    function render($name, $context = [])
    {
        $name .= '.html.twig';
        return new Response($this->twig->render($name, $context));
    }

    /**
     * @param string $namedRoute
     * @param array $parameters
     * @return Response
     */
    function redirect($namedRoute, $parameters = [])
    {
        $url = $this->urlGenerator->generate($namedRoute, $parameters);

        return new RedirectResponse($url);
    }
}
