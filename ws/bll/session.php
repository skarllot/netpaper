<?php

namespace bll;

class Session
{
	const VALIDATION_ID = 'wBSsq2MYvFbOcdcv';

	public static function getIsAdmin() {
		return (isset($_SESSION['admin']) &&
			((bool)$_SESSION['admin']));
	}

	public static function setIsAdmin($isadmin) {
		$_SESSION['admin'] = $isadmin;
	}

	public static function getUser() {
		if (!isset($_SESSION['user']))
			return NULL;
		return $_SESSION['user'];
	}

	public static function setUser($user) {
		$_SESSION['user'] = $user;
	}

	public static function getIsValid() {
		return (isset($_SESSION['VALIDATION_ID']) &&
			$_SESSION['VALIDATION_ID'] == self::VALIDATION_ID);
	}

	public static function setIsValid($isvalid) {
		if ($isvalid)
			$_SESSION['VALIDATION_ID'] = self::VALIDATION_ID;
		else
			$_SESSION['VALIDATION_ID'] = NULL;
	}

	public static function createToken() {
		session_start();
		self::setIsValid(True);
		return session_id();
	}

	public static function setToken($token = NULL) {
		if (isset($token) && !empty($token))
			session_id($token);

		session_start();

		if (!self::getIsValid()) {
			self::destroyToken();
			return False;
		}
		return True;
	}

	public static function destroyToken($token = NULL) {
		if (isset($token) && !empty($token)) {
			session_id($token);
			session_start();
		}

		if (!self::getIsValid())
			return False;
		self::setIsValid(False);

		$_SESSION = array();
		if (isset($_COOKIE[session_name()]))
			setcookie(session_name(), '', time() - 42000, '/');
		session_destroy();
		return True;
	}
}

/*
vim: ts=4 sw=4
*/
?>
