<?php
require_once __DIR__ . "/config.php";

class Database {
    public $connection;
    public $message = "[!] Successfully connected to database!\n";

    /*function __construct() {
        return $this->getConnection();
    }*/

    public function getConnection() {
        try {
            $this->connection = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->connection->exec("set names utf8");
            echo $this->message;
        } catch (PDOException $exception){
            echo "Connection error: ". $exception->getMessage();
        }
        return $this->connection;
    }
}