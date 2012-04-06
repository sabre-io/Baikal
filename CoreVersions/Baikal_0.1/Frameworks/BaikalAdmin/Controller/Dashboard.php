<?php

namespace BaikalAdmin\Controller;

class Dashboard extends \Flake\Core\Controller {
	
	function execute() {
	}
	
	function render() {
		$sLinkUsers = \BaikalAdmin\Controller\Users::link();
		
		$sMessage =<<<MESSAGE
Ici, infos sur:<br />
* Nombre d'utilisateurs<br />
* Nombre de calendriers<br />
* Nombre d'évènements<br />
* Version + Mise à jour de baikal<br />
* <a href='{$sLinkUsers}'>Lien vers liste des utilisateurs</a><br />
* Lien vers paramétrage<br />
MESSAGE;
		return $sMessage;
	}
}