<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Model/user.php';
require_once __DIR__ . '/../Config/mail.php';

class AuthController {
    public function __construct() {
        // Prevent PHP warnings from breaking JSON
        ini_set('display_errors', 0);
        
        $database = new Database();
        $this->db = $database->connect();
        $this->userModel = new User($this->db);
    }

    public function register() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return;
        }

        // Support both JSON and Form input
        $input = $_POST;
        if (empty($input)) {
            $json = file_get_contents('php://input');
            $input = json_decode($json, true);
        }

        $username = trim($input['username'] ?? '');
        $full_name = trim($input['full_name'] ?? '');
        $email = trim($input['email'] ?? '');
        $password = $input['password'] ?? '';
        
        // Default role is 'user' if not specified (or enforce logic)
        $role = $input['role'] ?? 'user';
        $business_name = trim($input['business_name'] ?? '');

        // USERNAME VALIDATION
        if (empty($username) || !preg_match("/^[a-zA-Z0-9_]{3,20}$/", $username)) {
            $this->jsonResponse(["status" => "error", "message" => "Username must be 3-20 chars, alphanumeric or underscores."]);
            return;
        }

        // NAME VALIDATION
        if (!preg_match("/^[a-zA-Z\s\.\-]+$/", $full_name)) {
            $this->jsonResponse(["status" => "error", "message" => "Name cannot contain numbers or special characters (except . and -)."]);
            return;
        }

        // EMAIL VALIDATION
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(["status" => "error", "message" => "Invalid email format."]);
            return;
        }

        // PASSWORD VALIDATION
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
            $this->jsonResponse(["status" => "error", "message" => "Password must include min 8 chars, uppercase, lowercase, and a number."]);
            return;
        }

        // CHECK IF USERNAME EXISTS
        if ($this->userModel->usernameExists($username)) {
             $this->jsonResponse(["status" => "error", "message" => "Username already taken."]);
             return;
        }

        $hashed = password_hash($password, PASSWORD_BCRYPT);

        $shop_code = null;
        if ($role === 'owner') {
             // Generate unique Shop ID
             $shop_code = 'SHOP-' . strtoupper(substr(md5(uniqid()), 0, 6)); 
        }

        // Register with Username and Shop Code
        if ($this->userModel->register($username, $full_name, $email, $hashed, $role, $business_name, 'individual', $shop_code)) {
            // Note: Email is not unique anymore, so multiple accounts can share it.
            $subject = $role === 'owner' ? "Owner Registration" : "Registration Successful";
            $msg = "Welcome $username! Your account is now active.";
            if ($shop_code) {
                $msg .= " Your Shop ID is: <b>$shop_code</b>. Share this with customers so they can book repairs.";
            }
            sendEmail($email, $subject, $msg);
            $this->jsonResponse(["status" => "success", "message" => "User registered successfully!" . ($shop_code ? " Shop ID: $shop_code" : "")]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Registration failed."]);
        }
    }

    public function login() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return;
        }

        $input = $_POST;
        if (empty($input)) {
            $json = file_get_contents('php://input');
            $input = json_decode($json, true);
        }

        $username = trim($input['username'] ?? ''); 
        $password = $input['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->jsonResponse(["status" => "error", "message" => "Username and password required."]);
            return;
        }

        $user = $this->userModel->login($username);

        if ($user && password_verify($password, $user['password'])) {
            // Start session if not already started
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['username'] = $user['username'];

            $this->jsonResponse(["status" => "success", "message" => "Login successful", "role" => $user['role']]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Invalid username or password."]);
        }
    }

    private function jsonResponse($data) {
        // Clear any previous output (whitespace, warnings, BOMs)
        if (ob_get_length()) {
            ob_clean();
        }
        
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header("Location: " . BASE_URL . "View/User/Home/index.php");
        exit;
    }

    public function socialLogin() {
        // Simple mock implementation for prototype
        $email = $_POST['email'] ?? '';
        $name = $_POST['full_name'] ?? 'Social User';
        $provider = $_POST['provider'] ?? 'Google';
        
        if (empty($email)) {
            $this->jsonResponse(["status" => "error", "message" => "Email required."]);
            return;
        }

        // Check if user exists by email
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            // Register new user automatically
            // Create a dummy username from email
            $username = explode('@', $email)[0] . '_' . rand(100, 999);
            // Ensure unique
            while($this->userModel->usernameExists($username)) {
                $username = explode('@', $email)[0] . '_' . rand(1000, 9999);
            }
            
            $password = bin2hex(random_bytes(8)); // Random password
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            
            $this->userModel->register($username, $name, $email, $hashed, 'user', null, 'individual', null);
            
            // Fetch again
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
        }

        // Login
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['username'] = $user['username'];

        $this->jsonResponse(["status" => "success", "message" => "Logged in with $provider", "role" => $user['role']]);
    }
}

// Handle Request
if (isset($_GET['action'])) {
    $controller = new AuthController();
    $action = $_GET['action'];

    switch ($action) {
        case 'register':
            $controller->register();
            break;
        case 'login':
            $controller->login();
            break;
        case 'social_login': // Add this
            $controller->socialLogin();
            break;
        case 'logout':
            $controller->logout();
            break;
        default:
            header("HTTP/1.0 400 Bad Request");
            echo json_encode(["status" => "error", "message" => "Invalid action"]);
            exit;
    }
}

