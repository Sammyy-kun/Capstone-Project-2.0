<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Model/technician.php';
require_once __DIR__ . '/../Model/user.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class TechController {
    private $techModel;
    private $userModel;
    private $db;

    public function __construct() {
        ini_set('display_errors', 0);
        $database = new Database();
        $this->db = $database->connect();
        $this->techModel = new Technician($this->db);
        $this->userModel = new User($this->db);
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? null;
        
        switch ($action) {
            case 'list_all':
                $this->listAll();
                break;
            case 'create':
                $this->createTechnician();
                break;
            case 'assign_job':
                $this->assignJob();
                break;
            case 'my_jobs':
                $this->listMyJobs();
                break;
            case 'update_status':
                $this->updateStatus();
                break;
            case 'get_profile':
                $this->getTechProfile();
                break;
            case 'update_profile':
                $this->updateTechProfile();
                break;
            default:
                $this->jsonResponse(["status" => "error", "message" => "Invalid action"]);
                break;
        }
    }

    public function getTechProfile() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technician') {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized."]);
             return;
        }

        $stmt = $this->db->prepare("SELECT t.*, u.full_name, u.email, u.phone, u.profile_picture FROM technician_profiles t JOIN users u ON t.user_id = u.id WHERE t.user_id = :uid");
        $stmt->execute([':uid' => $_SESSION['user_id']]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($profile) {
            $this->jsonResponse(["status" => "success", "data" => $profile]);
        } else {
            // If strictly no profile yet (shouldn't happen if created by admin), return basic user info
             $user = $this->userModel->getProfile(); // Assuming this exists or we fetch manually
             $this->jsonResponse(["status" => "success", "data" => ["user_id" => $_SESSION['user_id'], "specialization" => "", "bio" => ""]]);
        }
    }

    public function updateTechProfile() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technician') {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized."]);
             return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $specialization = $input['specialization'] ?? '';
        $bio = $input['bio'] ?? '';
        $status = $input['status'] ?? 'offline';

        if ($this->techModel->saveProfile($_SESSION['user_id'], $specialization, $bio, $status)) {
             $this->jsonResponse(["status" => "success", "message" => "Profile updated."]);
        } else {
             $this->jsonResponse(["status" => "error", "message" => "Update failed."]);
        }
    }

    public function updateStatus() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technician') {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized."]);
             return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $status = $input['status'] ?? null;
        
        if (!$status || !in_array($status, ['active', 'busy', 'offline'])) {
            $this->jsonResponse(["status" => "error", "message" => "Invalid status"]);
            return;
        }

        // Get tech ID (reusing lookup logic, should factor out)
        $stmt = $this->db->prepare("SELECT id FROM technician_profiles WHERE user_id = :uid");
        $stmt->execute([':uid' => $_SESSION['user_id']]);
        $tech = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tech) {
            // Update status
            $stmt = $this->db->prepare("UPDATE technician_profiles SET status = :status WHERE id = :id");
            if ($stmt->execute([':status' => $status, ':id' => $tech['id']])) {
                 $this->jsonResponse(["status" => "success", "message" => "Status updated."]);
            } else {
                 $this->jsonResponse(["status" => "error", "message" => "Update failed."]);
            }
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Profile not found."]);
        }
    }

    public function listAll() {
        // Only Admin or Owner
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'owner'])) {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized."]);
             return;
        }
        $techs = $this->techModel->getAllTechnicians();
        $this->jsonResponse(["status" => "success", "data" => $techs]);
    }

    public function createTechnician() {
        // Admin only
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized."]);
             return;
        }

        $input = $_POST;
        if (empty($input)) {
             $json = file_get_contents('php://input');
             $input = json_decode($json, true);
        }

        $username = trim($input['username'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        $full_name = trim($input['full_name'] ?? '');
        $specialization = $input['specialization'] ?? '';

        if (empty($username) || empty($email) || empty($password)) {
             $this->jsonResponse(["status" => "error", "message" => "Required fields missing."]);
             return;
        }

        // Register User first
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        if ($this->userModel->register($username, $full_name, $email, $hashed, 'technician')) {
            // Get ID
            $user = $this->userModel->login($username); // Assuming login returns user details by username
            $this->techModel->saveProfile($user['id'], $specialization, "Newly created technician.");
            $this->jsonResponse(["status" => "success", "message" => "Technician created successfully."]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Failed to create user account."]);
        }
    }

    public function assignJob() {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'owner'])) {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized."]);
             return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $repair_id = $input['repair_id'] ?? null;
        $tech_id = $input['technician_id'] ?? null;

        if (!$repair_id || !$tech_id) {
            $this->jsonResponse(["status" => "error", "message" => "Missing IDs."]);
            return;
        }

        if ($this->techModel->assignJob($repair_id, $tech_id)) {
            $this->jsonResponse(["status" => "success", "message" => "Job assigned."]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Assignment failed."]);
        }
    }

    public function listMyJobs() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'technician') {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized."]);
             return;
        }
        
        // Need to get tech profile ID from user ID
        // For now, getAllTechnicians joins users, so we can find it.
        // Ideally, we store technician_id in session or look it up.
        // Let's look it up via DB for now. (Improving Model later)
        
        // Quick lookup
        $stmt = $this->db->prepare("SELECT id FROM technician_profiles WHERE user_id = :uid");
        $stmt->execute([':uid' => $_SESSION['user_id']]);
        $tech = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($tech) {
            $jobs = $this->techModel->getAssignedJobs($tech['id']);
            $this->jsonResponse(["status" => "success", "data" => $jobs]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Profile not found."]);
        }
    }

    private function jsonResponse($data) {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new TechController();
    $controller->handleRequest();
}
?>
