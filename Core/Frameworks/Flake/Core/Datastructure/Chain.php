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

class Chain extends \SplDoublyLinkedList {
    /*
     * Note: SplDoublyLinkedList::push expects mixed $value
     * https://www.php.net/manual/en/spldoublylinkedlist.push.php
     * In this implementation it has been restricted to Chainable.
     * phpstan complains about that. So analaysis of this file has been
     * disabled in the excludes_analyse section of phpstan.neon
     */
    function push(\Flake\Core\Datastructure\Chainable $value) {
        $value->chain($this, $this->count());
        parent::push($value);
    }

    function offsetUnset($offset) {
        throw new \Exception("Cannot delete Chainable in Chain");
    }

    function &first() {
        $oRes = $this->bottom();

        return $oRes;
    }

    function &last() {
        $oRes = $this->top();

        return $oRes;
    }

    function reset() {
        reset($this);
    }

    function __toString() {
        ob_start();
        var_dump($this);
        $sDump = ob_get_contents();
        ob_end_clean();

        return "<pre>" . htmlspecialchars($sDump) . "</pre>";
    }
}
