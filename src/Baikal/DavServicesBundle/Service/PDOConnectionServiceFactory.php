<?php

namespace Baikal\DavServicesBundle\Service;

class PDOConnectionServiceFactory {

    public function __construct($dbal) {
        $this->dbal = $dbal;
    }

    public function get() {
        $pdo = $this->dbal->getWrappedConnection();
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }
}