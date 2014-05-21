<?php

	require_once("lib/nusoap/nusoap.php");
	require_once("bll/dbversion.php");
	require_once("bll/ldap.php");
	require_once("bll/session.php");
	require_once("bll/user.php");

	function createFirstLogin($token, $user, $password, $email, $name) {
		if (!\bll\Session::setToken($token))
			return new nusoap_fault('1', 'initializeSession', 'Invalid session ID', '');

		$userclass = new \bll\User();
		return $userclass->createFirstLogin($user, $password, $email, $name);
	}

	function createSession() {
		return \bll\Session::createToken();
	}

	function destroySession($token) {
		return \bll\Session::destroyToken($token);
	}

	function getDBVersion($token) {
		if (!\bll\Session::setToken($token))
			return new nusoap_fault('1', 'initializeSession', 'Invalid session ID', '');

		$dbversion = new \bll\DBVersion();
		return $dbversion->getVersion();
	}

	function getLdapConfig($token) {
		if (!\bll\Session::setToken($token))
			return new nusoap_fault('1', 'initializeSession', 'Invalid session ID', '');
		if (!\bll\Session::getIsAdmin())
			return new nusoap_fault('2', 'checkPermission', 'Insufficient permissions', '');

		$ldap = new \bll\Ldap();
		return $ldap->getConfig();
	}

	function hasUsers($token) {
		if (!\bll\Session::setToken($token))
			return new nusoap_fault('1', 'initializeSession', 'Invalid session ID', '');

		$userclass = new \bll\User();
		return $userclass->hasUsers();
	}

	function logon($token, $user, $password) {
		if (!\bll\Session::setToken($token))
			return new nusoap_fault('1', 'initializeSession', 'Invalid session ID', '');

		$userclass = new \bll\User();
		return $userclass->logon($user, $password);
	}

/*
vim: ts=4 sw=4
*/
?>
