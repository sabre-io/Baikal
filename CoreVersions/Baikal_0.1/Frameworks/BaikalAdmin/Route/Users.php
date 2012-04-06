<?php

namespace BaikalAdmin\Route;

class Users {
	
	public static function execute(\Flake\Core\Render\Container &$oRenderContainer) {
		$oRenderContainer->zone("Payload")->addBlock(new \BaikalAdmin\Controller\Users());
	}
}