<?php
class Technician {
    private $conn;
    private $table = "technician_profiles";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create or Update Profile
    public function saveProfile($user_id, $specialization, $bio, $status = 'offline') {
        // Check if exists
        $stmt = $this->conn->prepare("SELECT id FROM {$this->table} WHERE user_id = :uid");
        $stmt->execute([':uid' => $user_id]);
        $exists = $stmt->fetch();

        if ($exists) {
            $sql = "UPDATE {$this->table} SET specialization = :spec, bio = :bio, status = :status WHERE user_id = :uid";
        } else {
            $sql = "INSERT INTO {$this->table} (user_id, specialization, bio, status) VALUES (:uid, :spec, :bio, :status)";
        }

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':uid' => $user_id,
            ':spec' => $specialization,
            ':bio' => $bio,
            ':status' => $status
        ]);
    }

    // Get All Technicians with User Info
    public function getAllTechnicians() {
        $sql = "SELECT t.*, u.full_name, u.email, u.phone 
                FROM {$this->table} t
                JOIN users u ON t.user_id = u.id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Assign Job
    public function assignJob($repair_id, $technician_id) {
        $sql = "INSERT INTO repair_assignments (repair_id, technician_id) VALUES (:rid, :tid)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':rid' => $repair_id, ':tid' => $technician_id]);
    }

    // Get Assigned Jobs for a Technician
    public function getAssignedJobs($technician_id) {
        $sql = "SELECT ra.*, r.description, r.status as repair_status, u.full_name as customer_name
                FROM repair_assignments ra
                JOIN repairs r ON ra.repair_id = r.id
                JOIN users u ON r.user_id = u.id
                WHERE ra.technician_id = :tid";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':tid' => $technician_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
