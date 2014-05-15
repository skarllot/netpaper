<?php

require_once dirname(__FILE__).'/../config/db.php';

class Connection
{
	private $link;

	function __destruct() {
		if ($this->link)
			mysql_close($this->link);
	}

	public function connect() {
		global $DB;
		$this->link = mysql_connect($DB["SERVER"], $DB["USER"], $DB["PASSWORD"]);
		if (!$this->link)
			die('Cannot connect to database');

		mysql_select_db($DB["DATABASE"], $this->link) or die('Cannot select database');
	}

	public function getLink() {
		return $this->link;
	}
}

/*
vim: ts=4 sw=4
*/
?>
