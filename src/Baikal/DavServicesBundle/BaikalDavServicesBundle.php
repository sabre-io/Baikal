<?php

namespace Baikal\DavServicesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle,
    Symfony\Component\DependencyInjection\ContainerBuilder;

use Baikal\DavServicesBundle\DependencyInjection\Compiler\DoctrineEntityListenerPass;

class BaikalDavServicesBundle extends Bundle
{
    public function build(ContainerBuilder $container) {
        parent::build($container);
        $container->addCompilerPass(new DoctrineEntityListenerPass());
    }
}
