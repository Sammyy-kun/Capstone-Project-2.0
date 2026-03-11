<?php
class Notification {
    private $conn;
    private $table = "notifications";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new notification
    public function create($user_id, $title, $message, $type = 'info', $target_url = null) {
        $sql = "INSERT INTO {$this->table} (user_id, title, message, type, target_url) VALUES (:user_id, :title, :message, :type, :target_url)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':user_id' => $user_id,
            ':title' => $title,
            ':message' => $message,
            ':type' => $type,
            ':target_url' => $target_url
        ]);
    }

    // Get all notifications for a user (ordered by newest first)
    public function getByUser($user_id) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get unread count
    public function getUnreadCount($user_id) {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE user_id = :user_id AND is_read = 0";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['count'];
    }

    // Mark a single notification as read
    public function markAsRead($id, $user_id) {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id, ':user_id' => $user_id]);
    }

    // Mark all notifications as read for a user
    public function markAllAsRead($user_id) {
        $sql = "UPDATE {$this->table} SET is_read = 1 WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':user_id' => $user_id]);
    }

    // Delete a notification
    public function delete($id, $user_id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id, ':user_id' => $user_id]);
    }
}
?>
