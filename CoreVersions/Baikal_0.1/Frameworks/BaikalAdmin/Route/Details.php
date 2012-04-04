<?php

namespace BaikalAdmin\Route;

class Details {
	
	public static function execute(\Flake\Core\Render\Container &$oRenderContainer) {
		$oRenderContainer->zone("Payload")->addBlock(new \BaikalAdmin\Controler\Details());
	}
}