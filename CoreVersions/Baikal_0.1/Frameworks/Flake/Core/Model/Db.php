<?php

namespace Flake\Core\Model;

abstract class Db extends \Flake\Core\Model {

	public function __construct($sPrimary) {
		$this->initByPrimary($sPrimary);
	}

	public static function &getBaseRequester() {
		$oRequester = new \Flake\Core\Requester\Sql(self::getClass());
		$oRequester->setDataTable(self::getDataTable());
	
		return $oRequester;
	}

	public static function &getByRequest(\FS\Core\Requester\Sql $oRequester) {
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
	
	public function getPrimary() {
		return $this->get(self::getPrimaryKey());
	}

	protected function initByPrimary($sPrimary) {
		
		$rSql = $GLOBALS["DB"]->exec_SELECTquery(
			"*",
			self::getDataTable(),
			self::getPrimaryKey() . "='" . $GLOBALS["DB"]->quoteStr($sPrimary) . "'"
		);
	
		if(($aRs = $GLOBALS["DB"]->fetch($rSql)) === FALSE) {
			throw new \Exception("\Flake\Core\Model '" . htmlspecialchars($sPrimary) . "' not found for model " . self::getClass());
		}
		
		reset($aRs);
		$this->aData = $aRs;
	}
	
	public function persist() {
		$GLOBALS["DB"]->exec_UPDATEquery(
			self::getDataTable(),
			self::getPrimaryKey() . "='" . $GLOBALS["DB"]->quoteStr($this->getPrimary()) . "'",
			$this->getData()
		);
	}
	
	public function destroy() {
		$GLOBALS["DB"]->exec_DELETEquery(
			self::getDataTable(),
			self::getPrimaryKey() . "='" . $GLOBALS["DB"]->quoteStr($this->getPrimary()) . "'"
		);
	}
}