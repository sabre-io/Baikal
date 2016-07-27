<?php

namespace Baikal\Controller\Admin;

use Baikal\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig_Environment;

final class SystemSettingsController extends Controller
{

    function __construct(Twig_Environment $twig, UrlGeneratorInterface $urlGenerator)
    {
        parent::__construct($twig, $urlGenerator);
    }

    function indexAction()
    {
        return $this->render('admin/settings/system');
    }
}
