<?php
namespace App\core;
use PDO;

define("HOST" , "localhost");
define("DB_NAME" , "artshell");
define("USER" , "root");
define("PASSWORD" , "");




class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
         $this->connect();
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function connect() {
        if ($this->connection === null) {
            try {
                $this->connection = new PDO("mysql:host=" . HOST . ";dbname=" . DB_NAME, USER, PASSWORD);
                $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                return $this->connection;
            } catch (PDOException $e) {
                return "Connection failed: " . $e->getMessage();
            }
        }
        return $this->connection;
    }

    public function disconnect() {
        $this->connection = null;
    }
}

