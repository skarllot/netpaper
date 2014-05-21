<?php

namespace bll;
require_once("dal/user.php");
require_once("bll/session.php");

class User
{
	private $dal_user;
	private $dal_ldap;

	function __construct() {
		if (!\dal\Connection::isConnected())
			\dal\Connection::connect();
		$this->dal_user = new \dal\User();
		$this->dal_ldap = new \dal\Ldap();
	}

	function createFirstLogin($user, $password, $email, $name) {
		if ($this->hasUsers())
			return False;

		$password = self::saltPassword($user, $password);
		return $this->dal_user->createUser($user, $password, $email, $name,
			true, false, 1);
	}

	function hasUsers() {
		return ($this->dal_user->getUsersCount() > 0);
	}

	function logon($user, $password) {
		Session::setIsAdmin(False);
		Session::setUser(NULL);

		if ($this->dal_user->isLdap($user))
			$row = $this->logonLdap($user, $password);
		else
			$row = $this->logonLocal($user, $password);

		if (!isset($row))
			return False;

		Session::setUser($user);
		Session::setIsAdmin(((bool)$row['admin']));
		Session::setLanguage($row['language']);
		return True;
	}

	private function logonLdap($user, $password) {
		$row = $this->dal_user->getUser($user);
		if (!isset($row))
			return NULL;

		$ldapcfg = $this->dal_ldap->getConfig();
		if (!isset($ldapcfg))
			return NULL;

		$ret = $this->dal_ldap->hasUser($ldapcfg['servers_name'],
			$ldapcfg['domain_name'], $user, $password);
		if (!$ret)
			return NULL;

		return $row;
	}

	private function logonLocal($user, $password) {
		$password = self::saltPassword($user, $password);
		$row = $this->dal_user->getUserWithPassword($user, $password);
		if (!isset($row))
			return NULL;

		return $row;
	}

	private static function saltPassword($user, $password) {
		return hash('sha256',
			sha1(strval(strlen($user))).$user.
			sha1(strval(strlen($password))).$password.
			hash('sha256', $user)
		);
	}
}

/*
vim: ts=4 sw=4
*/
?>
