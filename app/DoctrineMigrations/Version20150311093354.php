<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migrating to SabreDAV 2.1.3
 */
class Version20150311093354 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $calendarobjects = $schema->getTable('calendarobjects');
        $calendarobjects->addColumn('uid', 'string', array(
            'notnull' => false,
        ));

        $schedulingobjects = $schema->createTable('schedulingobjects');
        $schedulingobjects->addColumn('id', 'integer')->setAutoincrement(true);

        $schedulingobjects->addColumn('principaluri', 'string', array(
            'length' => 255,
            'notnull' => false,
        ));

        $schedulingobjects->addColumn('calendardata', 'text', array(
            'notnull' => false,
        ));

        $schedulingobjects->addColumn('uri', 'string', array(
            'length' => 200,
            'notnull' => false,
        ));

        $schedulingobjects->addColumn('lastmodified', 'integer', array(
            'notnull' => false,
        ));

        $schedulingobjects->addColumn('etag', 'string', array(
            'length' => 255,
            'notnull' => false,
        ));

        $schedulingobjects->addColumn('size', 'integer', array(
            'notnull' => false,
        ));

        $schedulingobjects->setPrimaryKey(array('id'));

    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

        $calendarobjects = $schema->getTable('calendarobjects');
        $calendarobjects->removeColumn('uid');

        $schema->dropTable('schedulingobjects');
    }
}
