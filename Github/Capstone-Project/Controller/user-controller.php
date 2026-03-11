<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Model/user.php';
require_once __DIR__ . '/../Model/ownership.php';
require_once __DIR__ . '/../Model/BusinessApplication.php';
require_once __DIR__ . '/../Model/notification.php';
require_once __DIR__ . '/../Config/session.php'; // Ensures session is started

class UserController {
    private $userModel;
    private $ownershipModel;
    private $db;

    public function __construct() {
        ini_set('display_errors', 0);
        $database = new Database();
        $this->db = $database->connect();
        $this->userModel = new User($this->db);
        $this->ownershipModel = new Ownership($this->db);
        $this->bizModel = new BusinessApplication($this->db);
        $this->notifModel = new Notification($this->db);
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? null;
        
        switch ($action) {
            case 'add_appliance':
                $this->addAppliance();
                break;
            case 'my_appliances':
                $this->listMyAppliances();
                break;
            case 'update_profile':
                $this->updateProfile();
                break;
            case 'get_profile':
                $this->getProfile();
                break;
            case 'list_shops':
                $this->listShops();
                break;
            case 'approve_shop':
                $this->updateShopStatus('Approved');
                break;
            case 'reject_shop':
                $this->updateShopStatus('Rejected');
                break;
            case 'list_all_users':
                $this->listAllUsers();
                break;
            case 'admin_update_user':
                $this->adminUpdateUser();
                break;
            case 'get_dashboard_metrics':
                $this->getDashboardMetrics();
                break;
            case 'get_sales_analytics':
                $this->getSalesAnalytics();
                break;
            case 'update_status':
                $this->updateStatus();
                break;
            default:
                $this->jsonResponse(["status" => "error", "message" => "Invalid action"]);
                break;
        }
    }

    public function listShops() {
        $sql = "SELECT id, full_name, business_name, email, shop_code, status FROM users WHERE role = 'owner'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $shops = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $this->jsonResponse(["status" => "success", "data" => $shops]);
    }

