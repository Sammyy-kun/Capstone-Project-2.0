<?php
class SparePart {
    private $conn;
    private $table = "spare_parts";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $sql = "SELECT s.*, sup.name as supplier_name 
                FROM {$this->table} s 
                LEFT JOIN suppliers sup ON s.supplier_id = sup.id 
                ORDER BY s.part_name ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function add($name, $number, $stock, $price, $supplier_id) {
        $sql = "INSERT INTO {$this->table} (part_name, part_number, stock, price, supplier_id) 
                VALUES (:name, :num, :stock, :price, :sup_id)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':name' => $name,
            ':num' => $number,
            ':stock' => $stock,
            ':price' => $price,
            ':sup_id' => $supplier_id
        ]);
    }

    public function checkLowStock() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE stock <= reorder_level");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function generateNextPartNumber($category, $type, $spec, $version = 'A') {
        // SKU Format: CAT-TYPE-SPEC-SEQ-VER
        // Example: ELE-MTR-12V-001-A
        $prefix = strtoupper("{$category}-{$type}-{$spec}-");
        
        $sql = "SELECT part_number FROM {$this->table} 
                WHERE part_number LIKE :prefix 
                ORDER BY part_number DESC LIMIT 1";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':prefix' => $prefix . '%']);
        $lastSku = $stmt->fetchColumn();

        if ($lastSku) {
            // Extract sequence (last 5 parts: 001-A)
            // Assuming format is rigid: PREFIX-001-A
            // Length of prefix is known.
            $parts = explode('-', $lastSku);
            // Expected: [cat, type, spec, seq, ver]
            // We want the second to last element
            if (count($parts) >= 5) {
                $seq = intval($parts[count($parts) - 2]);
                $nextSeq = str_pad($seq + 1, 3, '0', STR_PAD_LEFT);
            } else {
                $nextSeq = '001';
            }
        } else {
            $nextSeq = '001';
        }

        return $prefix . $nextSeq . '-' . strtoupper($version);
    }
}
?>
