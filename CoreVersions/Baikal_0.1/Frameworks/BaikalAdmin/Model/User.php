<?php

namespace BaikalAdmin\Model;

class User extends \Flake\Core\Model\Db {
	const DATATABLE = 'users';
	const PRIMARYKEY = 'username';
}