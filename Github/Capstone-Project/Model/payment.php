<?php
class Payment {
    private $conn;
    private $table = "payments";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createPayment($repair_id, $user_id, $amount, $method) {
        $sql = "INSERT INTO {$this->table} (repair_id, user_id, amount, method, status)
                VALUES (:rid, :uid, :amt, :method, 'pending')";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":rid" => $repair_id,
            ":uid" => $user_id,
            ":amt" => $amount,
            ":method" => $method
        ]);
    }

    public function getPaymentByRepairId($repair_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE repair_id = :rid LIMIT 1");
        $stmt->execute([":rid" => $repair_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserPayments($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE user_id = :uid ORDER BY created_at DESC");
        $stmt->execute([":uid" => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
