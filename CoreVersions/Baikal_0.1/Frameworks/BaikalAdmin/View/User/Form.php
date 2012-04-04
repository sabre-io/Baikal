<?php

namespace BaikalAdmin\View\User;

class Form extends \Flake\Core\View {
	public function render() {
		$oTemplate = new \Flake\Core\Template(BAIKALADMIN_PATH_TEMPLATES . "User/Form.html", TRUE);
		$oUser = $this->get("user");
		
		return $oTemplate->parse(array(
			"user" => $oUser,
			"linkcancel" => \BaikalAdmin\Controler\Users::link(),
			"action" => \BaikalAdmin\Controler\Users::linkEdit($oUser),
			"messages" => $this->get("messages"),
		));
	}
}