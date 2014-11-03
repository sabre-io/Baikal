<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20140731151542 extends AbstractMigration {
    
    public function up(Schema $schema) {

        #######################################################################
        # Baikal\DavServicesBundle\Entity\Calendar
        #######################################################################

        $calendars = $schema->createTable('calendars');
        
        $calendars->addColumn('id', 'integer')->setAutoincrement(true);

        $calendars->addColumn('principaluri', 'string', array(
            'length' => 255,
            'notnull' => false,
        ));

        $calendars->addColumn('displayname', 'string', array(
            'length' => 255,
            'notnull' => false,
        ));

        $calendars->addColumn('uri', 'string', array(
            'length' => 255,
            'notnull' => false,
        ));

        $calendars->addColumn('synctoken', 'integer', array(
            'notnull' => false,
        ));

        $calendars->addColumn('description', 'text', array(
            'notnull' => false,
        ));

        $calendars->addColumn('calendarorder', 'integer', array(
            'notnull' => false,
        ));

        $calendars->addColumn('calendarcolor', 'string', array(
            'length' => 10,
            'notnull' => false,
        ));

        $calendars->addColumn('timezone', 'text', array(
            'notnull' => false,
        ));

        $calendars->addColumn('components', 'string', array(
            'length' => 20,
            'notnull' => false,
        ));

        $calendars->addColumn('transparent', 'boolean');

        $calendars->setPrimaryKey(array('id'));

        $calendars->addUniqueIndex(
            array(
                'principaluri',
                'uri'
            )
        );

        #######################################################################
        # Baikal\DavServicesBundle\Entity\Event
        #######################################################################

        $calendarobjects = $schema->createTable('calendarobjects');

        $calendarobjects->addColumn('id', 'integer')->setAutoincrement(true);

        $calendarobjects->addColumn('calendardata', 'text', array(
            'notnull' => false,
        ));

        $calendarobjects->addColumn('uri', 'string', array(
            'notnull' => false,
        ));

        $calendarobjects->addColumn('lastmodified', 'integer', array(
            'notnull' => false,
        ));

        $calendarobjects->addColumn('etag', 'string', array(
            'length' => 255,
            'notnull' => false,
        ));

        $calendarobjects->addColumn('size', 'integer', array(
            'notnull' => false,
        ));

        $calendarobjects->addColumn('componenttype', 'string', array(
            'length' => 255,
            'notnull' => false,
        ));

        $calendarobjects->addColumn('firstoccurence', 'integer', array(
            'notnull' => false,
        ));

        $calendarobjects->addColumn('lastoccurence', 'integer', array(
            'notnull' => false,
        ));

        $calendarobjects->addColumn('calendarid', 'integer');

        $calendarobjects->setPrimaryKey(array('id'));

        $calendarobjects->addUniqueIndex(
            array(
                'calendarid',
                'uri'
            )
        );

        #######################################################################
        # Baikal\DavServicesBundle\Entity\CalendarChange
        #######################################################################

        $calendarchanges = $schema->createTable('calendarchanges');
        
        $calendarchanges->addColumn('id', 'integer')->setAutoincrement(true);

        $calendarchanges->addColumn('uri', 'string', array(
            'length' => 255,
            'notnull' => false,
        ));

        $calendarchanges->addColumn('synctoken', 'integer', array(
            'notnull' => false,
        ));

        $calendarchanges->addColumn('operation', 'boolean', array(
            'notnull' => false,
        ));

        $calendarchanges->addColumn('calendarid', 'integer');

        $calendarchanges->setPrimaryKey(array('id'));

        $calendarchanges->addIndex(
            array(
                'calendarid',
                'synctoken'
            ),
            'calendarid_synctoken'
        );

        #######################################################################
        # Baikal\DavServicesBundle\Entity\CalendarSubscription
        #######################################################################

        $calendarsubscriptions = $schema->createTable('calendarsubscriptions');

        $calendarsubscriptions->addColumn('id', 'integer')->setAutoincrement(true);

        $calendarsubscriptions->addColumn('uri', 'string', array(
            'length' => 200,
            'notnull' => false,
        ));

        $calendarsubscriptions->addColumn('principaluri', 'string', array(
            'length' => 200,
            'notnull' => false,
        ));

        $calendarsubscriptions->addColumn('source', 'text', array(
            'notnull' => false,
        ));

        $calendarsubscriptions->addColumn('displayname', 'string', array(
            'length' => 100,
            'notnull' => false,
        ));

        $calendarsubscriptions->addColumn('refreshrate', 'string', array(
            'length' => 10,
            'notnull' => false,
        ));

        $calendarsubscriptions->addColumn('calendarorder', 'integer', array(
            'notnull' => false,
        ));

        $calendarsubscriptions->addColumn('calendarcolor', 'string', array(
            'length' => 10,
            'notnull' => false,
        ));

        $calendarsubscriptions->addColumn('striptodos', 'boolean', array(
            'notnull' => false,
        ));

        $calendarsubscriptions->addColumn('stripalarms', 'boolean', array(
            'notnull' => false,
        ));

        $calendarsubscriptions->addColumn('stripattachments', 'boolean', array(
            'notnull' => false,
        ));

        $calendarsubscriptions->addColumn('lastmodified', 'integer', array(
            'notnull' => false,
        ));

        $calendarsubscriptions->setPrimaryKey(array('id'));

        $calendarsubscriptions->addUniqueIndex(
            array(
                'principaluri',
                'uri'
            )
        );

        #######################################################################
        # Relations
        #######################################################################

        # calendars <= calendarobjects 

        #$calendarobjects->addForeignKeyConstraint(
        #    $calendars,             # Foreign table
        #    array('calendarid'),    # Local key
        #    array('id')             # Foreign key
        #);

        # calendars <= calendarchanges

        #$calendarchanges->addForeignKeyConstraint(
        #    $calendarchanges,           # Foreign table
        #    array('calendarid'),        # Local key
        #    array('id')                 # Foreign key
        #);

    }

    public function down(Schema $schema) {
        $schema->dropTable('calendars');
        $schema->dropTable('calendarobjects');
        $schema->dropTable('calendarchanges');
        $schema->dropTable('calendarsubscriptions');
    }
}
