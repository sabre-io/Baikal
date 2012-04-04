<?php

namespace BaikalAdmin\Controler;

class Dashboard extends \Flake\Core\Controler {
	
	function execute() {
	}
	
	function render() {
		$sLinkUsers = \BaikalAdmin\Controler\Users::link();
		
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