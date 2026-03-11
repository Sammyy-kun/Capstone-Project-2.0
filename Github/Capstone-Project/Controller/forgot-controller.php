<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Config/mail.php';

class ForgotController {
    private $db;
    private $conn;

    public function __construct() {
        ini_set('display_errors', 0);
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function sendCode() {
        $email = $_POST['email'] ?? '';
        
        // Basic email check
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid email format.']);
        }

        // Check if email exists in users table (optional security measure, preventing enumeration? 
        // User asked to "make sure it sends", so we should check existence first).
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        if (!$stmt->fetch()) {
             // For security, usually we don't say "Email not found", but for this project generic is fine or specific if requested. 
             // Let's say "If an account exists..." or just fail.
             $this->jsonResponse(['status' => 'error', 'message' => 'Email not found.']);
        }

        // Generate 6-digit code
        $code = rand(100000, 999999);
        $token = bin2hex(random_bytes(16)); // Secure token for verification logic if needed, but here we just use code.
        
        // Delete old codes
        $stmt = $this->conn->prepare("DELETE FROM password_resets WHERE email = :email");
        $stmt->execute([':email' => $email]);

        // Insert new code
        // Use MySQL timestamp for consistency
        $stmt = $this->conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (:email, :code, DATE_ADD(NOW(), INTERVAL 15 MINUTE))");
        if ($stmt->execute([':email' => $email, ':code' => $code])) {
            if (sendEmail($email, "Password Reset Code", "Your verification code is: <b>$code</b>")) {
                $this->jsonResponse(['status' => 'success', 'message' => 'Verification code sent to email.']);
            } else {
                $this->jsonResponse(['status' => 'error', 'message' => 'Failed to send email.']);
            }
        } else {
            $this->jsonResponse(['status' => 'error', 'message' => 'Database error.']);
        }
    }

    public function verifyCode() {
        $email = $_POST['email'] ?? '';
        $code = trim($_POST['code'] ?? '');

        $stmt = $this->conn->prepare("SELECT * FROM password_resets WHERE email = :email");
        $stmt->execute([':email' => $email]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$record) {
             $this->jsonResponse(['status' => 'error', 'message' => 'No reset code found for this email. Request a new one.']);
             return;
        }

        if ($record['token'] != $code) {
             $this->jsonResponse(['status' => 'error', 'message' => "Invalid code."]);
             return;
        }
        
        // Check Expiry
        $stmt = $this->conn->prepare("SELECT * FROM password_resets WHERE email = :email AND token = :code AND expires_at > NOW()");
        $stmt->execute([':email' => $email, ':code' => $code]);

        if ($stmt->fetch()) {
            // Success! Generate token
            $resetToken = bin2hex(random_bytes(32));
            $stmt = $this->conn->prepare("UPDATE password_resets SET token = :token, expires_at = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE email = :email");
            $stmt->execute([':token' => $resetToken, ':email' => $email]);

            // Fetch associated users
            $stmt = $this->conn->prepare("SELECT id, username, role, full_name, profile_picture FROM users WHERE email = :email");
            $stmt->execute([':email' => $email]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $this->jsonResponse([
                'status' => 'success', 
                'message' => 'Code verified.', 
                'token' => $resetToken,
                'accounts' => $users 
            ]);
        } else {
            $this->jsonResponse(['status' => 'error', 'message' => 'Code has expired.']);
        }
    }

    public function resetPassword() {
        $email = $_POST['email'] ?? '';
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';
        $user_id = $_POST['user_id'] ?? ''; // Added user_id param

        // Verify token
        $stmt = $this->conn->prepare("SELECT * FROM password_resets WHERE email = :email AND token = :token AND expires_at > NOW()");
        $stmt->execute([':email' => $email, ':token' => $token]);
        
        if (!$stmt->fetch()) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid session or token expired.']);
        }

        // Update Password for Specific User
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        
        // Ensure the user_id actually belongs to this email (security check)
        $stmt = $this->conn->prepare("UPDATE users SET password = :pass WHERE id = :id AND email = :email");
        if ($stmt->execute([':pass' => $hashed, ':id' => $user_id, ':email' => $email])) {
            
            // Allow multiple resets? Or delete?
            // If strictly one-time use per verification flow, we delete.
            // But if user wants to reset multiple accounts sequentially?
            // Safe bet is to delete to prevent replay attacks. User can verify code again if needed? 
            // Better: Delete the token.
            $stmt = $this->conn->prepare("DELETE FROM password_resets WHERE email = :email");
            $stmt->execute([':email' => $email]);
            
            $this->jsonResponse(['status' => 'success', 'message' => 'Password has been reset.']);
        } else {
            // If no rows affected (e.g. user_id mismatch), return error
            if ($stmt->rowCount() == 0) {
                 $this->jsonResponse(['status' => 'error', 'message' => 'Failed. Account validation error.']);
            }
            $this->jsonResponse(['status' => 'error', 'message' => 'Failed to update password.']);
        }
    }

    private function jsonResponse($data) {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

if (isset($_GET['action'])) {
    $controller = new ForgotController();
    $action = $_GET['action'];

    switch ($action) {
        case 'send_code':
            $controller->sendCode();
            break;
        case 'verify_code':
            $controller->verifyCode();
            break;
        case 'reset_password':
            $controller->resetPassword();
            break;
    }
}
?>
