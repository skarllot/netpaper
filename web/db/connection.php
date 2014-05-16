<?php

class Connection
{
	private $link;

	function __destruct() {
		if ($this->link)
			mysql_close($this->link);
	}

	public function connect() {
		global $cfg;

		$this->link = mysql_connect($cfg["DB"]["SERVER"],
			$cfg["DB"]["USER"], $cfg["DB"]["PASSWORD"]);
		if (!$this->link)
			die('Cannot connect to database');

		mysql_select_db($cfg["DB"]["DATABASE"], $this->link) or die('Cannot select database');
	}

	public function getLink() {
		return $this->link;
	}
}

/*
vim: ts=4 sw=4
*/
?>
