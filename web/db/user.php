<?php

class User
{
	private $connection;

	function __construct(Connection $conn) {
		$this->connection = $conn;
	}

	function getUsers() {
	}

	private function isLdap($user) {
		$query = "SELECT is_ldap FROM user WHERE user = '%s'";
		$result = $this->connection->query($query, array($user));
		if (!$result)
			die('Error querying database');
		if (mysql_num_rows($result) != 1)
			return False;

		$val = mysql_fetch_assoc($result)["is_ldap"];
		$this->connection->freeQuery($result);
		return ((bool)$val);
	}
}

/*
vim: ts=4 sw=4
*/
?>
