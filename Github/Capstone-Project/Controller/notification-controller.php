<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Model/notification.php';

class NotificationController {
    private $db;
    private $notificationModel;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->notificationModel = new Notification($this->db);
    }

    public function fetchNotifications() {
        $this->verifyAuth();
        $user_id = $_SESSION['user_id'];
        
        $notifications = $this->notificationModel->getByUser($user_id);
        $unread_count = $this->notificationModel->getUnreadCount($user_id);
        
        $this->jsonResponse(['status' => 'success', 'data' => $notifications, 'unread_count' => $unread_count]);
    }

    public function markRead() {
        $this->verifyAuth();
        $id = $_POST['id'] ?? null;
        $user_id = $_SESSION['user_id'];

        if (!$id) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Notification ID required']);
        }

        if ($this->notificationModel->markAsRead($id, $user_id)) {
            $this->jsonResponse(['status' => 'success', 'message' => 'Marked as read']);
        } else {
            $this->jsonResponse(['status' => 'error', 'message' => 'Failed to update']);
        }
    }

    public function markAllRead() {
        $this->verifyAuth();
        $user_id = $_SESSION['user_id'];

        if ($this->notificationModel->markAllAsRead($user_id)) {
            $this->jsonResponse(['status' => 'success', 'message' => 'All marked as read']);
        } else {
            $this->jsonResponse(['status' => 'error', 'message' => 'Failed to update']);
        }
    }

    public function delete() {
        $this->verifyAuth();
        $id = $_POST['id'] ?? null;
        $user_id = $_SESSION['user_id'];

        if (!$id) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Notification ID required']);
        }

        if ($this->notificationModel->delete($id, $user_id)) {
            $this->jsonResponse(['status' => 'success', 'message' => 'Notification removed']);
        } else {
            $this->jsonResponse(['status' => 'error', 'message' => 'Failed to remove']);
        }
    }
    
    // For testing/debugging purposes, allow creating a notification via API
    public function create() {
        $this->verifyAuth();
        $user_id = $_SESSION['user_id'];
        $title = $_POST['title'] ?? 'Notification';
        $message = $_POST['message'] ?? '';
        $type = $_POST['type'] ?? 'info';
        $target_url = $_POST['target_url'] ?? null;
        
        if ($this->notificationModel->create($user_id, $title, $message, $type, $target_url)) {
            $this->jsonResponse(['status' => 'success', 'message' => 'Notification created']);
        } else {
             $this->jsonResponse(['status' => 'error', 'message' => 'Failed to create']);
        }
    }

    private function verifyAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Unauthorized']);
        }
    }

    private function jsonResponse($data) {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

// Handle Requests
if (isset($_GET['action'])) {
    $controller = new NotificationController();
    $action = $_GET['action'];

    switch ($action) {
        case 'fetch':
            $controller->fetchNotifications();
            break;
        case 'mark_read':
            $controller->markRead();
            break;
        case 'mark_all_read':
            $controller->markAllRead();
            break;
        case 'delete':
            $controller->delete();
            break;
        case 'create':
            $controller->create();
            break;
        default:
            echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
            exit;
    }
}
?>
