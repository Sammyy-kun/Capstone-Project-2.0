<?php
class User {
    private $conn;
    private $table = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function usernameExists($username) {
        $stmt = $this->conn->prepare("SELECT id FROM {$this->table} WHERE username = :username LIMIT 1");
        $stmt->execute([":username" => $username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function emailExists($email) {
        // Email is no longer unique for login, but we might want to know if it's used
        $stmt = $this->conn->prepare("SELECT id FROM {$this->table} WHERE email = :email LIMIT 1");
        $stmt->execute([":email" => $email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function register($username, $name, $email, $password, $role, $business_name = null, $account_type = 'individual') {
        $sql = "INSERT INTO {$this->table} (username, full_name, email, password, role, business_name, account_type)
                VALUES (:username, :name, :email, :pass, :role, :biz, :acct)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":username" => $username,
            ":name"     => $name,
            ":email"    => $email,
            ":pass"     => $password,
            ":role"     => $role,
            ":biz"      => $business_name,
            ":acct"     => $account_type,
        ]);
    }

    public function findByShopCode($code) {
        $stmt = $this->conn->prepare("SELECT id FROM {$this->table} WHERE shop_code = :code LIMIT 1");
        $stmt->execute([":code" => $code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function login($username) {
        // Login by Username
        $sql = "SELECT * FROM {$this->table} WHERE username=:username LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":username"=>$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status) {
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id' => $id
        ]);
    }

    public function updateUser($id, $username, $fullName, $email, $role) {
        $sql = "UPDATE {$this->table} SET username = :username, full_name = :fullName, email = :email, role = :role WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':username' => $username,
            ':fullName' => $fullName,
            ':email' => $email,
            ':role' => $role,
            ':id' => $id
        ]);
    }
}

