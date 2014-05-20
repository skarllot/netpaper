<?php

class Connection
{
	private $link;

	function __destruct() {
		if ($this->link)
			mysql_close($this->link);
	}

	public function connect() {
		$this->link = mysql_connect(Configuration::DB_SERVER,
			Configuration::DB_USER, Configuration::DB_PASSWORD);
		if (!$this->link)
			die('Cannot connect to database');

		mysql_select_db(Configuration::DB_DATABASE, $this->link)
			or die('Cannot select database');
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

		$result = mysql_query($query, $this->link);
		if (!$result)
			die('Error querying database');
		return $result;
	}

	public function query_write($query, array $params) {
		if (!$this->query($query, $params))
			die('Error querying database');

		return mysql_affected_rows($this->link);
	}

	private function safeString($str) {
		return mysql_real_escape_string($str, $this->link);
	}
}

/*
vim: ts=4 sw=4
*/
?>
