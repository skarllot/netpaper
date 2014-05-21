<?php

namespace bll;
require_once("dal/ldap.php");

class Ldap
{
	private $dal_ldap;

	function __construct() {
		if (!\dal\Connection::isConnected())
			\dal\Connection::connect();
		$this->dal_ldap = new \dal\Ldap();
	}

	function getConfig() {
		return $this->dal_ldap->getConfig();
	}
}

/*
vim: ts=4 sw=4
*/
?>
