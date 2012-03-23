<?php

namespace BaikalAdmin\Route\User;

class Listing {
	
	public function execute(\Flake\Core\Render\Container &$oRenderContainer) {
		$oRenderContainer->zone("Payload")->addBlock(new \BaikalAdmin\Controler\User\Listing());
	}
}