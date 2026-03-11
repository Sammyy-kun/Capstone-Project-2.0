<?php
class Repair {
    private $conn;
    private $table = "repairs";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function requestRepair($user_id, $owner_id, $description, $schedule_date, $service_type = 'walk_in', $issue_category = 'other', $delivery_fee = 0, $delivery_payment_method = null) {
        $sql = "INSERT INTO {$this->table} (user_id, owner_id, description, schedule_date, service_type, issue_category, delivery_fee, delivery_payment_method, status)
                VALUES (:uid, :oid, :desc, :date, :type, :cat, :dfee, :dmethod, 'pending')";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":uid"     => $user_id,
            ":oid"     => $owner_id,
            ":desc"    => $description,
            ":date"    => $schedule_date,
            ":type"    => $service_type,
            ":cat"     => $issue_category,
            ":dfee"    => $delivery_fee,
            ":dmethod" => $delivery_payment_method
        ]);
    }

    public function getOwnerRepairs($owner_id) {
        $sql = "SELECT r.*, u.full_name as customer_name, u.email as customer_email
                FROM {$this->table} r
                JOIN users u ON r.user_id = u.id
                WHERE r.owner_id = :oid
                ORDER BY r.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":oid" => $owner_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserRepairs($user_id) {
        $sql = "SELECT r.*, u.full_name as owner_name, u.business_name as shop_name
                FROM {$this->table} r
                LEFT JOIN users u ON r.owner_id = u.id
                WHERE r.user_id = :uid
                ORDER BY r.id DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":uid" => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update the status of a repair. Validates that the owner_id matches.
     */
    public function updateStatus($repair_id, $status, $owner_id) {
        $allowed = ['accepted', 'rejected', 'in_progress', 'completed'];
        if (!in_array($status, $allowed)) return false;

        $sql = "UPDATE {$this->table} SET status = :status WHERE id = :rid AND owner_id = :oid";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':status' => $status,
            ':rid'    => $repair_id,
            ':oid'    => $owner_id
        ]);
    }

    /**
     * Get queue position for each of the user's active (pending/accepted) repairs.
     */
    public function getQueuePositionsForUser($user_id) {
        $sql = "SELECT r.id, r.status, r.owner_id, r.created_at,
                u.full_name as shop_name,
                COALESCE(ba.business_name, u.full_name) as business_name,
                (SELECT COUNT(*) FROM repairs q
                 WHERE q.owner_id = r.owner_id
                 AND q.id < r.id
                 AND q.status IN ('pending', 'accepted', 'in_progress')) + 1 as queue_position,
                (SELECT COUNT(*) FROM repairs q2
                 WHERE q2.owner_id = r.owner_id
                 AND q2.status IN ('pending', 'accepted', 'in_progress')) as total_in_queue
                FROM repairs r
                JOIN users u ON u.id = r.owner_id
                LEFT JOIN business_applications ba ON ba.user_id = r.owner_id AND ba.status = 'Approved'
                WHERE r.user_id = :uid
                AND r.status IN ('pending', 'accepted', 'in_progress')
                ORDER BY r.id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':uid' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Mark a repair as consulted (owner has communicated with client).
     */
    public function markConsulted($repair_id, $owner_id, $notes = null) {
        $sql = "UPDATE {$this->table} SET consultation_status = 'consulted', consultation_notes = :notes 
                WHERE id = :rid AND owner_id = :oid AND status = 'accepted'";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':notes' => $notes,
            ':rid'   => $repair_id,
            ':oid'   => $owner_id
        ]);
    }

    /**
     * Get consultation status for a repair.
     */
    public function getConsultationStatus($repair_id) {
        $sql = "SELECT consultation_status, consultation_notes FROM {$this->table} WHERE id = :rid";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':rid' => $repair_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
