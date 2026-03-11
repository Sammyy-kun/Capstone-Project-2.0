<?php
class Ownership {
    private $conn;
    private $table = "ownership_records";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addRecord($user_id, $brand, $model, $serial, $purchase_date) {
        $sql = "INSERT INTO {$this->table} (user_id, brand, model, serial_number, purchase_date, warranty_expiry)
                VALUES (:uid, :brand, :model, :serial, :pdate, :wdate)";
        
        // Simple warranty calculation: 1 year from purchase
        $warranty_expiry = date('Y-m-d', strtotime($purchase_date . ' + 1 year'));

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":uid" => $user_id,
            ":brand" => $brand,
            ":model" => $model,
            ":serial" => $serial,
            ":pdate" => $purchase_date,
            ":wdate" => $warranty_expiry
        ]);
    }

    public function getUserRecords($user_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE user_id = :uid ORDER BY created_at DESC");
        $stmt->execute([":uid" => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
