<?php

namespace Baikal\Framework\Silex;

use Twig_Environment;

trait TwigTemplate
{
    /** @var Twig_Environment */
    private $template;

    /**
     * @param Twig_Environment $template
     */
    public function setTemplate(Twig_Environment $template)
    {
        $this->template = $template;
    }

    /**
     * @return Twig_Environment
     */
    public function getTemplate()
    {
        return $this->template;
    }
}
