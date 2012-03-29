<?php

namespace Formal\Core;

class Form {
	
	protected $sModelClass = "";
	protected $aOptions = array();
	protected $oModelInstance = null;
	protected $oElements = null;
	
	public function __construct($sModelClass, $aOptions = array()) {
		$this->sModelClass = $sModelClass;
		$this->aOptions = array_merge($this->aOptions, $aOptions);
		$this->oElements = new \Flake\Core\CollectionTyped("\Formal\Element");
	}
	
	public function option($sName) {
		return $this->aOptions[$sName];
	}
	
	public function setModelInstance($oModelInstance) {
		if(!\Flake\Util\Tools::is_a($oModelInstance, $this->sModelClass)) {
			throw new \Exception("\Formal\Core->setModelInstance(): Given instance is not of class '" . $this->sModelClass . "'");
		}
		
		$this->oModelInstance = $oModelInstance;
		
		$this->oElements->reset();
		foreach($this->oElements as $oElement) {
			$oElement->setValue(
				$this->getModelInstance()->get(
					$oElement->option("prop")
				)
			);
		}
		
		return $this;
	}
	
	public function getModelInstance() {
		return $this->oModelInstance;
	}
	
	public function hasModelInstance() {
		return !is_null($this->getModelInstance());
	}
	
	public function add(\Formal\Element $oElement) {
		$this->oElements->push($oElement);
	}
	
	public function element($sPropName) {
		$this->oElements->reset();
		foreach($this->oElements as $oElement) {
			if($oElement->option("prop") === $sPropName) {
				return $oElement;
			}
		}
		
		throw new \Exception("\Formal\Core\Form->element(): Element prop='" . $sPropName . "' not found");
	}
	
	public function execute() {
		if(!$this->hasModelInstance()) {
			return;
		}
		
		$this->oElements->reset();
		foreach($this->oElements as $oElement) {
			# If element is readonly, skip process
			if($oElement->option("readonly")) {
				continue;
			}
			
			$sPropName = $oElement->option("prop");
			
			# posted value is fetched, then passes to element before persistance
			$sPostValue = $this->postValue($sPropName);
			$oElement->setValue($sPostValue);
			
			$this->getModelInstance()->set(
				$sPropName,
				$oElement->value()
			);
		}
		
		$this->getModelInstance()->persist();
	}
	
	public function postValue($sPropName) {
		# could be as well \Flake\Util\Tools::POST($sPropName)
		return \Flake\Util\Tools::POST($this->element($sPropName)->option("prop"));
	}
	
	public function render() {
		$aHtml = array();
		
		$this->oElements->reset();
		foreach($this->oElements as $oElement) {
			$aHtml[] = $oElement->render();
		}
		
		$elements = implode("\n", $aHtml);
		
		if($this->hasModelInstance()) {
			$sTitle = "Editing " . $this->getHumanModelName() . " <strong>" . $this->getModelInstance()->getLabel() . "</strong>";
		} else {
			$sTitle = "Creating new " . $this->getHumanModelName();
		}
		
		$sSubmittedFlagName = $this->sModelClass . "::submitted";
		$sCloseUrl = $this->option("closeurl");
		
		$sHtml =<<<HTML
<form class="form-horizontal" action="" method="post" enctype="multipart/formdata">
	<input type="hidden" name="{$sSubmittedFlagName}" value="1" />
	<fieldset>
		<legend>{$sTitle}</legend>
		{$elements}
		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Save changes</button>
			<a class="btn" href="{$sCloseUrl}">Close</a>
		</div>
	</fieldset>
</form>
HTML;
		
		return $sHtml;
	}
	
	public function submitted() {
		return intval(\Flake\Util\Tools::POST($this->sModelClass . "::submitted")) === 1;
	}
	
	public function getHumanModelName() {
		return array_pop(explode("\\", $this->sModelClass));
	}
}