<?php
class BusinessApplication {
    private $conn;
    private $table = "business_applications";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($data) {
        $fields = array_keys($data);
        $placeholders = array_map(function($f) { return ":$f"; }, $fields);
        
        $sql = "INSERT INTO {$this->table} (" . implode(", ", $fields) . ") 
                VALUES (" . implode(", ", $placeholders) . ")";
        
        $stmt = $this->conn->prepare($sql);
        
        foreach ($data as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }
        
        return $stmt->execute();
    }

    public function listAll() {
        $sql = "SELECT id, user_id, first_name, last_name, email, business_name, business_type,
                       business_email, business_phone, business_address, status, created_at, updated_at
                FROM {$this->table} ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listByStatus($status) {
        $sql = "SELECT id, user_id, first_name, last_name, email, business_name, business_type,
                       business_email, business_phone, business_address, status, created_at, updated_at
                FROM {$this->table} WHERE status = :status ORDER BY updated_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':status' => $status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status, $rejectionReason = null) {
        if ($rejectionReason !== null) {
            $sql = "UPDATE {$this->table} SET status = :status, rejection_reason = :reason WHERE id = :id";
            $stmt = $this->conn->prepare($sql);
            return $stmt->execute([
                ':status' => $status,
                ':reason' => $rejectionReason,
                ':id'     => $id
            ]);
        }
        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':id'     => $id
        ]);
    }

    public function findByUserId($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :uid ORDER BY created_at DESC LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateById($id, $data) {
        $safe = $data;
        unset($safe['id'], $safe['user_id']);
        if (empty($safe)) return false;
        $setClauses = array_map(function($k) { return "`$k` = :set_$k"; }, array_keys($safe));
        $sql = "UPDATE {$this->table} SET " . implode(", ", $setClauses) . " WHERE id = :where_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':where_id', $id, PDO::PARAM_INT);
        foreach ($safe as $key => $value) {
            $stmt->bindValue(":set_$key", $value);
        }
        return $stmt->execute();
    }
}
?>
