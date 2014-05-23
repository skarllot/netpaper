<?php

namespace dal;
require_once("config.inc.php");

class Connection
{
    private static $dsn;
    private static $user;
    private static $password;
    private static $isInitialized;
    /**
     *
     * @var \PDO
     */
    private $pdo;

    function __construct() {
        if (!self::$isInitialized)
            self::init();
    }
    
    function __destruct() {
        $this->pdo = NULL;
    }

    public function connect() {
		try {
			$this->pdo = new \PDO(self::$dsn, self::$user, self::$password);
			$this->pdo->setAttribute(\PDO::ATTR_ERRMODE,
                    \PDO::ERRMODE_EXCEPTION);
		} catch (\PDOException $e) {
			die($e->getMessage());
		}
	}

    private static function init() {
        self::$dsn =  \Configuration::DB_DSN;
        self::$user = \Configuration::DB_USER;
        self::$password = \Configuration::DB_PASSWORD;
        self::$isInitialized = TRUE;
    }
    
    public function insert($sql, array $params) {
		$query = $this->pdo->prepare($sql);
		try { $query->execute($params); }
		catch (\PDOException $e) { die($e->getMessage()); }

		$ret = array('count' => $query->rowCount(),
			'lastId' => $this->pdo->lastInsertId());
		$query->closeCursor();
		return $ret;
	}

	public function isConnected() {
		return (isset($this->pdo) && $this->pdo instanceof \PDO);
	}

	public function query($sql, array $params) {
		$query = $this->pdo->prepare($sql);
		try { $query->execute($params); }
		catch (\PDOException $e) { die($e->getMessage()); }

		$rows = $query->fetchAll();
		$query->closeCursor();
		return $rows;
	}
}

/*
vim: ts=4 sw=4
*/
