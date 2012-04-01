<?php

namespace Flake\Core\Model;

abstract class Db extends \Flake\Core\Model {
	
	protected $bFloating = TRUE;
	
	public function __construct($sPrimary = FALSE) {
		if($sPrimary === FALSE) {
			# Object will be floating
			$this->initFloating();
			$this->bFloating = TRUE;
		} else {
			$this->initByPrimary($sPrimary);
			$this->bFloating = FALSE;
		}
	}

	public static function &getBaseRequester() {
		$oRequester = new \Flake\Core\Requester\Sql(get_called_class());
		$oRequester->setDataTable(self::getDataTable());
	
		return $oRequester;
	}

	public static function &getByRequest(\FS\Core\Requester\Sql $oRequester) {
		// renvoie une collection de la classe du modèle courant (this)
		return $oRequester->execute();
	}

	public static function getDataTable() {
		$sClass = get_called_class();
		return $sClass::DATATABLE;
	}
	
	public static function getPrimaryKey() {
		$sClass = get_called_class();
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
		if($this->floating()) {
			$GLOBALS["DB"]->exec_INSERTquery(
				self::getDataTable(),
				$this->getData()
			);
			
			$sPrimary = $GLOBALS["DB"]->sql_insert_id();
			$this->initByPrimary($sPrimary);
			$this->bFloating = FALSE;
		} else {
			$GLOBALS["DB"]->exec_UPDATEquery(
				self::getDataTable(),
				self::getPrimaryKey() . "='" . $GLOBALS["DB"]->quoteStr($this->getPrimary()) . "'",
				$this->getData()
			);
		}
	}
	
	public function destroy() {
		$GLOBALS["DB"]->exec_DELETEquery(
			self::getDataTable(),
			self::getPrimaryKey() . "='" . $GLOBALS["DB"]->quoteStr($this->getPrimary()) . "'"
		);
	}
	
	protected function initFloating() {
		# nothing; object will be blank	
	}
	
	public function floating() {
		return $this->bFloating;
	}
}