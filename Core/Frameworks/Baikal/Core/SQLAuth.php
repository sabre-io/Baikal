<?php

namespace Baikal\Core;

/**
 * This is an authentication backend which executes an SQL statement to check
 * user credentials. For successful authentication credentials the statement
 * should return a single row with a single attribute containing the username
 * as the value. Where authentication fails no rows should be returned.
 *
 * This requires BASIC authentication so you should ensure SSL is enabled.
 *
 * The global database connection / configuration is used.
 *
 * Settings:
 *  BAIKAL_DAV_SQL_AUTH - The SQL statement to execute.
 *     This statement will be prepared using the PDO's prepare function using
 *     NAMED parameters. The username parameter name is :username; the password
 *     uses the name :password. Other available keys include:
 *       :remote - $_SERVER['REMOTE_ADDR']
 *       :server - $_SERVER['SERVER_NAME']
 *     The config requires this to be a litteral type to avoid quote validation
 *     issues - this means the whole query needs to be quoted in the admin UI.
 *     But, nicely, the framework just DEFINE()'s the internal string :).
 * NOTE: This is different to the BAIKAL_ADMIN_SQL_AUTH query which is used to
 * authenticate with the admin interface and only takes a username/password.
 *
 * See http://www.php.net/manual/en/pdo.prepare.php for examples / details.
 *
 * @author Andrew Bevitt (andrewbevitt) <andrew@andrewbevitt.com>
 * @license http://code.google.com/p/sabredav/wiki/License Modified BSD License
 */
class SQLAuth extends AbstractExternalAuth {

    /**
     * Prevent auto-creation of users because we're using the DB explicitly.
     */
    public function validateUserPass($username, $password) {
        if ($this->validateUserPassExternal($username, $password)) {
			# Password validated so make sure user principal exists
			$this->autoUserCreation($username);
			return TRUE; # and login succeeded
		}

		# auth failed so bail
		return FALSE;
    }

    /**
     * Validates the given username and password by executing the configured
     * SQL statement and checking the results. 
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function validateUserPassExternal($username, $password) {
        // grab the global database handler and prepare the query
		// trim()ed just in case the litteral doesn't do it
        $stmt = $this->pdo->prepare(trim(BAIKAL_DAV_SQL_AUTH, '"\''));
		$qParams = array();
		foreach( array(':username'=>$username, ':password'=>$password,
				':remote'=>$_SERVER['REMOTE_ADDR'],
				':server'=>$_SERVER['SERVER_NAME']) as $name => $value ) {
			if (FALSE !== strpos(BAIKAL_DAV_SQL_AUTH, $name))
				$qParams[$name] = $value;
		}
		/* now execute the statement with the required parameters */
        $stmt->execute($qParams);

        /* fetch the first row - if any and compare username */
        if ( $row = $stmt->fetch(\PDO::FETCH_NUM) )
            return 0 === strcasecmp($username, $row[0]);
        return FALSE;
    }
	
	/**
	 * Ensure principal for the user exists, because users are managed externally.
	 *
	 * @param string $username
	 */
	private function autoUserCreation($username) {
		// User should already exist (in the SQL store) but the principals won't
		// unless they've been created the admin view of the user list; so they
		// are created here also to ensure they exist.
		$cPrincipal = \Baikal\Model\Principal::getBaseRequester()
			->addClauseEquals("uri", "principals/" . $username)
			->execute()
			->first();
		if (is_null($cPrincipal)) {
			$cUser = \Baikal\Model\User::getBaseRequester()
				->addClauseEquals("username", $username)
				->execute()
				->first();
			$cUser->autoCreatePrincipal(TRUE); // TODO: Is this called anyway?
		}
	}
	
	/**
	 * Indicates whether this backend allows creating/editing/deleting of accounts.
	 * The SQL auth pulls users from an externally managed source so doesn't allow.
	 *
	 * @return Boolean indicating if backend can manage user accounts (default TRUE).
	 */
	public function canManageUsers() {
		return FALSE;
	}

}
 
