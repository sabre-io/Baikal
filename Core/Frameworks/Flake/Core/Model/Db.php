<?php

#################################################################
#  Copyright notice
#
#  (c) 2013 Jérôme Schneider <mail@jeromeschneider.fr>
#  All rights reserved
#
#  http://flake.codr.fr
#
#  This script is part of the Flake project. The Flake
#  project is free software; you can redistribute it
#  and/or modify it under the terms of the GNU General Public
#  License as published by the Free Software Foundation; either
#  version 2 of the License, or (at your option) any later version.
#
#  The GNU General Public License can be found at
#  http://www.gnu.org/copyleft/gpl.html.
#
#  This script is distributed in the hope that it will be useful,
#  but WITHOUT ANY WARRANTY; without even the implied warranty of
#  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#  GNU General Public License for more details.
#
#  This copyright notice MUST APPEAR in all copies of the script!
#################################################################

namespace Flake\Core\Model;

abstract class Db extends \Flake\Core\Model {
    protected $bFloating = true;

    function __construct($sPrimary = false) {
        if ($sPrimary === false) {
            # Object will be floating
            $this->initFloating();
            $this->bFloating = true;
        } else {
            $this->initByPrimary($sPrimary);
            $this->bFloating = false;
        }
    }

    static function &getBaseRequester() {
        $oRequester = new \Flake\Core\Requester\Sql(get_called_class());
        $oRequester->setDataTable(self::getDataTable());

        return $oRequester;
    }

    static function &getByRequest(\Flake\Core\Requester\Sql $oRequester) {
        // renvoie une collection de la classe du modèle courant (this)
        return $oRequester->execute();
    }

    static function getDataTable() {
        $sClass = get_called_class();

        return $sClass::DATATABLE;
    }

    static function getPrimaryKey() {
        $sClass = get_called_class();

        return $sClass::PRIMARYKEY;
    }

    function getPrimary() {
        return $this->get(self::getPrimaryKey());
    }

    protected function initByPrimary($sPrimary) {
        $rSql = $GLOBALS["DB"]->exec_SELECTquery(
            "*",
            self::getDataTable(),
            self::getPrimaryKey() . "='" . $GLOBALS["DB"]->quote($sPrimary) . "'"
        );

        if (($aRs = $rSql->fetch()) === false) {
            throw new \Exception("\Flake\Core\Model '" . htmlspecialchars($sPrimary) . "' not found for model " . get_class($this));
        }

        reset($aRs);
        $this->aData = $aRs;
    }

    function persist() {
        if ($this->floating()) {
            $GLOBALS["DB"]->exec_INSERTquery(
                self::getDataTable(),
                $this->getData()
            );

            $sPrimary = $GLOBALS["DB"]->lastInsertId();
            $this->initByPrimary($sPrimary);
            $this->bFloating = false;
        } else {
            $GLOBALS["DB"]->exec_UPDATEquery(
                self::getDataTable(),
                self::getPrimaryKey() . "='" . $GLOBALS["DB"]->quote($this->getPrimary()) . "'",
                $this->getData()
            );
        }
    }

    function destroy() {
        $GLOBALS["DB"]->exec_DELETEquery(
            self::getDataTable(),
            self::getPrimaryKey() . "='" . $GLOBALS["DB"]->quote($this->getPrimary()) . "'"
        );
    }

    protected function initFloating() {
        # nothing; object will be blank
    }

    function floating() {
        return $this->bFloating;
    }
}