    public function updateShopStatus($status) {
        requireLogin();
        // Ideally verify admin role here
        
        $input = json_decode(file_get_contents('php://input'), true);
        $userId = $input['user_id'] ?? null;

        if (!$userId) {
            $this->jsonResponse(["status" => "error", "message" => "User ID required."]);
            return;
        }

        if ($this->userModel->updateStatus($userId, $status)) {
            // Also keep business_applications in sync so Application Review shows the correct status
            $syncSql = "UPDATE business_applications SET status = :status WHERE user_id = :uid";
            $syncStmt = $this->db->prepare($syncSql);
            $syncStmt->execute([':status' => $status, ':uid' => $userId]);

            // Notify the owner so the bell badge persists across logins
            if ($status === 'Approved') {
                $this->notifModel->create(
                    $userId,
                    'Application Approved',
                    'Congratulations! Your business application has been approved. All merchant features are now unlocked.',
                    'success',
                    null
                );
            } elseif ($status === 'Rejected') {
                $this->notifModel->create(
                    $userId,
                    'Application Rejected',
                    'Your business application was not approved. Please check the Application Review page for the reasons and resubmit.',
                    'error',
                    null
                );
            }

            $this->jsonResponse(["status" => "success", "message" => "Shop " . strtolower($status) . " successfully."]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Failed to update status."]);
        }
    }

    public function listAllUsers() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 15;
        $offset = ($page - 1) * $limit;

        // Get total count for pagination
        $countSql = "SELECT COUNT(*) FROM users";
        $totalUsers = $this->db->query($countSql)->fetchColumn();
        $totalPages = ceil($totalUsers / $limit);

        // Get paginated users
        $sql = "SELECT id, username, full_name, email, role, created_at FROM users ORDER BY id DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->jsonResponse([
            "status" => "success", 
            "data" => $users,
            "pagination" => [
                "current_page" => $page,
                "total_pages" => $totalPages,
                "total_items" => $totalUsers,
                "limit" => $limit
            ]
        ]);
    }

    public function adminUpdateUser() {
        requireLogin();
        // Verify admin role here
        
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (empty($input['id']) || empty($input['username']) || empty($input['full_name']) || empty($input['email']) || empty($input['role'])) {
            $this->jsonResponse(["status" => "error", "message" => "All fields are required."]);
            return;
        }

        if ($this->userModel->updateUser($input['id'], $input['username'], $input['full_name'], $input['email'], $input['role'])) {
             $this->jsonResponse(["status" => "success", "message" => "User updated successfully."]);
        } else {
             $this->jsonResponse(["status" => "error", "message" => "Failed to update user."]);
        }
    }

    public function getDashboardMetrics() {
        require_once __DIR__ . '/../Config/session.php';
        $role = $_SESSION['role'] ?? 'user';
        $userId = $_SESSION['user_id'] ?? 0;

        if ($role === 'admin') {
            $usersCount = $this->db->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn();
            $ownersCount = $this->db->query("SELECT COUNT(*) FROM users WHERE role='owner'")->fetchColumn();
            $techsCount = $this->db->query("SELECT COUNT(*) FROM users WHERE role='technician'")->fetchColumn();
            
            $this->jsonResponse([
                "status" => "success", 
                "data" => [
                    "users" => $usersCount,
                    "owners" => $ownersCount,
                    "technicians" => $techsCount
                ]
            ]);
        } else if ($role === 'owner') {
            $productCount = $this->db->prepare("SELECT COUNT(*) FROM products WHERE owner_id = ?");
            $productCount->execute([$userId]);
            
            $repairCount = $this->db->prepare("SELECT COUNT(*) FROM repairs WHERE owner_id = ?");
            $repairCount->execute([$userId]);
            
            $revenueStmt = $this->db->prepare("SELECT SUM(p.amount) FROM payments p JOIN repairs r ON p.repair_id = r.id WHERE r.owner_id = ? AND p.status = 'paid'");
            $revenueStmt->execute([$userId]);
            $revenue = $revenueStmt->fetchColumn() ?: 0;

            $this->jsonResponse([
                "status" => "success",
                "data" => [
                    "products" => $productCount->fetchColumn(),
                    "repairs" => $repairCount->fetchColumn(),
                    "revenue" => "₱" . number_format($revenue, 2)
                ]
            ]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Unauthorized"]);
        }
    }

    public function getSalesAnalytics() {
        require_once __DIR__ . '/../Config/session.php';
        $userId = $_SESSION['user_id'] ?? 0;
        
        // Mock data if no real data exists for demonstration, or real query
        $sql = "SELECT DATE_FORMAT(p.created_at, '%b') as month, SUM(p.amount) as total 
                FROM payments p 
                JOIN repairs r ON p.repair_id = r.id 
                WHERE r.owner_id = :owner_id AND p.status = 'paid' 
                AND p.created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
                GROUP BY DATE_FORMAT(p.created_at, '%b')
                ORDER BY p.created_at ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':owner_id' => $userId]);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // If no real data, return the last 6 months with 0 revenue — accurate, not fake
        if (empty($data)) {
            $months = [];
            for ($i = 5; $i >= 0; $i--) {
                $months[] = [
                    "month" => date('M', strtotime("-$i months")),
                    "total" => 0
                ];
            }
            $data = $months;
        }

        $this->jsonResponse(["status" => "success", "data" => $data]);
    }

    public function addAppliance() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return;
        }

        requireLogin();

        $input = $_POST;
        if (empty($input)) {
             $json = file_get_contents('php://input');
             $input = json_decode($json, true);
        }

        $user_id = $_SESSION['user_id'];
        $brand = trim($input['brand'] ?? '');
        $model = trim($input['model'] ?? '');
        $serial = trim($input['serial_number'] ?? '');
        $purchase_date = $input['purchase_date'] ?? date('Y-m-d');

        if (empty($brand) || empty($model)) {
            $this->jsonResponse(["status" => "error", "message" => "Brand and Model are required."]);
            return;
        }

        if ($this->ownershipModel->addRecord($user_id, $brand, $model, $serial, $purchase_date)) {
            $this->jsonResponse(["status" => "success", "message" => "Appliance registered successfully."]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Failed to register appliance."]);
        }
    }

    public function listMyAppliances() {
        requireLogin();
        $records = $this->ownershipModel->getUserRecords($_SESSION['user_id']);
        $this->jsonResponse(["status" => "success", "data" => $records]);
    }

    public function getProfile() {
        requireLogin();
        $user_id = $_SESSION['user_id'];
        
        $sql = "SELECT id, username, full_name, email, role, business_name, address, phone, profile_picture FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user) {
            // Fix broken relative paths by stripping ../
            if (!empty($user['profile_picture'])) {
                $cleanPath = preg_replace('/(\.\.\/)+/', '/', $user['profile_picture']);
                // Ensure it starts with the correct base path if it doesn't already
                if (strpos($cleanPath, '/Capstone-Project') !== 0) {
                    $cleanPath = '/GitHub/Capstone-Project' . $cleanPath;
                }
                $user['profile_picture'] = $cleanPath;
            }
            $this->jsonResponse(["status" => "success", "data" => $user]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "User not found."]);
        }
    }

    public function updateProfile() {
        // Prevent any previous output from breaking JSON
        ob_start();
        ini_set('display_errors', 0);
        error_reporting(E_ALL); // Log errors, don't display them

        try {
            requireLogin();
            
            // Handle file upload separately or alongside fields
            $input = $_POST;
            if (empty($input)) {
                 $json = file_get_contents('php://input');
                 $input = json_decode($json, true);
            }

            $user_id = $_SESSION['user_id'];
            $full_name = trim($input['full_name'] ?? '');
            $email = trim($input['email'] ?? '');
            $business = trim($input['business_name'] ?? '');
            $phone = trim($input['phone'] ?? '');
            $address = trim($input['address'] ?? '');
            $password = $input['password'] ?? '';

            if (empty($full_name) || empty($email)) {
                $this->jsonResponse(["status" => "error", "message" => "Name and Email are required."]);
                return;
            }

            // Build Update Query
            $sql = "UPDATE users SET full_name = :name, email = :email, business_name = :biz, phone = :phone, address = :addr";
            $params = [
                ':name' => $full_name,
                ':email' => $email,
                ':biz' => $business,
                ':phone' => $phone,
                ':addr' => $address,
                ':id' => $user_id
            ];

            if (!empty($password)) {
                $sql .= ", password = :pass";
                $params[':pass'] = password_hash($password, PASSWORD_BCRYPT);
            }

            // Handle Profile Picture Upload
            if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['profile_picture']['tmp_name'];
                $fileName = $_FILES['profile_picture']['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp', 'avif');
                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../Public/uploads/profiles/';
                    
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0755, true);
                    }

                    $dest_path = $uploadFileDir . $newFileName;

                    if(move_uploaded_file($fileTmpPath, $dest_path)) {
                        $sql .= ", profile_picture = :pic";
                        $params[':pic'] = '../../../Public/uploads/profiles/' . $newFileName;
                    }
                } else {
                    $this->jsonResponse(["status" => "error", "message" => "Invalid file type. Allowed: jpg, png, webp, avif"]);
                    return;
                }
            } elseif (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] !== UPLOAD_ERR_NO_FILE) {
                 // Check for other upload errors (like size limit)
                 $uploadError = $_FILES['profile_picture']['error'];
                 $this->jsonResponse(["status" => "error", "message" => "Upload error code: " . $uploadError]);
                 return;
            }

            $sql .= " WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            
            if ($stmt->execute($params)) {
                $_SESSION['full_name'] = $full_name;
                $this->jsonResponse(["status" => "success", "message" => "Profile updated successfully.", "image" => $params[':pic'] ?? null]);
            } else {
                $errorInfo = $stmt->errorInfo();
                $this->jsonResponse(["status" => "error", "message" => "SQL Error: " . $errorInfo[2]]);
            }
        
        } catch (Throwable $e) {
            // Catch ANY error (Fatal or Exception) and return JSON
            $this->jsonResponse(["status" => "error", "message" => "Server Code Error: " . $e->getMessage()]);
        }
    }

    private function jsonResponse($data) {
        // Clear buffer before sending JSON to ensure clean output
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new UserController();
    $controller->handleRequest();
}
?>
