<?php

class Ldap
{
	private $connection;

	function __construct(Connection $conn) {
		$this->connection = $conn;
	}

	function getConfig() {
		$query = "SELECT domain_name, base_dn, servers_name, use_ssl,
			use_tls, port, filter FROM ldap";
		$result = $this->connection->query($query, array());
		if (!$result)
			die('Error querying database');
		if (mysql_num_rows($result) != 1)
			return array();

		$row = mysql_fetch_assoc($result);
		$val = array('domain_name' => $row['domain_name'],
			'base_dn' => $row['base_dn'],
			'servers_name' => $row['servers_name'],
			'use_ssl' => ((bool)$row['use_ssl']),
			'use_tls' => ((bool)$row['use_tls']),
			'port' => $row['port'],
			'filter' => $row['filter']
		);
		$this->connection->freeQuery($result);
		return $val;
	}
}

/*
vim: ts=4 sw=4
*/
?>
