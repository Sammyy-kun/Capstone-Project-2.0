<?php
class Supplier {
    private $conn;
    private $table = "suppliers";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY name ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($name, $contact, $address) {
        $sql = "INSERT INTO {$this->table} (name, contact_info, address) VALUES (:name, :contact, :address)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':name' => $name,
            ':contact' => $contact,
            ':address' => $address
        ]);
    }
}
?>
