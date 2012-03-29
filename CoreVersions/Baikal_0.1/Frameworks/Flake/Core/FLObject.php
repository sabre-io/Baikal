<?php

namespace Flake\Core;

class FLObject {
	public function __toString() {
		return print_r($this, TRUE);
	}
	
	public function getClass() {
//		throw new \Exception("getClass() is deprecated");
		if(!isset($this)) {
#			echo "STATIC<br />";
			return get_called_class();
		} else {
#			echo "INSTANCE<br />";
#			debug($this);
			return get_class($this);
		}
	}
	
	public function isA($sClassOrProtocolName) {
		return \Flake\Util\Tools::is_a($this, $sClassOrProtocolName);
	}
}