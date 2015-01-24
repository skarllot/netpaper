<?php

namespace dal;
require_once("dal/connection.php");

class Location extends Connection
{
	const SQL_GET_LOCATIONS = 'SELECT id, name, description FROM location';

	function getLocations() {
		return $this->query(self::SQL_GET_LOCATIONS, array());
	}
}

/*
vim: ts=4 sw=4
*/
?>
