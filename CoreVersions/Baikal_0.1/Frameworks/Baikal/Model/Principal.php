<?php

namespace Baikal\Model;

class Principal extends \Flake\Core\Model\Db {
	const DATATABLE = "principals";
	const PRIMARYKEY = "id";
	protected $aData = array(
		"uri" => "",
		"displayname" => "",
		"email" => "",
	);
}