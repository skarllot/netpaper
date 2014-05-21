<?php

namespace dal;
require_once("dal/connection.php");
require_once("lib/ldap.php");

class Ldap extends Connection
{
	const SQL_GET_CONFIG = 'SELECT domain_name, base_dn, servers_name,
			use_ssl, use_tls, port, filter FROM ldap';

	function getConfig() {
		$rows = $this->query(self::SQL_GET_CONFIG, array());
		if (count($rows) != 1)
			return NULL;

		return $rows[0];
	}

	function hasUser($servers, $domain, $user, $password) {
		$ldap = new \ldap\LDAP($servers, $domain, $user, $password);
		$users = $ldap->get_users($user);

		return !(empty($users[0]['name']));
	}
}

/*
vim: ts=4 sw=4
*/
?>
