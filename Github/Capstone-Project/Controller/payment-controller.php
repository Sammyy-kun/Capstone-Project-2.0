<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Model/payment.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class PaymentController {
    private $paymentModel;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->paymentModel = new Payment($this->db);
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? null;
        
        switch ($action) {
            case 'pay':
                $this->processPayment();
                break;
            case 'history':
                $this->getPaymentHistory();
                break;
            default:
                $this->jsonResponse(["status" => "error", "message" => "Invalid action"]);
                break;
        }
    }

    public function processPayment() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return;
        }

        if (!isset($_SESSION['user_id'])) {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized."]);
             return;
        }

        $input = $_POST;
        if (empty($input)) {
             $json = file_get_contents('php://input');
             $input = json_decode($json, true);
        }

        $user_id = $_SESSION['user_id'];
        $repair_id = $input['repair_id'] ?? null;
        $amount = $input['amount'] ?? 0;
        $method = $input['method'] ?? '';

        if (!$repair_id || $amount <= 0 || empty($method)) {
            $this->jsonResponse(["status" => "error", "message" => "Invalid payment details."]);
            return;
        }

        if ($this->paymentModel->createPayment($repair_id, $user_id, $amount, $method)) {
            $this->jsonResponse(["status" => "success", "message" => "Payment initiated successfully."]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Payment failed."]);
        }
    }

    public function getPaymentHistory() {
        if (!isset($_SESSION['user_id'])) {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized."]);
             return;
        }

        $payments = $this->paymentModel->getUserPayments($_SESSION['user_id']);
        $this->jsonResponse(["status" => "success", "data" => $payments]);
    }

    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new PaymentController();
    $controller->handleRequest();
}
?>
