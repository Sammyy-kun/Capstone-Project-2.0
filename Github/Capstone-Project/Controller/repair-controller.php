<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Model/repair.php';
require_once __DIR__ . '/../Model/technician.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class RepairController {
    private $repairModel;
    private $techModel;
    private $db;

    public function __construct() {
        ini_set('display_errors', 0);
        $database = new Database();
        $this->db = $database->connect();
        $this->repairModel = new Repair($this->db);
        $this->techModel   = new Technician($this->db);
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? null;

        switch ($action) {
            case 'request':
                $this->requestRepair();
                break;
            case 'list_my_repairs': // For users
                $this->listUserRepairs();
                break;
            case 'list_owner_repairs': // For owners
                $this->listOwnerRepairs();
                break;
            case 'update_status': // Owner: accept / reject / complete
                $this->updateRepairStatus();
                break;
            case 'list_technicians': // Owner: get technicians for dispatch modal
                $this->listTechnicians();
                break;
            case 'dispatch_technician': // Owner: assign technician to accepted repair
                $this->dispatchTechnician();
                break;
            case 'mark_consulted': // Owner: mark consultation with client as completed
                $this->markConsulted();
                break;
            case 'get_queue_positions': // User: get queue position for active repairs
                $this->getQueuePositions();
                break;
            default:
                $this->jsonResponse(["status" => "error", "message" => "Invalid action"]);
                break;
        }
    }

    public function requestRepair() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return;
        }

        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(["status" => "error", "message" => "Unauthorized. Please login."]);
            return;
        }

        $input = $_POST;
        if (empty($input)) {
            $json  = file_get_contents('php://input');
            $input = json_decode($json, true);
        }

        $user_id        = $_SESSION['user_id'];
        $shop_code      = trim($input['shop_code'] ?? '');
        $description    = trim($input['description'] ?? '');
        $schedule_date  = $input['schedule_date'] ?? null;
        $service_type   = $input['service_type'] ?? 'walk_in';
        $issue_category = $input['issue_category'] ?? 'other';
        $delivery_fee   = floatval($input['delivery_fee'] ?? 0);
        $delivery_method = $input['delivery_payment_method'] ?? null;

        if (empty($shop_code) || empty($description)) {
            $this->jsonResponse(["status" => "error", "message" => "Shop ID and Description are required."]);
            return;
        }

        // Validate delivery fee for home service
        if ($service_type === 'home_service' && $delivery_fee > 0 && empty($delivery_method)) {
            $this->jsonResponse(["status" => "error", "message" => "Please select a delivery payment method."]);
            return;
        }

        require_once __DIR__ . '/../Model/user.php';
        $userModel = new User($this->db);
        $shop      = $userModel->findByShopCode($shop_code);

        if (!$shop) {
            $this->jsonResponse(["status" => "error", "message" => "Can't find the shop id: " . htmlspecialchars($shop_code)]);
            return;
        }
        $owner_id = $shop['id'];

        if ($this->repairModel->requestRepair($user_id, $owner_id, $description, $schedule_date, $service_type, $issue_category, $delivery_fee, $delivery_method)) {
            $this->jsonResponse(["status" => "success", "message" => "Repair requested successfully."]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Failed to request repair. System error."]);
        }
    }

    public function listUserRepairs() {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(["status" => "error", "message" => "Unauthorized."]);
            return;
        }
        $repairs = $this->repairModel->getUserRepairs($_SESSION['user_id']);
        $this->jsonResponse(["status" => "success", "data" => $repairs]);
    }

    public function listOwnerRepairs() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
            $this->jsonResponse(["status" => "error", "message" => "Unauthorized. Owners only."]);
            return;
        }
        $repairs = $this->repairModel->getOwnerRepairs($_SESSION['user_id']);
        $this->jsonResponse(["status" => "success", "data" => $repairs]);
    }

    /**
     * Owner can accept, reject, or mark a repair as completed.
     * POST body: { repair_id, status }
     */
    public function updateRepairStatus() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
            $this->jsonResponse(["status" => "error", "message" => "Unauthorized. Owners only."]);
            return;
        }

        $input     = json_decode(file_get_contents('php://input'), true);
        $repair_id = $input['repair_id'] ?? null;
        $status    = $input['status'] ?? null;

        if (!$repair_id || !$status) {
            $this->jsonResponse(["status" => "error", "message" => "repair_id and status are required."]);
            return;
        }

        if ($this->repairModel->updateStatus($repair_id, $status, $_SESSION['user_id'])) {
            $this->jsonResponse(["status" => "success", "message" => "Repair status updated to '{$status}'."]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Failed to update status. Invalid status or unauthorized."]);
        }
    }

    /**
     * Returns technicians associated with the logged-in owner.
     */
    public function listTechnicians() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
            $this->jsonResponse(["status" => "error", "message" => "Unauthorized. Owners only."]);
            return;
        }

        // Get technicians that belong to this owner's shop via ownership table.
        // Falling back to all technicians if ownership table doesn't link them.
        $sql = "SELECT tp.id, tp.user_id, tp.specialization, tp.status,
                       u.full_name, u.email
                FROM technician_profiles tp
                JOIN users u ON tp.user_id = u.id
                JOIN ownership o ON o.technician_id = tp.user_id
                WHERE o.owner_id = :oid";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':oid' => $_SESSION['user_id']]);
            $techs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // If no ownership links, fall back to all technicians
            if (empty($techs)) {
                $techs = $this->techModel->getAllTechnicians();
            }
        } catch (\Exception $e) {
            // ownership table may not exist — fall back
            $techs = $this->techModel->getAllTechnicians();
        }

        $this->jsonResponse(["status" => "success", "data" => $techs]);
    }

    /**
     * Assign a technician to an accepted repair and flip its status to in_progress.
     * POST body: { repair_id, technician_id }
     */
    public function dispatchTechnician() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
            $this->jsonResponse(["status" => "error", "message" => "Unauthorized. Owners only."]);
            return;
        }

        $input        = json_decode(file_get_contents('php://input'), true);
        $repair_id    = $input['repair_id']    ?? null;
        $technician_id = $input['technician_id'] ?? null;

        if (!$repair_id || !$technician_id) {
            $this->jsonResponse(["status" => "error", "message" => "repair_id and technician_id are required."]);
            return;
        }

        // Check consultation status before dispatching
        $consultStatus = $this->repairModel->getConsultationStatus($repair_id);
        if (!$consultStatus || $consultStatus['consultation_status'] !== 'consulted') {
            $this->jsonResponse(["status" => "error", "message" => "Please complete the client consultation before dispatching a technician."]);
            return;
        }

        // Insert assignment record
        $assigned = $this->techModel->assignJob($repair_id, $technician_id);
        if (!$assigned) {
            $this->jsonResponse(["status" => "error", "message" => "Failed to assign technician. They may already be assigned."]);
            return;
        }

        // Flip repair status to in_progress
        $this->repairModel->updateStatus($repair_id, 'in_progress', $_SESSION['user_id']);

        $this->jsonResponse(["status" => "success", "message" => "Technician dispatched successfully."]);
    }

    /**
     * Owner marks that consultation with the client has been completed.
     * POST body: { repair_id, notes (optional) }
     */
    public function markConsulted() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
            $this->jsonResponse(["status" => "error", "message" => "Unauthorized. Owners only."]);
            return;
        }

        $input     = json_decode(file_get_contents('php://input'), true);
        $repair_id = $input['repair_id'] ?? null;
        $notes     = trim($input['notes'] ?? '');

        if (!$repair_id) {
            $this->jsonResponse(["status" => "error", "message" => "repair_id is required."]);
            return;
        }

        if ($this->repairModel->markConsulted($repair_id, $_SESSION['user_id'], $notes ?: null)) {
            $this->jsonResponse(["status" => "success", "message" => "Client consultation marked as completed."]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Failed to update consultation status. Repair must be in 'accepted' state."]);
        }
    }

    public function getQueuePositions() {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(["status" => "error", "message" => "Unauthorized."]);
            return;
        }
        $positions = $this->repairModel->getQueuePositionsForUser($_SESSION['user_id']);
        $this->jsonResponse(["status" => "success", "data" => $positions]);
    }

    private function jsonResponse($data) {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new RepairController();
    $controller->handleRequest();
}
?>
