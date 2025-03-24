<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'Test1');

class Database {
    private $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            $this->conn->set_charset("utf8");
        } catch(Exception $e) {
            echo "Connection error: " . $e->getMessage();
        }
        return $this->conn;
    }
} 