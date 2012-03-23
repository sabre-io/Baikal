<?php

namespace BaikalAdmin\View\User;

class Listing extends \Flake\Core\View {
	public function render() {
		$oTemplate = new \Flake\Core\Template(BAIKALADMIN_PATH_TEMPLATES . "User/Listing.html", TRUE);
		
		return $oTemplate->parse(array(
			"users" => $this->get("users"),
		));
	}
}