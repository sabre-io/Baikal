<?php

abstract class Tabulator {
	
	protected $aColumns = array();
	protected $iNbValues = 0;
	
	public function	addColumn(TabulatorColumn $oColumn) {
		$this->aColumns[$oColumn->getName()] = $oColumn;
	}
	
	public function	hasColumn($sName) {
		return array_key_exists($sName, $this->aColumns);
	}
	
	public function	&getColumn($sName) {
		return $this->aColumns[$sName];
	}
	
	protected function renderHeader() {
		
		$aHeaderLine = array();
		
		# on rend les entêtes
		reset($this->aColumns);
		while(list($sName,) = each($this->aColumns)) {
			$aHeaderLine[] = $this->aColumns[$sName]->renderHeader();
		}
		
		return $this->wrapHeaders(implode($this->getSep(), $aHeaderLine));
	}
	
	protected function renderValues() {
		$aRes = array();
		
		for($k = 0; $k < $this->iNbValues; $k++) {
			$aLine = array();
			reset($this->aColumns);
			while(list($sName,) = each($this->aColumns)) {
				$aLine[] = $this->aColumns[$sName]->renderValueAtIndex($k);
			}
			
			$aRes[] = $this->wrapValues(implode($this->getSep(), $aLine), $k);
			
			if((($k+1) % $this->repeatHeadersEvery()) === 0 && ($k+1 < $this->iNbValues)) {
				$aRes[] = $this->renderHeader();
			}
		}
		
		return implode("", $aRes) . "\n";
	}
	
	public function	renderAndDisplay($aValues) {
		echo $this->render($aValues);		
	}
	
	public function	render($aValues) {
		$this->iNbValues = count($aValues);
		
		$sRes = "";
		
		reset($aValues);
		while(list($iRow,) = each($aValues)) {
			reset($aValues[$iRow]);
			while(list($sColumnName,) = each($aValues[$iRow])) {
				if($this->hasColumn($sColumnName)) {
					$this->getColumn($sColumnName)->addValue($aValues[$iRow][$sColumnName]);
				}
			}
		}
		
		$sRes .= $this->renderOpen();
		$sRes .= $this->renderHeader();
		$sRes .= $this->renderValues();
		$sRes .= $this->renderClose();
		
		return $sRes;
	}
	
	abstract protected function getSep();
	abstract protected function wrapHeaders($sHeaders);
	abstract protected function wrapValues($sValues, $iRowNum);
	abstract protected function renderOpen();
	abstract protected function renderClose();
	abstract protected function repeatHeadersEvery();
}