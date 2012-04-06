<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2012 Jérôme Schneider <mail@jeromeschneider.fr>
*  All rights reserved
*
*  http://baikal.codr.fr
*
*  This script is part of the Baïkal Server project. The Baïkal
*  Server project is free software; you can redistribute it
*  and/or modify it under the terms of the GNU General Public
*  License as published by the Free Software Foundation; either
*  version 2 of the License, or (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

namespace Formal;

class Form {
	
	protected $sModelClass = "";
	protected $aOptions = array(
		"action" => "",
	);
	protected $oModelInstance = null;
	protected $oElements = null;
	protected $aErrors = array();
	protected $bPersisted = null;		# TRUE when form has persisted; available only after execute
	
	protected $sDisplayTitle = "";		# Displayed form title; generated in setModelInstance()
	protected $sDisplayMessage = "";	# Displayed confirm message; generated in execute()
	
	public function __construct($sModelClass, $aOptions = array()) {
		$this->sModelClass = $sModelClass;
		$this->aOptions = array_merge($this->aOptions, $aOptions);
		$this->oElements = new \Flake\Core\CollectionTyped("\Formal\Element");
	}
	
	public function option($sName) {
		if(array_key_exists($sName, $this->aOptions)) {
			return $this->aOptions[$sName];
		}
		
		throw new \Exception("\Formal\Form->option(): Option '" . htmlspecialchars($sName) . "' not found.");
	}
	
	public function setOption($sName, $sValue) {
		$this->aOptions[$sName] = $sValue;
		return $this;
	}
	
	public function options() {
		$aOptions = $this->aOptions;
		return $aOptions;
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
		
		# Displayed form title is generated depending on modelInstance floatingness
		
		if($this->floatingModelInstance()) {
			$this->sDisplayTitle = "Creating new " . $this->getModelInstance()->getHumanName();
		} else {
			$this->sDisplayTitle = "Editing " . $this->getModelInstance()->getHumanName() . " <strong>" . $this->getModelInstance()->getLabel() . "</strong>";
		}
		
		return $this;
	}
	
	public function getModelInstance() {
		return $this->oModelInstance;
	}
	
	public function floatingModelInstance() {
		return $this->getModelInstance()->floating();
	}
	
	public function execute() {
		# Obtaining morphology from model object
		$oMorpho = $this->getModelInstance()->formMorphologyForThisModelInstance();
		
		$this->aErrors = array();
		$oMorpho->elements()->reset();
		foreach($oMorpho->elements() as $oElement) {
			# If element is readonly, skip process
			if($oElement->option("readonly")) {
				continue;
			}
			
			$sPropName = $oElement->option("prop");
			
			# posted value is fetched, then passes to element before persistance
			$sPostValue = $this->postValue($sPropName);
			$oElement->setValue($sPostValue);
			
			$sValue = $oElement->value();
			
			$this->getModelInstance()->set(
				$sPropName,
				$sValue
			);
		}
		
		$oMorpho->elements()->reset();
		foreach($oMorpho->elements() as $oElement) {
			$aValidation = $oElement->optionArray("validation");
			if(empty($aValidation)) {
				continue;
			}
			
			$sValue = $oElement->value();
			
			foreach($aValidation as $sValidation) {
				
				# If element is readonly, skip process
				if($oElement->option("readonly")) {
					continue;
				}

				$sParam = FALSE;
				if(strpos($sValidation, ":") !== FALSE) {
					$sValidation = strtok($sValidation, ":");
					$sParam = strtok(":");
				}
				
				$sMethod = "validate" . ucfirst(strtolower($sValidation));
				if(!method_exists($this, $sMethod)) {
					throw new \Exception("\Formal\Form::execute(): no validation method for '" . htmlspecialchars($sValidation) . "'");
				}
				
				if($sParam === FALSE) {
					$mValid = $this->$sMethod($sValue, $oMorpho, $oElement);
				} else {
					$mValid = $this->$sMethod($sValue, $oMorpho, $oElement, $sParam);
				}

				if($mValid !== TRUE) {
					$this->aErrors[] = array(
						"element" => $oElement,
						"message" => $mValid,
					);
					
					$oElement->setOption("error", TRUE);
					
					break;	# one error per element per submit
				}
			}
		}
		
		if(empty($this->aErrors)) {
			
			# Model object is persisted
			# Last chance to generate a confirm message corresponding to what *was* submitted ("Creating", instead of "Editing")
			
			if($this->floatingModelInstance()) {
				$this->sDisplayMessage = \Formal\Core\Message::notice(
					$this->getModelInstance()->getHumanName() . " <i class='" . $this->getModelInstance()->getIcon() . "'></i> <strong>" . $this->getModelInstance()->getLabel() . "</strong> has been created."
				);
			} else {
				$this->sDisplayMessage = \Formal\Core\Message::notice(
					"Changes on <i class='" . $this->getModelInstance()->getIcon() . "'></i> <strong>" . $this->getModelInstance()->getLabel() . "</strong> have been saved."
				);
			}
			
			$this->getModelInstance()->persist();
			$this->bPersisted = TRUE;
		} else {
			$this->bPersisted = FALSE;
		}
	}
	
	public function persisted() {
		if(is_null($this->bPersisted)) {
			throw new \Exception("\Formal\Form->persisted(): information is not available yet. This method may only be called after execute()");
		}
		
		return $this->bPersisted;
	}
	
	public function validateRequired($sValue, \Formal\Form\Morphology $oMorpho, \Formal\Element $oElement) {
		if(trim($sValue) !== "") {
			return TRUE;
		}
		
		return "<strong>" . $oElement->option("label") . "</strong> is required.";
	}
	
	public function validateEmail($sValue, \Formal\Form\Morphology $oMorpho, \Formal\Element $oElement) {
		if(\Flake\Util\Tools::validEmail($sValue)) {
			return TRUE;
		}
		
		return "<strong>" . $oElement->option("label") . "</strong> should be an email.";
	}
	
	public function validateSameas($sValue, \Formal\Form\Morphology $oMorpho, \Formal\Element $oElement, $sReferencePropName) {
		$sReferenceValue = $oMorpho->element($sReferencePropName)->value();
		if($sValue === $sReferenceValue) {
			return TRUE;
		}
		
		return "<strong>" . $oElement->option("label") . "</strong> does not match " . $oMorpho->element($sReferencePropName)->option("label") . ".";
	}
	
	public function validateUnique($sValue, \Formal\Form\Morphology $oMorpho, \Formal\Element $oElement) {
		$oColl = $this->getModelInstance()->getBaseRequester()->addClauseEquals(
			$oElement->option("prop"),
			$sValue
		)->execute();
		
		if($oColl->count() > 0) {
			return "<strong>" . $oElement->option("label") . "</strong> has to be unique. Given value is not available.";
		}
		
		return TRUE;
	}
	
	public function postValue($sPropName) {
		return \Flake\Util\Tools::POST($sPropName);
	}
	
	public function render() {
		$aHtml = array();
		
		$oMorpho = $this->getModelInstance()->formMorphologyForThisModelInstance();
		
		$oMorpho->elements()->reset();
		foreach($oMorpho->elements() as $oElement) {
			
			# Setting current prop value for element
			# Set on empty (just created) FormMorphology
			# And obtained from Model instance
			
			$oElement->setValue(
				$this->getModelInstance()->get(
					$oElement->option("prop")
				)
			);
			$aHtml[] = $oElement->render();
		}
		
		$elements = implode("\n", $aHtml);
		$sModelClass = $this->sModelClass;
		
		######################################################
		# Displaying messages
		######################################################
		
		if($this->submitted()) {
			
			# There were errors detected during execute()
			# Error messages are displayed
			
			if(!empty($this->aErrors)) {
				$this->sDisplayMessage = "";
				$aMessages = array();
				reset($this->aErrors);
				foreach($this->aErrors as $aError) {
					$aMessages[] = $aError["message"];
				}
				
				$this->sDisplayMessage = \Formal\Core\Message::error(
					implode("<br />", $aMessages)
				);				
			}
		}
		
		$sSubmittedFlagName = $this->sModelClass . "::submitted";
		$sCloseUrl = $this->option("closeurl");
		$sActionUrl = $this->option("action");
		
		$sHtml =<<<HTML
<form class="form-horizontal" action="{$sActionUrl}" method="post" enctype="multipart/formdata">
	<input type="hidden" name="{$sSubmittedFlagName}" value="1" />
	<fieldset>
		<legend>{$this->sDisplayTitle}</legend>
		{$this->sDisplayMessage}
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
}