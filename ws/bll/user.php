<?php

namespace bll;
require_once("dal/user.php");
require_once("bll/session.php");

class User
{
	private $dal_user;

	function __construct() {
		if (!\dal\Connection::isConnected())
			\dal\Connection::connect();
		$this->dal_user = new \dal\User();
	}

	function createFirstLogin($user, $password, $email, $name) {
		if ($this->dal_user->hasUsers())
			return False;

		$password = $this->saltPassword($user, $password);
		return $this->dal_user->createUser($user, $password, $email, $name,
			true, false, 0);
	}

	function hasUsers() {
		return ($this->dal_user->getUsersCount() > 0);
	}

	function logon($user, $password) {
		Session::setIsAdmin(False);
		Session::setUser(NULL);

		if ($this->dal_user->isLdap($user))
			$this->logonLdap($user, $password);
		else
			$this->logonLocal($user, $password);
	}

	private function saltPassword($user, $password) {
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
