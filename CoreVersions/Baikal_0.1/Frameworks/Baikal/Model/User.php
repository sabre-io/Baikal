<?php

namespace Baikal\Model;

class User extends \Flake\Core\Model\Db {
	const DATATABLE = "users";
	const PRIMARYKEY = "id";
	const LABELFIELD = "username";
	
	protected $aData = array(
		"username" => "",
	);
	
	protected $oIdentityPrincipal = null;
	
	public function initByPrimary($sPrimary) {
		parent::initByPrimary($sPrimary);
		
		# Initializing principals
		$this->oIdentityPrincipal = \Baikal\Model\Principal::getBaseRequester()
			->addClauseEquals("uri", "principals/" . $this->get("username"))
			->execute()
			->first();
	}
	
	public function initFloating() {
		parent::initFloating();
		
		# Initializing principals
		$this->oIdentityPrincipal = new \Baikal\Model\Principal();
	}
	
	public function get($sPropName) {
		try {
			# does the property exist on the model object ?
			$sRes = parent::get($sPropName);
		} catch(\Exception $e) {
			# no, it may belong to the oIdentityPrincipal model object
			$sRes = $this->oIdentityPrincipal->get($sPropName);
		}
		
		return $sRes;
	}
	
	public function set($sPropName, $sPropValue) {
		try {
			# does the property exist on the model object ?
			parent::set($sPropName, $sPropValue);
		} catch(\Exception $e) {
			# no, it may belong to the oIdentityPrincipal model object
			$this->oIdentityPrincipal->set($sPropName, $sPropValue);
		}
		
		return $this;
	}
	
	public function persist() {
		
		# Persisted first, as Model users loads this data
		$this->oIdentityPrincipal->set("uri", "principals/" . $this->get("username"));
		$this->oIdentityPrincipal->persist();
		
		if($this->floating()) {
			parent::persist();
		}
	}
	
	public function destroy() {
		# TODO: delete all related resources (principals, calendars, calendar events, contact books and contacts)
		
		# Destroying identity principal
		$this->oIdentityPrincipal->destroy();
		
		parent::destroy();
	}
	
	public function getMailtoURI() {
		return "mailto:" . rawurlencode($this->get("displayname") . " <" . $this->get("email") . ">");
	}
	
	public function formForThisModelInstance($options = array()) {
		$sClass = get_class($this);
		$oForm = new \Formal\Form($sClass, $options);
		$oForm->setModelInstance($this);
		
		return $oForm;
	}
	
	public function formMorphologyForThisModelInstance() {
		$oMorpho = new \Formal\Form\Morphology();
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "username",
			"label" => "Username",
			"validation" => "required"
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "displayname",
			"label" => "Display name",
			"validation" => "required"
		)));
		
		$oMorpho->add(new \Formal\Element\Text(array(
			"prop" => "email",
			"label" => "Email",
			"validation" => "required,email"
		)));
		
		if(!$this->floating()) {
			$oMorpho->element("username")->setOption("readonly", true);
		}
		
		return $oMorpho;
	}
	
	public static function getIcon() {
		return "icon-user";
	}
}