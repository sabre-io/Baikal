<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20140731153316 extends AbstractMigration {
    
    public function up(Schema $schema) {

        #######################################################################
        # Baikal\DavServicesBundle\Entity\Addressbook
        #######################################################################

        $addressbooks = $schema->createTable('addressbooks');
        
        $addressbooks->addColumn('id', 'integer')->setAutoincrement(true);

        $addressbooks->addColumn('principaluri', 'string', array(
            'length' => 255,
            'notnull' => false,
        ));

        $addressbooks->addColumn('displayname', 'string', array(
            'length' => 255,
            'notnull' => false,
        ));

        $addressbooks->addColumn('uri', 'string', array(
            'length' => 255,
            'notnull' => false,
        ));

        $addressbooks->addColumn('synctoken', 'integer', array(
            'notnull' => false,
        ));

        $addressbooks->addColumn('description', 'text', array(
            'notnull' => false,
        ));

        $addressbooks->setPrimaryKey(array('id'));

        $addressbooks->addUniqueIndex(
            array(
                'principaluri',
                'uri'
            )
        );

        #######################################################################
        # Baikal\DavServicesBundle\Entity\AddressbookContact
        #######################################################################

        $cards = $schema->createTable('cards');
        
        $cards->addColumn('id', 'integer')->setAutoincrement(true);

        $cards->addColumn('carddata', 'text', array(
            'notnull' => false,
        ));

        $cards->addColumn('uri', 'string', array(
            'length' => 255,
            'notnull' => false,
        ));

        $cards->addColumn('lastmodified', 'integer', array(
            'notnull' => false,
        ));

        $cards->addColumn('etag', 'string', array(
            'length' => 32,
            'notnull' => false,
        ));

        $cards->addColumn('size', 'integer', array(
            'notnull' => false,
        ));

        $cards->addColumn('addressbookid', 'integer');

        $cards->setPrimaryKey(array('id'));

        #######################################################################
        # Baikal\DavServicesBundle\Entity\AddressbookChange
        #######################################################################

        $addressbookchanges = $schema->createTable('addressbookchanges');
        
        $addressbookchanges->addColumn('id', 'integer')->setAutoincrement(true);

        $addressbookchanges->addColumn('uri', 'string', array(
            'length' => 255,
            'notnull' => false,
        ));

        $addressbookchanges->addColumn('synctoken', 'integer', array(
            'notnull' => false,
        ));

        $addressbookchanges->addColumn('operation', 'boolean', array(
            'notnull' => false,
        ));

        $addressbookchanges->addColumn('addressbookid', 'integer');

        $addressbookchanges->setPrimaryKey(array('id'));

        $addressbookchanges->addIndex(
            array(
                'addressbookid',
                'synctoken'
            ),
            'addressbookid_synctoken'
        );

        #######################################################################
        # Relations
        #######################################################################

        # addressbooks <= cards

        #$cards->addForeignKeyConstraint(
        #    $addressbooks,              # Foreign table
        #    array('addressbookid'),     # Local key
        #    array('id')                 # Foreign key
        #);


        # addressbooks <= addressbookchanges

        #$addressbookchanges->addForeignKeyConstraint(
        #    $addressbooks,              # Foreign table
        #    array('addressbookid'),     # Local key
        #    array('id')                 # Foreign key
        #);
    }

    public function down(Schema $schema) {
        $schema->dropTable('addressbooks');
        $schema->dropTable('cards');
        $schema->dropTable('addressbookchanges');
    }
}
