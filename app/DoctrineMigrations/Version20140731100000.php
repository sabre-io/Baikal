<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20140731100000 extends AbstractMigration {
    
    public function up(Schema $schema) {

        #######################################################################
        # Symfony\BootCampBundle\Entity\BootCampStatus
        #######################################################################

        $bootcampstatus = $schema->createTable('BootCampStatus');
        
        $bootcampstatus->addColumn('id', 'integer')->setAutoincrement(true);
        
        $bootcampstatus->addColumn('configuredversion', 'string', array(
            'length' => 32,
        ));

        $bootcampstatus->setPrimaryKey(array('id'));
    }

    public function down(Schema $schema) {

    }
}