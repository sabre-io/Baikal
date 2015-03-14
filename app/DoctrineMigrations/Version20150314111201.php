<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

use Symfony\Component\DependencyInjection\ContainerAwareInterface,
    Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Removing UserMetadata table
 */
class Version20150314111201 extends AbstractMigration implements ContainerAwareInterface
{
    private $container;

    public function setContainer(ContainerInterface $container = null) {
        $this->container = $container;
    }

    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs

        $users = $schema->getTable('users');
        $users->addColumn('roles', 'text', array(
            'notnull' => false,
        ));
    }

    public function postUp(Schema $schema)
    {
        //$em = $this->container->get('doctrine.orm.entity_manager');

        $query = "SELECT * FROM UserMetadata";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();

        $queryUpdate = "UPDATE users SET roles=:roles WHERE id=:userid";
        $stmtUpdate = $this->connection->prepare($queryUpdate);

        // We can't use Doctrine's ORM to fetch the item, because it has a load of extra fields
        // that aren't in the entity definition.
        while($row = $stmt->fetch()) {
            //$user = $em->getRepository('BaikalSystemBundle:User')->find($row['userid']);

            $stmtUpdate->bindValue('roles', $row['roles']);
            $stmtUpdate->bindValue('userid', $row['userid']);
            $stmtUpdate->execute();
        }

        $queryDrop = "DROP TABLE UserMetadata";
        $stmtDrop = $this->connection->prepare($queryDrop);
        $stmtDrop->execute();
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs

        $usermetadata = $schema->createTable('UserMetadata');
        $usermetadata->addColumn('id', 'integer')->setAutoincrement(true);
        $usermetadata->addColumn('roles', 'simple_array', array(
            'notnull' => false,
        ));
        $usermetadata->addColumn('userid', 'integer');
        $usermetadata->setPrimaryKey(array('id'));
    }

    public function postDown(Schema $schema)
    {
        //$em = $this->container->get('doctrine.orm.entity_manager');

        $query = "SELECT * FROM users";
        $stmt = $this->connection->prepare($query);
        $stmt->execute();

        $queryInsert = "INSERT INTO UserMetadata (roles, userid) VALUES(:roles, :userid)";
        $stmtInsert = $this->connection->prepare($queryInsert);

        // We can't use Doctrine's ORM to fetch the item, because it has a load of extra fields
        // that aren't in the entity definition.
        while($row = $stmt->fetch()) {
            //$user = $em->getRepository('BaikalSystemBundle:User')->find($row['userid']);

            $stmtInsert->bindValue('roles', $row['roles']);
            $stmtInsert->bindValue('userid', $row['id']);
            $stmtInsert->execute();
        }
        
        $queryDrop = "ALTER TABLE users DROP roles";
        $stmtDrop = $this->connection->prepare($queryDrop);
        $stmtDrop->execute();
    }
}
