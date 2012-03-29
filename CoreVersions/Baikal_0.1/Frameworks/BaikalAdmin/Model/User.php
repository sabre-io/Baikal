<?php

namespace BaikalAdmin\Model;

class User extends \Flake\Core\Model\Db {
	const DATATABLE = "users";
	const PRIMARYKEY = "id";
	const LABELFIELD = "username";
	
	protected $oIdentityPrincipal = null;
	
	public function initByPrimary($sPrimary) {
		parent::initByPrimary($sPrimary);
		
		# Initializing principals
		$this->oIdentityPrincipal = $this->getIdentityPrincipal();
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
		$this->oIdentityPrincipal->persist();
	}
	
	public function destroy() {
		# we have to delete user, and all related resources (principals, calendars, calendar events, contact books and contacts)
		die("TODO: Implement destroy() on " . self::getClass() . "(" . __FILE__ . ":" . __LINE__ . ")");
	}
	
	protected function getIdentityPrincipal() {
		$oPrincipal = new \BaikalAdmin\Model\Principal("principals/" . $this->get("username"));
		return $oPrincipal;
	}
	
	public function getMailtoURI() {
		return "mailto:" . rawurlencode($this->get("displayname") . " <" . $this->get("email") . ">");
	}
	
	# Empty form, 
	public static function formEmpty($options = array()) {
		$sClass = get_called_class();
		$oForm = new \Formal\Core\Form($sClass, $options);
		
		$oForm->add(new \Formal\Element\Text(array(
			"prop" => "username",
			"label" => "Username",
			"validation" => "required"
		)));
		
		$oForm->add(new \Formal\Element\Text(array(
			"prop" => "displayname",
			"label" => "Display name",
			"validation" => "required"
		)));
		
		$oForm->add(new \Formal\Element\Text(array(
			"prop" => "email",
			"label" => "Email",
			"validation" => "required,email"
		)));
		
		return $oForm;
	}
	
	public function formForInstance($options = array()) {
		$oForm = self::formEmpty($options)->setModelInstance($this);
		$oForm->element("username")->setOption("readonly", true);
		
		return $oForm;
	}
}