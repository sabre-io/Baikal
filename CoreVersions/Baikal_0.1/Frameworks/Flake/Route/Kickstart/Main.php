<?php

namespace Flake\Route\Kickstart;

class Main {
	
	public function execute(\Flake\Core\Render\Container &$oRenderContainer) {
		$oRenderContainer->zone("Payload")->addBlock(new \Flake\Controler\Kickstart());
	}
}