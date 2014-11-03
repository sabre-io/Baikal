<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20140731150213 extends AbstractMigration
{
    public function up(Schema $schema) {

        #######################################################################
        # Baikal\DavServicesBundle\Entity\Lock
        #######################################################################

        $locks = $schema->createTable('locks');
        
        $locks->addColumn('id', 'integer')->setAutoincrement(true);

        $locks->addColumn('owner', 'string', array(
            'length' => 100,
            'notnull' => false,
        ));

        $locks->addColumn('timeout', 'integer', array(
            'notnull' => false,
        ));

        $locks->addColumn('created', 'integer', array(
            'notnull' => false,
        ));

        $locks->addColumn('token', 'string', array(
            'length' => 100,
            'notnull' => false,
        ));

        $locks->addColumn('scope', 'integer', array(
            'notnull' => false,
        ));

        $locks->addColumn('depth', 'integer', array(
            'notnull' => false,
        ));

        $locks->addColumn('uri', 'string', array(
            'length' => 1000,
            'notnull' => false,
        ));

        $locks->setPrimaryKey(array('id'));

        $locks->addIndex(array('token'));

        #######################################################################
        # Baikal\DavServicesBundle\Entity\PropertyStorage
        #######################################################################

        $propertystorage = $schema->createTable('propertystorage');
        
        $propertystorage->addColumn('id', 'integer')->setAutoincrement(true);

        $propertystorage->addColumn('path', 'string', array(
            'length' => 1024,
        ));

        $propertystorage->addColumn('name', 'string', array(
            'length' => 100,
        ));

        $propertystorage->addColumn('value', 'blob');

        $propertystorage->setPrimaryKey(array('id'));
    }

    public function down(Schema $schema) {
        $schema->dropTable('locks');
        $schema->dropTable('groupmembers');
    }
}
