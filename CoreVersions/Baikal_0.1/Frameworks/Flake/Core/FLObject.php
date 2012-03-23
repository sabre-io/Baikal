<?php

namespace Flake\Core;

class FLObject {
	public function __toString() {
		return print_r($this, TRUE);
	}
	
	public static function getClass() {
		return get_called_class();
	}
	
	public function isA($sClassOrProtocolName) {
		return \Flake\Util\Tools::is_a($this, $sClassOrProtocolName);
	}
}