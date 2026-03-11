<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Model/review.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class ReviewController {
    private $reviewModel;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->reviewModel = new Review($this->db);
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? null;

        switch ($action) {
            case 'submit_review':
                $this->submitReview();
                break;
            case 'get_reviews':
                $this->getReviews();
                break;
            case 'get_stats':
                $this->getStats();
                break;
            default:
                echo json_encode(["status" => "error", "message" => "Invalid action"]);
                break;
        }
    }

    public function submitReview() {
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(["status" => "error", "message" => "Unauthorized"]);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        
        if ($this->reviewModel->create($_SESSION['user_id'], $input['target_id'], $input['target_type'], $input['rating'], $input['comment'])) {
            echo json_encode(["status" => "success", "message" => "Review submitted successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Failed to submit review"]);
        }
    }

    public function getReviews() {
        $target_id = $_GET['target_id'] ?? null;
        $target_type = $_GET['target_type'] ?? null;
        
        // If getting for self (Technician dashboard)
        if (!$target_id && isset($_SESSION['role']) && $_SESSION['role'] === 'technician') {
             // Look up tech ID
             $stmt = $this->db->prepare("SELECT id FROM technician_profiles WHERE user_id = :uid");
             $stmt->execute([':uid' => $_SESSION['user_id']]);
             $tech = $stmt->fetch(PDO::FETCH_ASSOC);
             if ($tech) {
                 $target_id = $tech['id'];
                 $target_type = 'technician';
             }
        }

        if ($target_id && $target_type) {
            $reviews = $this->reviewModel->getReviewsForTarget($target_id, $target_type);
            echo json_encode(["status" => "success", "data" => $reviews]);
        } else {
            echo json_encode(["status" => "error", "message" => "Missing parameters"]);
        }
    }

    public function getStats() {
        $target_id = $_GET['target_id'] ?? null;
        $target_type = $_GET['target_type'] ?? null;

        // If getting for self (Technician dashboard)
        if (!$target_id && isset($_SESSION['role']) && $_SESSION['role'] === 'technician') {
             // Look up tech ID
             $stmt = $this->db->prepare("SELECT id FROM technician_profiles WHERE user_id = :uid");
             $stmt->execute([':uid' => $_SESSION['user_id']]);
             $tech = $stmt->fetch(PDO::FETCH_ASSOC);
             if ($tech) {
                 $target_id = $tech['id'];
                 $target_type = 'technician';
             }
        }

        if ($target_id && $target_type) {
            $stats = $this->reviewModel->getStats($target_id, $target_type);
            echo json_encode(["status" => "success", "data" => $stats]);
        } else {
            echo json_encode(["status" => "error", "message" => "Missing parameters"]);
        }
    }
}

$controller = new ReviewController();
$controller->handleRequest();
?>
