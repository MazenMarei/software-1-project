<?php
namespace App\core;
use PDO;

define("HOST" , "localhost");
define("DB_NAME" , "artshelf");
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
            } catch (\PDOException $e) {
                return "Connection failed: " . $e->getMessage();
            }
        }
        return $this->connection;
    }

    public function disconnect() {
        $this->connection = null;
    }

    public function __destruct() {
        $this->disconnect();
    }

    public function getAll($query){
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            return [
                "status" => true,
                "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
        } catch (\Throwable $th) {
           return [
                "status" => false,
                "message" => $th->getMessage()
            ];
           
        } 
    } 

    public  function getOne($query){
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            return [
                "status" => true,
                "data" => $stmt->fetch(PDO::FETCH_ASSOC)
            ];
        } catch (\Throwable $th) {
           return [
                "status" => false,
                "message" => $th->getMessage()
            ];
           
        } 
    }

    public function update($query) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            return [
                "status" => true,
                "message" => "Data updated successfully",
                "data" => $stmt->rowCount()
            ];
        } catch (\Throwable $th) {
           return [
                "status" => false,
                "message" => $th->getMessage()
            ];
           
        } 
    }
    
    public function delete($query) {
        try {
            $stmt = $this->connection->prepare($query);
            $stmt->execute();
            return [
                "status" => true,
                "message" => "Data deleted successfully",
                "data" => $stmt->rowCount()
            ];
        } catch (\Throwable $th) {
           return [
                "status" => false,
                "message" => $th->getMessage()
            ];
           
        } 
    }


    
    
}