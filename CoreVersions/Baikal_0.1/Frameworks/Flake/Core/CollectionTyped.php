<?php

namespace Flake\Core;

class CollectionTyped extends \Flake\Core\Collection {

	private $sTypeClassOrProtocol;

	/*
	 * Constructeur de la classe
	 *
	 * @param	$sTypeClassOrProtocol	string	type des éléments de la collection
	 * @return	void
	 */
	public function __construct($sTypeClassOrProtocol) {
		$this->sTypeClassOrProtocol = $sTypeClassOrProtocol;
		$this->setMetaType($this->sTypeClassOrProtocol);
	}

	/*
	 * Insère un élément dans la collection
	 *
	 * @param 	$mMixed	mixed	valeur à insérer dans la collection
	 * @return	void
	 */
	public function push(&$mMixed) {
		if(!\Flake\Util\Tools::is_a($mMixed, $this->sTypeClassOrProtocol)) {
			throw new \Exception("\Flake\Core\CollectionTyped<" . $this->sTypeClassOrProtocol . ">: Given object is not correctly typed.");
		}

		parent::push($mMixed);
	}
}