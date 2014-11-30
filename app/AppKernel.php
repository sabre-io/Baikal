<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            
            new Netgusto\ParameterTouchBundle\NetgustoParameterTouchBundle(),

            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new FOS\RestBundle\FOSRestBundle(),

            new FOS\OAuthServerBundle\FOSOAuthServerBundle(),

            new Baikal\ModelBundle\BaikalModelBundle(),
            new Baikal\CoreBundle\BaikalCoreBundle(),
            new Baikal\DavServicesBundle\BaikalDavServicesBundle(),
            new Baikal\RestBundle\BaikalRestBundle(),
            new Baikal\FrontendBundle\BaikalFrontendBundle(),
            new Baikal\AdminBundle\BaikalAdminBundle(),


            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),

            new Netgusto\DevServerBundle\NetgustoDevServerBundle(),
            new Netgusto\AutorouteBundle\NetgustoAutorouteBundle(),

            new Symfony\BootCampBundle\SymfonyBootCampBundle(),
            new Baikal\ViewComponentsBundle\BaikalViewComponentsBundle(),
            new Netgusto\PortalBundle\NetgustoPortalBundle(),
        );

        #new Netgusto\BootCampBundle\NetgustoBootCampBundle($bundles);

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            #$bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
