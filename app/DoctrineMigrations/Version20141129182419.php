<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20141129182419 extends AbstractMigration
{
    public function up(Schema $schema) {
        $AuthCode = $schema->createTable('OAuthAuthCode');
        $AuthCode->addColumn('id', 'integer')->setAutoincrement(true);
        $AuthCode->addColumn('client_id', 'integer');
        $AuthCode->addColumn('user_id', 'integer', array('notnull' => false));
        $AuthCode->addColumn('token', 'string', array('length' => 255));
        $AuthCode->addColumn('redirect_uri', 'string', array('length' => 4096));
        $AuthCode->addColumn('expires_at', 'integer', array('notnull' => false));
        $AuthCode->addColumn('scope', 'string', array('length' => 255, 'notnull' => false));
        $AuthCode->setPrimaryKey(array('id'));
        $AuthCode->addUniqueIndex(array('token'));
        $AuthCode->addIndex(array('client_id'));
        $AuthCode->addIndex(array('user_id'));

        $AccessToken = $schema->createTable('OAuthAccessToken');
        $AccessToken->addColumn('id', 'integer')->setAutoincrement(true);
        $AccessToken->addColumn('client_id', 'integer');
        $AccessToken->addColumn('user_id', 'integer', array('notnull' => false));
        $AccessToken->addColumn('token', 'string', array('length' => 255));
        $AccessToken->addColumn('expires_at', 'integer', array('notnull' => false));
        $AccessToken->addColumn('scope', 'string', array('length' => 255, 'notnull' => false));
        $AccessToken->setPrimaryKey(array('id'));
        $AccessToken->addUniqueIndex(array('token'));
        $AccessToken->addIndex(array('client_id'));
        $AccessToken->addIndex(array('user_id'));

        $RefreshToken = $schema->createTable('OAuthRefreshToken');
        $RefreshToken->addColumn('id', 'integer')->setAutoincrement(true);
        $RefreshToken->addColumn('client_id', 'integer');
        $RefreshToken->addColumn('user_id', 'integer', array('notnull' => false));
        $RefreshToken->addColumn('token', 'string', array('length' => 255));
        $RefreshToken->addColumn('expires_at', 'integer', array('notnull' => false));
        $RefreshToken->addColumn('scope', 'string', array('length' => 255, 'notnull' => false));
        $RefreshToken->setPrimaryKey(array('id'));
        $RefreshToken->addUniqueIndex(array('token'));
        $RefreshToken->addIndex(array('client_id'));
        $RefreshToken->addIndex(array('user_id'));

        $Client = $schema->createTable('OAuthClient');
        $Client->addColumn('id', 'integer')->setAutoincrement(true);
        $Client->addColumn('random_id', 'string', array('length' => 255));
        $Client->addColumn('redirect_uris', 'text');
        $Client->addColumn('secret', 'string', array('length' => 255));
        $Client->addColumn('allowed_grant_types', 'text');

        $Client->addColumn('name', 'string', array('length' => 255));
        $Client->addColumn('description', 'text', array('notnull' => false));
        $Client->addColumn('homepageurl', 'string', array('length' => 255, 'notnull' => false));

        $Client->setPrimaryKey(array('id'));
    }

    public function down(Schema $schema) {
        $schema->dropTable('OAuthAuthCode');
        $schema->dropTable('OAuthAccessToken');
        $schema->dropTable('OAuthRefreshToken');
        $schema->dropTable('OAuthClient');
    }
}
