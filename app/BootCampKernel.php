<?php

use Symfony\BootCampBundle\Kernel\BootCampKernel as BaseBootCampKernel;

class BootCampKernel extends BaseBootCampKernel {
    
    public function registerBundles() {

        $bundles = parent::registerBundles();
        $bundles[] = new Baikal\BootCampBundle\BaikalBootCampBundle();
        $bundles[] = new Baikal\ModelBundle\BaikalModelBundle();
        $bundles[] = new Baikal\SystemBundle\BaikalSystemBundle();

        return $bundles;
    }
}
