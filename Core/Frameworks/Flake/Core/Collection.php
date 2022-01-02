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

class Collection extends \Flake\Core\FLObject implements \Iterator {
    protected $aCollection = [];
    protected $aMeta = [];

    #[\ReturnTypeWillChange]
    function current() {
        return current($this->aCollection);
    }

    #[\ReturnTypeWillChange]
    function key() {
        return key($this->aCollection);
    }

    function next(): void {
        next($this->aCollection);
    }

    function rewind(): void {
        $this->reset();
    }

    function valid(): bool {
        $key = key($this->aCollection);

        return ($key !== null && $key !== false);
    }

    function &getForKey($sKey) {
        $aKeys = $this->keys();
        if (!in_array($sKey, $aKeys)) {
            throw new \Exception("\Flake\Core\Collection->getForKey(): key '" . $sKey . "' not found in Collection");
        }

        $oRes = $this->aCollection[$sKey];

        return $oRes;
    }

    function reset() {
        reset($this->aCollection);
    }

    function prev() {
        return prev($this->aCollection);
    }

    function count() {
        return count($this->aCollection);
    }

    function keys() {
        return array_keys($this->aCollection);
    }

    function isEmpty() {
        return $this->count() === 0;
    }

    function isAtFirst() {
        return $this->key() === array_shift($this->keys());
    }

    function isAtLast() {
        return $this->key() === array_pop($this->keys());
    }

    function push(&$mMixed) {
        array_push($this->aCollection, $mMixed);
    }

    function flush() {
        unset($this->aCollection);
        $this->aCollection = [];
    }

    function &first() {
        if (!$this->isEmpty()) {
            $aKeys = $this->keys();

            return $this->aCollection[array_shift($aKeys)];
        }

        $var = null;    # two lines instead of one

        return $var;    # as PHP needs a variable to return by ref
    }

    function &last() {
        if (!$this->isEmpty()) {
            $aKeys = $this->keys();

            return $this->aCollection[array_pop($aKeys)];
        }

        $var = null;

        return $var;
    }

    function toArray() {
        return $this->aCollection;
    }

    static function fromArray($aData) {
        $oColl = new \Flake\Core\Collection();
        reset($aData);
        foreach ($aData as $mData) {
            $oColl->push($mData);
        }

        return $oColl;
    }

    # Create a new collection like this one
    # This abstraction is useful because of CollectionTyped
    protected function newCollectionLikeThisOne() {
        $oCollection = new \Flake\Core\Collection();    # two lines instead of one

        return $oCollection;                            # as PHP needs a variable to return by ref
    }

    function map($sFunc) {
        $aData = $this->toArray();
        $oNewColl = $this->fromArray(array_map($sFunc, $aData));

        return $oNewColl;
    }

    function walk($sFunc, $aParams = []) {
        $aData = $this->toArray();
        $oNewColl = $this->fromArray(array_walk($aData, $sFunc, $aParams));

        return $oNewColl;
    }

    function remove($sKey) {
        $aKeys = $this->keys();
        if (!in_array($sKey, $aKeys)) {
            throw new \Exception("\Flake\Core\Collection->remove(): key '" . $sKey . "' not found in Collection");
        }

        unset($this->aCollection[$sKey]);
        $this->aCollection = array_values($this->aCollection);
    }

    function &__call($sName, $aArguments) {
        if (
            strlen($sName) > 7 &&
            $sName[0] === "s" &&
            $sName[1] === "e" &&
            $sName[2] === "t" &&
            $sName[3] === "M" &&
            $sName[4] === "e" &&
            $sName[5] === "t" &&
            $sName[6] === "a"
        ) {
            $sKey = strtolower(substr($sName, 7, 1)) . substr($sName, 8);
            $mValue = &$aArguments[0];

            if (is_null($mValue)) {
                if (array_key_exists($sKey, $this->aMeta)) {
                    unset($this->aMeta[$sKey]);
                }
            } else {
                $this->aMeta[$sKey] = &$mValue;
            }

            $res = null;

            return $res;    # To avoid 'Notice: Only variable references should be returned by reference'
        } elseif (
            strlen($sName) > 7 &&
            $sName[0] === "g" &&
            $sName[1] === "e" &&
            $sName[2] === "t" &&
            $sName[3] === "M" &&
            $sName[4] === "e" &&
            $sName[5] === "t" &&
            $sName[6] === "a"
        ) {
            $sKey = strtolower(substr($sName, 7, 1)) . substr($sName, 8);
            if (array_key_exists($sKey, $this->aMeta)) {
                return $this->aMeta[$sKey];
            } else {
                return null;
            }
        } else {
            throw new \Exception("Method " . $sName . "() not found on " . get_class($this));
        }
    }
}
