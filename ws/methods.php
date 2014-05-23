<?php

	require_once("lib/nusoap/nusoap.php");
    require_once 'bll/Migration.php';
    require_once 'bll/session.php';
	/*require_once("bll/dbversion.php");
	require_once("bll/ldap.php");
	require_once("bll/session.php");
	require_once("bll/user.php");*/

	function getDBVersion($token) {
		if (!\bll\Session::setToken($token))
			return new nusoap_fault('1', 'initializeSession', 'Invalid session ID', '');

        $migration = new bll\Migration();
        return $migration->getVersion();
	}

	function getLdapConfig($token) {
		if (!\bll\Session::setToken($token))
			return new nusoap_fault('1', 'initializeSession', 'Invalid session ID', '');
		if (!\bll\Session::getIsAdmin())
			return new nusoap_fault('2', 'checkPermission', 'Insufficient permissions', '');

		$ldap = new \bll\Ldap();
		return $ldap->getConfig();
	}

/*
vim: ts=4 sw=4
*/
?>
