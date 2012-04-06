<?php

namespace BaikalAdmin\Route;

class Dashboard {
	
	public static function execute(\Flake\Core\Render\Container &$oRenderContainer) {
		$oRenderContainer->zone("Payload")->addBlock(new \BaikalAdmin\Controller\Dashboard());
	}
}