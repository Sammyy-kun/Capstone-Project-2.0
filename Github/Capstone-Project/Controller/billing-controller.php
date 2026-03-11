<?php
require_once __DIR__ . '/../Config/database.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class BillingController {
    private $db;

    public function __construct() {
        ini_set('display_errors', 0);
        $database = new Database();
        $this->db = $database->connect();
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? null;

        switch ($action) {
            case 'create_invoice':
                $this->createInvoice();
                break;
            case 'get_invoice':
                $this->getInvoice();
                break;
            case 'my_invoices':
                $this->listMyInvoices();
                break;
            case 'pay':
                $this->processPayment();
                break;
            default:
                $this->jsonResponse(["status" => "error", "message" => "Invalid action"]);
                break;
        }
    }

    public function createInvoice() {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'owner', 'technician'])) {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized."]);
             return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $repair_id = $input['repair_id'] ?? null;
        $amount = $input['amount'] ?? 0;
        
        if (!$repair_id || $amount <= 0) {
            $this->jsonResponse(["status" => "error", "message" => "Invalid input."]);
            return;
        }

        $sql = "INSERT INTO invoices (repair_id, total_amount) VALUES (:rid, :amt)";
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute([':rid' => $repair_id, ':amt' => $amount])) {
             $this->jsonResponse(["status" => "success", "message" => "Invoice created."]);
        } else {
             $this->jsonResponse(["status" => "error", "message" => "Failed to create invoice."]);
        }
    }

    public function getInvoice() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->jsonResponse(["status" => "error", "message" => "Missing ID."]);
            return;
        }

        $sql = "SELECT i.*, r.description, u.full_name as customer 
                FROM invoices i
                JOIN repairs r ON i.repair_id = r.id
                JOIN users u ON r.user_id = u.id
                WHERE i.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($invoice) {
            $this->jsonResponse(["status" => "success", "data" => $invoice]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Invoice not found."]);
        }
    }

    public function listMyInvoices() {
        if (!isset($_SESSION['user_id'])) {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized."]);
             return;
        }

        $sql = "SELECT i.*, r.description 
                FROM invoices i
                JOIN repairs r ON i.repair_id = r.id
                WHERE r.user_id = :uid
                ORDER BY i.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $_SESSION['user_id']]);
        $invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->jsonResponse(["status" => "success", "data" => $invoices]);
    }

    public function processPayment() {
        if (!isset($_SESSION['user_id'])) {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized."]);
             return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $invoice_id = $input['invoice_id'] ?? null;
        $amount = $input['amount'] ?? 0;
        $method = $input['method'] ?? 'cash';

        if (!$invoice_id || $amount <= 0) {
            $this->jsonResponse(["status" => "error", "message" => "Invalid input."]);
            return;
        }

        // Logic: Verify invoice amount, payment gateway simulation
        // For now, record payment and mark invoice paid.
        
        $this->db->beginTransaction();

        try {
            $sql = "INSERT INTO payments (invoice_id, user_id, amount, payment_method) VALUES (:iid, :uid, :amt, :meth)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':iid' => $invoice_id, ':uid' => $_SESSION['user_id'], ':amt' => $amount, ':meth' => $method]);

            $sql = "UPDATE invoices SET status = 'paid' WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':id' => $invoice_id]);

            $this->db->commit();
            $this->jsonResponse(["status" => "success", "message" => "Payment successful."]);
        } catch (Exception $e) {
            $this->db->rollBack();
            $this->jsonResponse(["status" => "error", "message" => "Payment failed."]);
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
    $controller = new BillingController();
    $controller->handleRequest();
}
?>
