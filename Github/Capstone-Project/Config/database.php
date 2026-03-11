<?php

if (!defined('DB_HOST')) {
    $configPath = __DIR__ . '/constants.php';
    if (file_exists($configPath)) {
        require_once $configPath;
    }
}

class Database {
    private $host;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct() {
        $this->host = defined('DB_HOST') ? DB_HOST : "localhost";
        $this->db_name = defined('DB_NAME') ? DB_NAME : "capstone_db";
        $this->username = defined('DB_USER') ? DB_USER : "root";
        $this->password = defined('DB_PASS') ? DB_PASS : "";
    }

    public function connect() {
        $this->conn = null;
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4";
            $this->conn = new PDO($dsn, $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            // Log error instead of leaking sensitive info in production, but for now exit is fine for debugging
            if (php_sapi_name() === 'cli') {
                throw $e;
            }
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Database Connection Failed: " . $e->getMessage()]);
            exit;
        }
        return $this->conn;
    }
}

