<?php

namespace BaikalAdmin\Route;

class Install {
	
	public static function execute(\Flake\Core\Render\Container &$oRenderContainer) {
		$oRenderContainer->zone("Payload")->addBlock(new \BaikalAdmin\Controler\Install());
	}
}