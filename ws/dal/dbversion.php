<?php

namespace dal;
require_once("dal/connection.php");

class DBVersion extends Connection
{
	const SQL_GET_VERSION = 'SELECT value FROM dbversion';

	function getVersion() {
		$rows = $this->query(self::SQL_GET_VERSION, array());
		if (count($rows) != 1)
			return '';

		return $rows[0]['value'];
	}
}

/*
vim: ts=4 sw=4
*/
?>
