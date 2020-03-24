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

namespace Flake\Core\Requester;

class Sql extends \Flake\Core\Requester {
    protected $sDataTable = "";
    protected $aClauses = [];
    protected $sModelClass = "";
    protected $sOrderField = "";
    protected $sOrderDirection = "ASC";
    protected $iLimitStart = false;
    protected $iLimitNumber = false;
    protected $bHasBeenExecuted = false;

    function setDataTable($sDataTable) {
        $this->sDataTable = $sDataTable;

        return $this;
    }

    function addClauseEquals($sField, $sValue) {
        $sWrap = "{field}='{value}'";
        $this->addClauseWrapped($sField, $sValue, $sWrap);

        return $this;
    }

    function addClauseNotEquals($sField, $sValue) {
        $sWrap = "{field}!='{value}'";
        $this->addClauseWrapped($sField, $sValue, $sWrap);

        return $this;
    }

    function addClauseLike($sField, $sValue) {
        $sWrap = "{field} LIKE '%{value}%'";
        $this->addClauseWrapped($sField, $sValue, $sWrap);

        return $this;
    }

    function addClauseLikeBeginning($sField, $sValue) {
        $sWrap = "{field} LIKE '{value}%'";
        $this->addClauseWrapped($sField, $sValue, $sWrap);

        return $this;
    }

    function addClauseLikeEnd($sField, $sValue) {
        $sWrap = "{field} LIKE '%{value}'";
        $this->addClauseWrapped($sField, $sValue, $sWrap);

        return $this;
    }

    function addClauseNotLike($sField, $sValue) {
        $sWrap = "{field} NOT LIKE '%{value}%'";
        $this->addClauseWrapped($sField, $sValue, $sWrap);

        return $this;
    }

    function addClauseNotLikeBeginning($sField, $sValue) {
        $sWrap = "{field} NOT LIKE '{value}%'";
        $this->addClauseWrapped($sField, $sValue, $sWrap);

        return $this;
    }

    function addClauseNotLikeEnd($sField, $sValue) {
        $sWrap = "{field} NOT LIKE '%{value}'";
        $this->addClauseWrapped($sField, $sValue, $sWrap);

        return $this;
    }

    function addClauseIn($sField, $sValue) {
        $sWrap = "{field} IN ({value})";
        $this->addClauseWrapped($sField, $sValue, $sWrap);

        return $this;
    }

    function addClauseNotIn($sField, $sValue) {
        $sWrap = "{field} NOT IN ({value})";
        $this->addClauseWrapped($sField, $sValue, $sWrap);

        return $this;
    }

    protected function addClauseWrapped($sField, $sValue, $sWrap) {
        $sValue = $this->escapeSqlValue($sValue);
        $sClause = str_replace(
            [
                "{field}",
                "{value}",
            ],
            [
                $sField,
                $sValue
            ],
            $sWrap
        );

        $this->addClauseLiteral($sClause);

        return $this;
    }

    function addClauseLiteral($sClause) {
        $this->aClauses[] = $sClause;

        return $this;
    }

    protected function escapeSqlValue($sValue) {
        return $GLOBALS["DB"]->quote(
            $sValue,
            $this->sDataTable
        );
    }

    protected function &reify($aData) {
        $sTemp = $this->sModelClass;
        $res = new $sTemp($aData[$sTemp::getPrimaryKey()]);

        return $res;    # To address 'Notice: Only variable references should be returned by reference'
    }

    function hasBeenExecuted() {
        return $this->bHasBeenExecuted;
    }

    function getQuery($sFields = "*") {
        $sWhere = "1=1";
        $sOrderBy = "";
        $sLimit = "";

        if (!empty($this->aClauses)) {
            $sWhere = implode(" AND ", $this->aClauses);
        }

        if (trim($this->sOrderField) !== "") {
            $sOrderBy = $this->sOrderField . " " . $this->sOrderDirection;
        }

        if ($this->iLimitStart !== false) {
            if ($this->iLimitNumber !== false) {
                $sLimit = $this->iLimitStart . ", " . $this->iLimitNumber;
            } else {
                $sLimit = $this->iLimitStart;
            }
        } elseif ($this->iLimitNumber !== false) {
            $sLimit = "0, " . $this->iLimitNumber;
        }

        return $GLOBALS["DB"]->SELECTquery(
            $sFields,
            $this->sDataTable,
            $sWhere,
            "",
            $sOrderBy,
            $sLimit
        );
    }

    function getCountQuery() {
        return $this->getQuery("count(*) as nbitems");
    }

    function execute() {
        $oCollection = new \Flake\Core\CollectionTyped($this->sModelClass);
        $sSql = $this->getQuery();

        $rSql = $GLOBALS["DB"]->query($sSql);
        while (($aRs = $rSql->fetch()) !== false) {
            $oCollection->push(
                $this->reify($aRs)
            );
        }

        $this->bHasBeenExecuted = true;

        return $oCollection;
    }

    function count() {
        $sSql = $this->getCountQuery();

        $rSql = $GLOBALS["DB"]->query($sSql);
        if (($aRs = $rSql->fetch()) !== false) {
            return intval($aRs["nbitems"]);
        }

        return 0;
    }
}
