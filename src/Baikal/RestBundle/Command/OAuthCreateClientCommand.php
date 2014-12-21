<?php

namespace Baikal\RestBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\ProcessBuilder;
use Symfony\Component\Process\ProcessUtils;
use Symfony\Component\Process\Process;

class OAuthCreateClientCommand extends ContainerAwareCommand {
    
    protected function configure() {
        $this
            ->setName('oauth:createclient')
            ->setDescription('TEST: create oauth2 client');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $container = $this->getContainer();
        $serverconfig = $container->getParameter('netgusto_dev_server.config');

        $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setRedirectUris(array('http://www.example.com'));
        $client->setAllowedGrantTypes(array('token', 'authorization_code', 'client_credentials'));
        $clientManager->updateClient($client);

        $output->writeLn('Hello, World !');
    }
}