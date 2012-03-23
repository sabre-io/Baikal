<?php

namespace Flake\Core;

class Auth extends \Flake\Core\FLObject {
	public static function isAdmin() {
		$sUrl = \Flake\Util\Tools::getCurrentUrl();
		$aParts = explode("/", $sUrl);
		return in_array("edit", $aParts);
	}
}