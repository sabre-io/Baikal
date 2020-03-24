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

namespace Flake\Core;

abstract class Requester extends \Flake\Core\FLObject {
    protected $sModelClass = '';
    protected $sOrderField = '';
    protected $sOrderDirection = 'ASC';
    protected $iLimitStart = false;
    protected $iLimitNumber = false;

    function __construct($sModelClass) {
        $this->sModelClass = $sModelClass;
    }

    protected function addClause($sField, $sValue) {
        $this->addClauseEquals($sField, $sValue);

        return $this;
    }

    function limit($iStart, $iNumber = false) {
        if ($iNumber !== false) {
            return $this->setLimitStart($iStart)->setLimitNumber($iNumber);
        }

        return $this->setLimitStart($iStart);
    }

    function orderBy($sOrderField, $sOrderDirection = "ASC") {
        $this->sOrderField = $sOrderField;
        $this->sOrderDirection = $sOrderDirection;

        return $this;
    }

    function setLimitStart($iLimitStart) {
        $this->iLimitStart = $iLimitStart;

        return $this;
    }

    function setLimitNumber($iLimitNumber) {
        $this->iLimitNumber = $iLimitNumber;

        return $this;
    }

    abstract function addClauseEquals($sField, $sValue);

    abstract function execute();

    abstract function count();
}
