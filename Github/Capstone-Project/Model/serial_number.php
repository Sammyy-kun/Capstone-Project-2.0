<?php
class SerialNumber {
    private $conn;
    private $table = "product_serial_numbers";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addSerials($product_id, $serials) {
        $sql = "INSERT INTO {$this->table} (product_id, serial_number, status) VALUES (:pid, :serial, 'in_stock')";
        $stmt = $this->conn->prepare($sql);
        
        $count = 0;
        foreach ($serials as $serial) {
            $serial = trim($serial);
            if (!empty($serial)) {
                try {
                    $stmt->execute([':pid' => $product_id, ':serial' => $serial]);
                    $count++;
                } catch (PDOException $e) {
                    // Skip duplicates
                }
            }
        }
        return $count;
    }

    public function getByProduct($product_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE product_id = :pid");
        $stmt->execute([':pid' => $product_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($serial, $status, $distributed_to = null) {
        $sql = "UPDATE {$this->table} SET status = :status, distributed_to = :dto WHERE serial_number = :serial";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':dto' => $distributed_to,
            ':serial' => $serial
        ]);
    }
}
?>
