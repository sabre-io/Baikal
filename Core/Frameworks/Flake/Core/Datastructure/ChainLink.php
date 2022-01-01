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

namespace Flake\Core\Datastructure;

abstract class ChainLink implements \Flake\Core\Datastructure\Chainable {
    protected $__container = null;
    protected $__key = null;

    function chain(Chain $container, $key) {
        $this->__container = $container;
        $this->__key = $key;
    }

    function offsetSet($offset, $value): void {
        if (is_null($this->__container)) {
            return;
        }

        $this->__container->offsetSet($offset, $value);
    }

    function offsetExists($offset): bool {
        if (is_null($this->__container)) {
            return false;
        }

        return $this->__container->offsetExists($offset);
    }

    function offsetUnset($offset): void {
        if (is_null($this->__container)) {
            return;
        }

        $this->__container->offsetUnset($offset);
    }

    function &offsetGet($offset) {
        if (is_null($this->__container)) {
            return null;
        }

        $oRes = $this->__container->offsetGet($offset);

        return $oRes;
    }

    function rewind(): void {
        $this->__container->rewind();
    }

    function current() {
        return $this->__container->current();
    }

    function key() {
        return $this->__container->key();
    }

    function &next(): void {
        $this->__container->next();
    }

    function &prev() {
        $oPrev = $this->__container->prev();

        return $oPrev;
    }

    function valid(): bool {
        return $this->__container->valid();
    }

    function count(): int {
        return $this->__container->count();
    }

    function &first() {
        $oRes = $this->__container->first();

        return $oRes;
    }

    function &last() {
        $oRes = $this->__container->last();

        return $oRes;
    }
}
