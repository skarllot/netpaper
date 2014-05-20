<?php

	require_once("lib/nusoap/nusoap.php");
	require_once("config.inc.php");
	require_once("session.php");
	require_once("db/connection.php");
	require_once("db/dbversion.php");
	require_once("db/ldap.php");
	require_once("db/user.php");

	function createFirstLogin($token, $user, $password, $email, $name) {
		$session = initializeSession($token);
		if(!$session)
			return new nusoap_fault('1', 'initializeSession', 'Invalid session ID', '');

		$conn = new Connection();
		$conn->connect();
		$userclass = new User($conn);
		return $userclass->createFirstLogin($user, $password, $email, $name);
	}

	function createSession() {
		$session = new Session();
		return $session->create();
	}

	function destroySession($token) {
		$session = new Session();
		return $session->destroy($token);
	}

	function getDBVersion($token) {
		$session = initializeSession($token);
		if(!$session)
			return new nusoap_fault('1', 'initializeSession', 'Invalid session ID', '');

		$conn = new Connection();
		$conn->connect();
		$dbversion = new DBVersion($conn);
		return $dbversion->getVersion();
	}

	function getLdapConfig($token) {
		$session = initializeSession($token);
		if(!$session)
			return new nusoap_fault('1', 'initializeSession', 'Invalid session ID', '');

		$conn = new Connection();
		$conn->connect();
		$ldap = new Ldap($conn);
		return $ldap->getConfig();
	}

	function hasUsers($token) {
		$session = initializeSession($token);
		if(!$session)
			return new nusoap_fault('1', 'initializeSession', 'Invalid session ID', '');

		$conn = new Connection();
		$conn->connect();
		$ldap = new User($conn);
		return !($ldap->isEmpty());
	}

	function logon($token, $user, $password) {
		$session = initializeSession($token);
		if(!$session)
			return new nusoap_fault('1', 'initializeSession', 'Invalid session ID', '');

		$conn = new Connection();
		$conn->connect();
		$ldap = new User($conn);
		return $ldap->logon($user, $password);
	}

	function initializeSession($token) {
		$session = new Session();
		if (!$session->start($token))
			return NULL;

		return $session;
	}

/*
vim: ts=4 sw=4
*/
?>
