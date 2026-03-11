<?php
class Review {
    private $conn;
    private $table = "reviews";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Submit a review
    public function create($reviewer_id, $target_id, $target_type, $rating, $comment) {
        $query = "INSERT INTO " . $this->table . " (reviewer_id, target_id, target_type, rating, comment) VALUES (:reviewer_id, :target_id, :target_type, :rating, :comment)";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':reviewer_id', $reviewer_id);
        $stmt->bindParam(':target_id', $target_id);
        $stmt->bindParam(':target_type', $target_type);
        $stmt->bindParam(':rating', $rating);
        $stmt->bindParam(':comment', $comment);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Get reviews for a target
    public function getReviewsForTarget($target_id, $target_type) {
        $query = "SELECT r.*, u.full_name as reviewer_name, u.profile_picture as reviewer_image 
                  FROM " . $this->table . " r 
                  JOIN users u ON r.reviewer_id = u.id 
                  WHERE r.target_id = :target_id AND r.target_type = :target_type 
                  ORDER BY r.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':target_id', $target_id);
        $stmt->bindParam(':target_type', $target_type);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Get stats
    public function getStats($target_id, $target_type) {
        $query = "SELECT COUNT(*) as total_reviews, AVG(rating) as average_rating 
                  FROM " . $this->table . " 
                  WHERE target_id = :target_id AND target_type = :target_type";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':target_id', $target_id);
        $stmt->bindParam(':target_type', $target_type);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>
