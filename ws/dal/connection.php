<?php

namespace dal;
require_once("config.inc.php");

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

	public static function isConnected() {
		return (isset(self::$pdo) && self::$pdo instanceof \PDO);
	}

	public function query($sql, array $params) {
		$query = self::$pdo->prepare($sql);
		try { $query->execute($params); }
		catch (\PDOException $e) { die($e->getMessage()); }

		$rows = $query->fetchAll();
		$query->closeCursor();
		return $rows;
	}

	public function query_write($sql, array $params) {
		$query = self::$pdo->prepare($sql);
		try { $query->execute($params); }
		catch (\PDOException $e) { die($e->getMessage()); }

		$count = $query->rowCount();
		$query->closeCursor();
		return $count;
	}
}

/*
vim: ts=4 sw=4
*/
?>
