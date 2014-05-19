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

	public function freeQuery($result) {
		mysql_free_result($result);
	}

	public function query($query, array $params) {
		if (count($params) > 0) {
			for ($i=0; $i<count($params); $i++)
				$params[$i] = $this->safeString($params[$i]);

			$query = vsprintf($query, $params);
		}

		return mysql_query($query, $this->link);
	}

	private function safeString($str) {
		return mysql_real_escape_string($str, $this->link);
	}
}

/*
vim: ts=4 sw=4
*/
?>
