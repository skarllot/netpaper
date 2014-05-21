<?php

namespace dal;
require_once("lib/ldap.php");
require_once("dal/connection.php");

class User extends Connection
{
	const SQL_GET_USERS_COUNT = 'SELECT count(id) AS count FROM user';
	const SQL_CREATE_USER = 'INSERT INTO user
		(user, password, email, name, admin, is_ldap, language)
		VALUES (:user, :pass, :email, :name, :admin, :isldap, :lang)';
	const SQL_IS_LDAP = 'SELECT is_ldap FROM user WHERE user = :user';

	function createUser($user, $password, $email, $name,
		$isadmin, $isldap, $lang) {
		$count = $this->query_write(self::SQL_CREATE_USER,
			array(':user' => $user, ':pass' => $password, ':email' => $email,
			':name' => $name, ':admin' => $isadmin, ':isldap' => $isldap,
			':lang' => $lang));
		if ($count != 1)
			return False;

		return True;
	}

	function getUsersCount() {
		$rows = $this->query(self::SQL_GET_USERS_COUNT, array());
		if (count($rows) != 1)
			return -1;

		return $rows[0]['count'];
	}

	function isLdap($user) {
		$rows = $this->query(self::SQL_IS_LDAP,
			array(':user' => $user));
		if (count($rows) != 1)
			return False;

		return ((bool)$rows[0]['is_ldap']);
	}

	private function logonLocal($user, $password) {
		$password = $this->saltPassword($user, $password);
		$query = "SELECT admin, language FROM user WHERE user = '%s' AND password = '%s'";
		$result = $this->connection->query($query, array($user, $password));
		if (!$result)
			die('Error querying database');
		if (mysql_num_rows($result) != 1)
			return False;

		$row = mysql_fetch_assoc($result);
		$this->connection->freeQuery($result);

		$_SESSION['user'] = $user;
		$_SESSION['language'] = $row['language'];
		$_SESSION['admin'] = ((bool)$row['admin']);
		return True;
	}

	private function logonLdap($user, $password) {
		// Database user validation
		$query = "SELECT admin, language FROM user WHERE user = '%s'";
		$result = $this->connection->query($query, array($user));
		if (!$result)
			die('Error querying database');
		if (mysql_num_rows($result) != 1)
			return False;

		$row = mysql_fetch_assoc($result);
		$this->connection->freeQuery($result);

		// LDAP user and password validation
		$ldapdb = new Ldap($this->connection);
		$ldapcfg = $ldapdb->getConfig();
		if (count($ldapcfg) == 0)
			return False;

		$ldap = new ldap\LDAP(
			$ldapcfg['servers_name'], $ldapcfg['domain_name'],
			$user, $password);
		$users = $ldap->get_users($user);

		if(empty($users[0]['name']))
			return False;

		// Set session variables
		$_SESSION['user'] = $user;
		$_SESSION['language'] = $row['language'];
		$_SESSION['admin'] = ((bool)$row['admin']);
		return True;
	}
}

/*
vim: ts=4 sw=4
*/
?>
