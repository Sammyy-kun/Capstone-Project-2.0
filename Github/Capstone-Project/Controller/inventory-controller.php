<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Model/supplier.php';
require_once __DIR__ . '/../Model/spare_part.php';
require_once __DIR__ . '/../Config/session.php';

class InventoryController {
    private $supplierModel;
    private $partModel;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->supplierModel = new Supplier($this->db);
        $this->partModel = new SparePart($this->db);
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? null;
        
        // Ensure only owners or admins can access inventory
        requireLogin();
        // Add robust role check here if needed (e.g., if ($_SESSION['role'] !== 'owner') ...)

        switch ($action) {
            case 'list_parts':
                $this->listParts();
                break;
            case 'add_part':
                $this->addPart();
                break;
            case 'list_suppliers':
                $this->listSuppliers();
                break;
            case 'add_supplier':
                $this->addSupplier();
                break;
            case 'check_low_stock':
                $this->checkLowStock();
                break;
            default:
                $this->jsonResponse(["status" => "error", "message" => "Invalid action"]);
                break;
        }
    }

    public function listParts() {
        $parts = $this->partModel->getAll();
        $this->jsonResponse(["status" => "success", "data" => $parts]);
    }

    public function addPart() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;
        
        $input = $_POST;
        // Handle empty supplier_id
        $supplier_id = !empty($input['supplier_id']) ? $input['supplier_id'] : null;

        // Structured or Manual?
        // If the form sends components, generate. If it sends a raw number (fallback), use it.
        $partNumber = $input['number'] ?? '';

        if (!empty($input['category']) && !empty($input['type']) && !empty($input['spec'])) {
             $version = !empty($input['version']) ? $input['version'] : 'A';
             $partNumber = $this->partModel->generateNextPartNumber(
                 $input['category'], 
                 $input['type'], 
                 $input['spec'],
                 $version
             );
        }

        if ($this->partModel->add($input['name'], $partNumber, $input['stock'], $input['price'], $supplier_id)) {
            $this->jsonResponse(["status" => "success", "message" => "Part added. SKU: " . $partNumber]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Failed to add part."]);
        }
    }

    public function listSuppliers() {
        $suppliers = $this->supplierModel->getAll();
        $this->jsonResponse(["status" => "success", "data" => $suppliers]);
    }

    public function addSupplier() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") return;

        $input = $_POST;
        if ($this->supplierModel->add($input['name'], $input['contact'], $input['address'])) {
            $this->jsonResponse(["status" => "success", "message" => "Supplier added."]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Failed to add supplier."]);
        }
    }

    public function checkLowStock() {
        $parts = $this->partModel->checkLowStock();
        $this->jsonResponse(["status" => "success", "data" => $parts]);
    }

    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new InventoryController();
    $controller->handleRequest();
}
?>
