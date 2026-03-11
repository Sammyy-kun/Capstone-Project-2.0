<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Model/BusinessApplication.php';
require_once __DIR__ . '/../Model/user.php';
require_once __DIR__ . '/../Model/notification.php';
require_once __DIR__ . '/../Config/session.php';

class BusinessController {
    private $db;
    private $bizModel;
    private $userModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->bizModel = new BusinessApplication($this->db);
        $this->userModel = new User($this->db);
        $this->notifModel = new Notification($this->db);
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? null;
        
        switch ($action) {
            case 'submit':
                $this->submitApplication();
                break;
            case 'list':
                $this->listApplications();
                break;
            case 'get':
                $this->getApplication();
                break;
            case 'approve':
                $this->updateStatus('Approved');
                break;
            case 'reject':
                $this->updateStatus('Rejected');
                break;
            case 'reapply':
                $this->reapplyApplication();
                break;
            case 'my_status':
                $this->getMyStatus();
                break;
            default:
                $this->jsonResponse(["status" => "error", "message" => "Invalid action"]);
                break;
        }
    }

    private function submitApplication() {
        $userId = $_SESSION['user_id'] ?? null;
        $data = $_POST;

        // Auto-Registration for Guests
        if (!$userId) {
            $username = trim($data['username'] ?? '');
            $password = $data['password'] ?? '';
            $firstName = trim($data['first_name'] ?? '');
            $lastName = trim($data['last_name'] ?? '');
            $fullName = $firstName . ' ' . $lastName;
            $email = trim($data['email'] ?? '');

            if (empty($username) || empty($password)) {
                $this->jsonResponse(["status" => "error", "message" => "Account credentials are required for new users."]);
                return;
            }

            if ($this->userModel->usernameExists($username)) {
                $this->jsonResponse(["status" => "error", "message" => "Username already taken. Please login or choose another."]);
                return;
            }

            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $role = 'owner'; // Force owner role for business apps
            
            if ($this->userModel->register($username, $fullName, $email, $hashed, $role)) {
                $user = $this->userModel->login($username);
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['username'] = $user['username'];
                $userId = $user['id'];
            } else {
                $this->jsonResponse(["status" => "error", "message" => "Failed to create user account."]);
                return;
            }
        }

        if (empty($data)) {
            $this->jsonResponse(["status" => "error", "message" => "No data provided."]);
            return;
        }

        $data['user_id'] = $userId;
        $data['status'] = 'Pending';
        
        // Remove UI specific and Auth specific fields from application data
        unset($data['agreed']);
        unset($data['certified']);
        unset($data['username']);
        unset($data['password']);

        // Handle File Uploads
        $uploadDir = __DIR__ . '/../Public/uploads/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileKeys = ['gov_id_file', 'business_permit', 'dti_registration'];
        foreach ($fileKeys as $key) {
            if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
                $filename = 'biz_' . $userId . '_' . $key . '_' . time() . '.' . $ext;
                $targetPath = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES[$key]['tmp_name'], $targetPath)) {
                    $data[$key] = 'Public/uploads/documents/' . $filename;
                }
            }
        }

        // Always ensure the applicant has the 'owner' role — covers both fresh guests
        // and existing 'user' (buyer) accounts who are upgrading to seller.
        $upgradeRoleSql = "UPDATE users SET role = 'owner' WHERE id = :uid AND role != 'admin'";
        $upgradeStmt = $this->db->prepare($upgradeRoleSql);
        $upgradeStmt->execute([':uid' => $userId]);
        // Sync session role so the redirect lands on the correct dashboard
        $_SESSION['role'] = 'owner';

        if ($this->bizModel->create($data)) {
            $this->jsonResponse(["status" => "success", "message" => "Application submitted successfully."]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Failed to submit application to database."]);
        }
    }

    private function listApplications() {
        $status = $_GET['status'] ?? null;
        $apps = $status ? $this->bizModel->listByStatus($status) : $this->bizModel->listAll();
        $this->jsonResponse(["status" => "success", "data" => $apps]);
    }

    private function getApplication() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->jsonResponse(["status" => "error", "message" => "Application ID required."]);
            return;
        }
        $app = $this->bizModel->findById($id);
        if ($app) {
            $this->jsonResponse(["status" => "success", "data" => $app]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Application not found."]);
        }
    }

    private function updateStatus($status) {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? null;

        if (!$id) {
            $this->jsonResponse(["status" => "error", "message" => "Application ID required."]);
            return;
        }

        $rejectionReason = null;
        if ($status === 'Rejected') {
            $reasons = $input['rejection_reasons'] ?? [];
            $note    = trim($input['rejection_note'] ?? '');
            $parts   = [];
            if (!empty($reasons)) {
                $parts[] = implode('; ', array_map('htmlspecialchars', $reasons));
            }
            if ($note !== '') {
                $parts[] = 'Note: ' . htmlspecialchars($note);
            }
            $rejectionReason = !empty($parts) ? implode(' | ', $parts) : null;
        }

        if ($this->bizModel->updateStatus($id, $status, $rejectionReason)) {
            // If approved, update the user table's business_name and status
            if ($status === 'Approved') {
                $app = $this->bizModel->findById($id);
                // Update user status to 'Approved' (active) and set business name
                $sql = "UPDATE users SET business_name = :biz, status = 'Approved' WHERE id = :uid";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([':biz' => $app['business_name'], ':uid' => $app['user_id']]);
                // Notify the owner about approval
                $this->notifModel->create(
                    $app['user_id'],
                    'Application Approved',
                    'Congratulations! Your business application has been approved. All merchant features are now unlocked.',
                    'success',
                    null
                );
            } elseif ($status === 'Rejected') {
                $app = $this->bizModel->findById($id);
                if ($app) {
                    // Notify the owner about rejection so the bell badge shows
                    $this->notifModel->create(
                        $app['user_id'],
                        'Application Rejected',
                        'Your business application was not approved. Please check the Application Review page for the reasons and resubmit.',
                        'error',
                        null
                    );
                }
            }
            $this->jsonResponse(["status" => "success", "message" => "Application $status successfully."]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Failed to update status."]);
        }
    }

    private function reapplyApplication() {
        requireLogin();
        $userId = $_SESSION['user_id'];

        $existing = $this->bizModel->findByUserId($userId);
        if (!$existing || $existing['status'] !== 'Rejected') {
            $this->jsonResponse(["status" => "error", "message" => "No rejected application found to reapply."]);
            return;
        }

        $data = $_POST;
        $data['status']           = 'Pending';
        $data['rejection_reason'] = null;
        unset($data['agreed'], $data['certified'], $data['username'], $data['password']);

        $uploadDir = __DIR__ . '/../Public/uploads/documents/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileKeys = ['gov_id_file', 'business_permit', 'dti_registration'];
        foreach ($fileKeys as $key) {
            if (isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
                $ext      = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
                $filename = 'biz_' . $userId . '_' . $key . '_' . time() . '.' . $ext;
                $targetPath = $uploadDir . $filename;
                if (move_uploaded_file($_FILES[$key]['tmp_name'], $targetPath)) {
                    $data[$key] = 'Public/uploads/documents/' . $filename;
                }
            }
        }

        if ($this->bizModel->updateById($existing['id'], $data)) {
            $this->jsonResponse(["status" => "success", "message" => "Application resubmitted successfully."]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Failed to resubmit application."]);
        }
    }

    private function getMyStatus() {
        requireLogin();
        $userId = $_SESSION['user_id'];
        $app = $this->bizModel->findByUserId($userId);
        $this->jsonResponse(["status" => "success", "data" => $app]);
    }

    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

$controller = new BusinessController();
try {
    $controller->handleRequest();
} catch (Throwable $e) {
    header('Content-Type: application/json');
    // Log the real error for debugging
    error_log('[business-controller] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    echo json_encode([
        'status'  => 'error',
        'message' => 'Database error: ' . $e->getMessage()
    ]);
    exit;
}

