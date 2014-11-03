<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

class Version20140731135018 extends AbstractMigration {

    public function up(Schema $schema) {

        #######################################################################
        # Baikal\DavServicesBundle\Entity\User
        #######################################################################

        $users = $schema->createTable('users');
        
        $users->addColumn('id', 'integer')->setAutoincrement(true);

        $users->addColumn('username', 'string', array(
            'length' => 255,
            'unique' => true,
            'notnull' => false,
        ));
        
        $users->addColumn('digesta1', 'string', array(
            'length' => 200,
            'notnull' => false,
        ));

        $users->setPrimaryKey(array('id'));

        $users->addUniqueIndex(array('username'));

        #######################################################################
        # Baikal\DavServicesBundle\Entity\UserMetadata
        #######################################################################

        $usermetadata = $schema->createTable('UserMetadata');

        $usermetadata->addColumn('id', 'integer')->setAutoincrement(true);

        $usermetadata->addColumn('roles', 'simple_array', array(
            'notnull' => false,
        ));

        $usermetadata->addColumn('userid', 'integer');

        $usermetadata->setPrimaryKey(array('id'));

        #######################################################################
        # Baikal\DavServicesBundle\Entity\UserPrincipal
        #######################################################################

        $principals = $schema->createTable('principals');

        $principals->addColumn('id', 'integer')->setAutoincrement(true);

        $principals->addColumn('uri', 'string', array(
            'length' => 200,
            'notnull' => false,
        ));

        $principals->addColumn('email', 'string', array(
            'length' => 80,
            'notnull' => false,
        ));

        $principals->addColumn('displayname', 'string', array(
            'length' => 80,
            'notnull' => false,
        ));

        $principals->addColumn('vcardurl', 'string', array(
            'length' => 255,
            'notnull' => false,
        ));

        $principals->setPrimaryKey(array('id'));

        $principals->addUniqueIndex(array('uri'));

        #######################################################################
        # Baikal\DavServicesBundle\Entity\Groupmember
        #######################################################################

        $groupmembers = $schema->createTable('groupmembers');
        
        $groupmembers->addColumn('id', 'integer')->setAutoincrement(true);

        $groupmembers->addColumn('principal_id', 'integer', array(
            'notnull' => false,
        ));

        $groupmembers->addColumn('member_id', 'integer', array(
            'notnull' => false,
        ));

        $groupmembers->setPrimaryKey(array('id'));

        $groupmembers->addUniqueIndex(array('principal_id', 'member_id'));

        #######################################################################
        # Relations
        #######################################################################

        /*
        # users <=> UserMetadata

        $usermetadata->addForeignKeyConstraint(
            $users,         # Foreign table
            array('userid'),  # Local key
            array('id'),    # Foreign key
            array(
                'onUpdate' => 'CASCADE',
                'onDelete' => 'CASCADE',
            )
        );

        $users->addForeignKeyConstraint(
            $usermetadata,  # Foreign table
            array('id'),    # Local key
            array('userid'),  # Foreign key
            array(
                'onUpdate' => 'CASCADE',
                'onDelete' => 'CASCADE',
            )
        );
        */
    }

    public function down(Schema $schema) {

        $schema->dropTable('users');
        $schema->dropTable('UserMetadata');
        $schema->dropTable('principals');
    }
}
