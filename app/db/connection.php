<?php

namespace db;

class Connection
{
	protected static $pdo;

	public static function connect() {
		try {
			self::$pdo = new \PDO(\Configuration::DB_DSN,
				\Configuration::DB_USER, \Configuration::DB_PASSWORD);
			self::$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
		} catch (\PDOException $e) {
			die($e->getMessage());
		}
	}

	public function query($sql, array $params) {
		$query = self::$pdo->prepare($sql);
		$query->execute($params);
		$rows = $query->fetchAll();
		$query->closeCursor();
		return $rows;
	}

	public function query_write($query, array $params) {
		if (!$this->query($query, $params))
			die('Error querying database');

		return mysql_affected_rows($this->link);
	}
}

/*
vim: ts=4 sw=4
*/
?>
