<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20140731155704 extends AbstractMigration {
    
    public function up(Schema $schema) {
        #######################################################################
        # Symfony\BootCampBundle\Entity\ConfigContainer
        #######################################################################

        $configcontainer = $schema->createTable('ConfigContainer');
        
        $configcontainer->addColumn('id', 'integer')->setAutoincrement(true);

        $configcontainer->addColumn('name', 'string', array(
            'length' => 255,
        ));

        $configcontainer->addColumn('config', 'json_array');

        $configcontainer->setPrimaryKey(array('id'));
    }

    public function down(Schema $schema) {
    }
}
