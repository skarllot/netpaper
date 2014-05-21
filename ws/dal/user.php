<?php

namespace dal;
require_once("dal/connection.php");

class User extends Connection
{
	const SQL_GET_USERS_COUNT = 'SELECT count(id) AS count FROM user';
	const SQL_CREATE_USER = 'INSERT INTO user
		(user, password, email, name, admin, is_ldap, language)
		VALUES (:user, :pass, :email, :name, :admin, :isldap, :lang)';
	const SQL_IS_LDAP = 'SELECT is_ldap FROM user WHERE user = :user';
	const SQL_GET_USER = 'SELECT id, name, admin, language
		FROM user WHERE user = :user';
	const SQL_GET_USER_WITH_PASSWORD = 'SELECT id, name, admin, language
		FROM user WHERE user = :user AND password = :pass';

	function createUser($user, $password, $email, $name,
		$isadmin, $isldap, $lang) {
		$ret = $this->insert(self::SQL_CREATE_USER,
			array(':user' => $user, ':pass' => $password, ':email' => $email,
			':name' => $name, ':admin' => $isadmin, ':isldap' => $isldap,
			':lang' => $lang));
		if ($ret['count'] != 1)
			return False;

		return True;
	}

	function getUser($user) {
		$rows = $this->query(self::SQL_GET_USER,
			array(':user' => $user));
		if (count($rows) != 1)
			return NULL;
		return $rows[0];
	}

	function getUserWithPassword($user, $password) {
		$rows = $this->query(self::SQL_GET_USER_WITH_PASSWORD,
			array(':user' => $user, ':pass' => $password));
		if (count($rows) != 1)
			return NULL;
		return $rows[0];
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
}

/*
vim: ts=4 sw=4
*/
?>
