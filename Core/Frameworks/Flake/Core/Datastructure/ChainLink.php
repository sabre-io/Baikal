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
	
	public function chain(Chain $container, $key) {
		$this->__container = $container;
		$this->__key = $key;
	}
	
	public function offsetSet($offset,$value) {
		if(is_null($this->__container)) {
			return;
		}
		
		$this->__container->offsetSet($offset, $value);
	}

	public function offsetExists($offset) {
		if(is_null($this->__container)) {
			return FALSE;
		}
		
		return $this->__container->offsetExists($offset);
	}

	public function offsetUnset($offset) {
		if(is_null($this->__container)) {
			return;
		}
		
		$this->__container->offsetUnset($offset);
	}

	public function &offsetGet($offset) {
		if(is_null($this->__container)) {
			return null;
		}
		
		$oRes = $this->__container->offsetGet($offset);
		return $oRes;
	}

	public function rewind() {
		$this->__container->rewind();
	}

	public function current() {
		return $this->__container->current();
	}

	public function key() {
		return $this->__container->key();
	}

	public function &next() {
		$oRes = $this->__container->next();
		return $oRes;
	}
	
	public function &prev() {
		$oPrev = $this->__container->prev();
		return $oPrev;
	}

	public function valid() {
		return $this->__container->valid();
	}    

	public function count() {
		return $this->__container->count();
	}
	
	public function &first() {
		$oRes = $this->__container->first();
		return $oRes;
	}
	
	public function &last() {
		$oRes = $this->__container->last();
		return $oRes;
	}
}