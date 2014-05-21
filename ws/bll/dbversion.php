<?php

namespace bll;
require_once("dal/dbversion.php");

class DBVersion
{
	private $dal_dbversion;

	function __construct() {
		if (!\dal\Connection::isConnected())
			\dal\Connection::connect();
		$this->dal_dbversion = new \dal\DBVersion();
	}

	function getVersion() {
		return $this->dal_dbversion->getVersion();
	}
}

/*
vim: ts=4 sw=4
*/
?>
