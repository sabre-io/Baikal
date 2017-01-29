<?php

namespace Baikal\Controller;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class InstallController implements ControllerProviderInterface {

    protected $app;
        
    function connect(Application $app) {

        $controllers = $app['controllers_factory'];
        $controllers->get('', [$this, 'installAction'])->bind('install');
        $this->app = $app;

        return $controllers;

    }

    function installAction(Application $app) {

        $minPhpVersion = '5.5.0';

        $compat = [];
        $compat[] = [
            'label' => 'PHP Version >= ' .  $minPhpVersion,
            'status' =>  version_compare(PHP_VERSION, $minPhpVersion) >= 0 ? 'good' : 'bad',
            'extraInfo' => 'Your PHP version must at least be ' . $minPhpVersion . '. Try upgrading PHP before running the installer',
        ];
        $compat[] = [
            'label' => 'LibXML version >= 20700',
            'status' =>  !defined('LIBXML_VERSION') && LIBXML_VERSION >= 20700 ? 'good' : 'bad',
            'extraInfo' => 'The compiled in libxml version must be higher than 20700. Have ' .  (defined('LIBXML_VERSION')? LIBXML_VERSION : '(none)') . '  installed. Upgrade your operating system\'s packages.',
        ];

        $extensions =  [

            // sabre/dav main dependencies
            'dom',
            'pcre',
            'spl',
            'simplexml',
            'mbstring',
            'ctype',
            'date',
            'iconv',
            'xmlreader',
            'xmlwriter',

            // Baikal needs
            'pdo',
        ];

        foreach($extensions as $extension) {
            $compat[]  = [
                'label' =>  'PHP extension:  ' . $extension,
                'status' => extension_loaded($extension) ? 'good' : 'bad',
                'extraInfo' => 'The ' .  $extension . ' PHP extension is required for Baïkal to function.  On Ubuntu/Debian systems you might be able to install this with <code>apt-get install php-' .  $extension . '</code>',
            ];
        }



        $vars = ['compatTable' => $compat];

        return $app['twig']->render('install.html', $vars);
    }

}
