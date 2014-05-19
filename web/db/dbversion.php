<?php

class DBVersion
{
	private $connection;

	function __construct(Connection $conn) {
		$this->connection = $conn;
	}

	function getVersion() {
		$query = 'SELECT * FROM dbversion';
		$result = $this->connection->query($query, array());
		if (!$result)
			die('Error querying database');
		if (mysql_num_rows($result) != 1)
			return 0;

		$val = mysql_fetch_assoc($result)["value"];
		$this->connection->freeQuery($result);
		return $val;
	}
}

/*
vim: ts=4 sw=4
*/
?>
