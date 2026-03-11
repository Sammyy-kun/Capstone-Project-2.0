<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Model/technician.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class ScheduleController {
    private $db;
    private $techModel;

    public function __construct() {
        ini_set('display_errors', 0);
        $database = new Database();
        $this->db = $database->connect();
        $this->techModel = new Technician($this->db);
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? null;

        switch ($action) {
            case 'get_schedule':
                $this->getSchedule();
                break;
            case 'create_appointment':
                $this->createAppointment();
                break;
            default:
                $this->jsonResponse(["status" => "error", "message" => "Invalid action"]);
                break;
        }
    }

    public function getSchedule() {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'owner', 'technician'])) {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized."]);
             return;
        }

        $date = $_GET['date'] ?? date('Y-m-d');
        
        $sql = "SELECT a.*, r.description, u.full_name as customer, u.address, t.full_name as technician, r.priority
                FROM appointments a
                JOIN repairs r ON a.repair_id = r.id
                JOIN users u ON r.user_id = u.id
                JOIN technician_profiles tp ON a.technician_id = tp.id
                JOIN users t ON tp.user_id = t.id
                WHERE a.appointment_date = :date
                ORDER BY a.time_slot ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':date' => $date]);
        $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $this->jsonResponse(["status" => "success", "data" => $appointments]);
    }

    public function createAppointment() {
        if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'owner'])) {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized."]);
             return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $repair_id = $input['repair_id'] ?? null;
        $technician_id = $input['technician_id'] ?? null;
        $date = $input['date'] ?? null;
        $time = $input['time'] ?? null;

        if (!$repair_id || !$technician_id || !$date || !$time) {
            $this->jsonResponse(["status" => "error", "message" => "Missing fields."]);
            return;
        }

        // Check availability (Naive check)
        $sql = "SELECT id FROM appointments WHERE technician_id = :tid AND appointment_date = :date AND time_slot = :time";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':tid' => $technician_id, ':date' => $date, ':time' => $time]);
        if ($stmt->fetch()) {
            $this->jsonResponse(["status" => "error", "message" => "Technician already booked at this time."]);
            return;
        }

        // Create
        $sql = "INSERT INTO appointments (repair_id, technician_id, appointment_date, time_slot) VALUES (:rid, :tid, :date, :time)";
        $stmt = $this->db->prepare($sql);
        if ($stmt->execute([':rid' => $repair_id, ':tid' => $technician_id, ':date' => $date, ':time' => $time])) {
            // Also update repair status and assign?
            // For now just schedule.
             $this->jsonResponse(["status" => "success", "message" => "Appointment scheduled."]);
        } else {
             $this->jsonResponse(["status" => "error", "message" => "Failed to schedule."]);
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
    $controller = new ScheduleController();
    $controller->handleRequest();
}
?>
