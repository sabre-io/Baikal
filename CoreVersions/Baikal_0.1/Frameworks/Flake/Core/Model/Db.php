<?php

namespace Flake\Core\Model;

abstract class Db extends \Flake\Core\Model {

	public function __construct($sPrimary) {
		if(($this->aData = $this->getRecordByPrimary($sPrimary)) === FALSE) {
			throw new \Exception("\Flake\Core\Model '" . $sPrimary . "' not found");
		}
	}

	public static function &getBaseRequester() {
		$oRequester = new \Flake\Core\Requester(self::getClass());
		$oRequester->setDataTable(self::getDataTable());
	
		return $oRequester;
	}

	public static function &getByRequest(\FS\Core\Requester $oRequester) {
		// renvoie une collection de la classe du modèle courant (this)
		return $oRequester->execute();
	}

	public static function getDataTable() {
		$sClass = self::getClass();
		return $sClass::DATATABLE;
	}
	
	public static function getPrimaryKey() {
		$sClass = self::getClass();
		return $sClass::PRIMARYKEY;
	}

	protected function getRecordByPrimary($sPrimary) {
		$rSql = $GLOBALS["DB"]->exec_SELECTquery(
			"*",
			self::getDataTable(),
			self::getPrimaryKey() . "='" . $GLOBALS["DB"]->quoteStr($sPrimary) . "'"
		);
	
		if(($aRs = $GLOBALS["DB"]->fetch($rSql)) !== FALSE) {
			reset($aRs);
			return $aRs;
		}
	
		return FALSE;
	}
}