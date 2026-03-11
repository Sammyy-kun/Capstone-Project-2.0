<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Config/session.php';
require_once __DIR__ . '/../Model/order.php';
require_once __DIR__ . '/../Model/notification.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class CheckoutController {
    private $db;
    private $orderModel;
    private $notifModel;

    public function __construct() {
        ini_set('display_errors', 0);
        $database = new Database();
        $this->db = $database->connect();
        $this->orderModel = new Order($this->db);
        $this->notifModel = new Notification($this->db);
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? null;
        switch ($action) {
            case 'get_items':        $this->getCheckoutItems();  break;
            case 'get_addresses':    $this->getAddresses();      break;
            case 'save_address':     $this->saveAddress();       break;
            case 'get_delivery_fee': $this->getDeliveryFee();    break;
            case 'place_order':      $this->placeOrder();        break;
            case 'get_order':        $this->getOrder();          break;
            case 'get_orders':       $this->getOrders();         break;
            default:
                $this->jsonResponse(['status' => 'error', 'message' => 'Invalid action']);
        }
    }

    private function requireUser() {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Please log in to continue.'], 401);
            exit;
        }
        return (int)$_SESSION['user_id'];
    }

    // ─── GET: Fetch selected cart items for checkout display ───────────────────
    private function getCheckoutItems() {
        $userId = $this->requireUser();
        $ids    = trim($_GET['ids'] ?? '');

        if (empty($ids)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'No items selected.']);
            return;
        }

        $idArray = array_values(array_filter(array_map('intval', explode(',', $ids))));
        if (empty($idArray)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid item IDs.']);
            return;
        }

        $placeholders = implode(',', $idArray);
        $sql = "SELECT c.id AS cart_item_id, c.product_id, c.quantity,
                       p.product_name, p.price, p.image_url, p.qty AS stock, p.owner_id,
                       COALESCE(ba.business_name, u.full_name) AS company_name,
                       ba.latitude AS biz_lat, ba.longitude AS biz_lng
                FROM cart_items c
                JOIN products p ON p.id = c.product_id
                JOIN users u ON u.id = p.owner_id
                LEFT JOIN business_applications ba ON ba.user_id = p.owner_id AND ba.status = 'Approved'
                WHERE c.id IN ({$placeholders}) AND c.user_id = :uid";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($items)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'No valid items found for your account.']);
            return;
        }

        $totalQty = array_sum(array_column($items, 'quantity'));
        if ($totalQty > 200) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Cart limit exceeded (max 200 items).']);
            return;
        }

        $this->jsonResponse(['status' => 'success', 'data' => $items, 'total_qty' => $totalQty]);
    }

    // ─── GET: Return user's saved addresses ────────────────────────────────────
    private function getAddresses() {
        $userId = $this->requireUser();
        $stmt   = $this->db->prepare("SELECT * FROM user_addresses WHERE user_id = :uid ORDER BY is_default DESC, created_at DESC");
        $stmt->execute([':uid' => $userId]);
        $this->jsonResponse(['status' => 'success', 'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)]);
    }

    // ─── POST: Save a new delivery address ─────────────────────────────────────
    private function saveAddress() {
        $userId = $this->requireUser();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        $label     = htmlspecialchars(strip_tags(trim($_POST['label']     ?? 'Home')));
        $name      = htmlspecialchars(strip_tags(trim($_POST['recipient_name'] ?? '')));
        $phone     = htmlspecialchars(strip_tags(trim($_POST['phone']     ?? '')));
        $street    = htmlspecialchars(strip_tags(trim($_POST['street']    ?? '')));
        $barangay  = htmlspecialchars(strip_tags(trim($_POST['barangay']  ?? '')));
        $city      = htmlspecialchars(strip_tags(trim($_POST['city']      ?? '')));
        $province  = htmlspecialchars(strip_tags(trim($_POST['province']  ?? '')));
        $zip       = htmlspecialchars(strip_tags(trim($_POST['zip_code']  ?? '')));
        $makeDef   = !empty($_POST['is_default']) ? 1 : 0;

        if (empty($name) || empty($phone) || empty($street) || empty($city)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Recipient name, phone, street, and city are required.']);
            return;
        }

        if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid phone number format.']);
            return;
        }

        if ($makeDef) {
            $this->db->prepare("UPDATE user_addresses SET is_default = 0 WHERE user_id = :uid")
                     ->execute([':uid' => $userId]);
        }

        $sql  = "INSERT INTO user_addresses (user_id, label, recipient_name, phone, street, barangay, city, province, zip_code, is_default)
                 VALUES (:uid, :lbl, :name, :phone, :street, :bgy, :city, :prov, :zip, :def)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':uid' => $userId, ':lbl' => $label,    ':name'  => $name,
            ':phone' => $phone, ':street' => $street, ':bgy' => $barangay,
            ':city'  => $city,  ':prov'  => $province, ':zip' => $zip,
            ':def'   => $makeDef,
        ]);
        $newId = (int)$this->db->lastInsertId();

        $s2 = $this->db->prepare("SELECT * FROM user_addresses WHERE id = :id");
        $s2->execute([':id' => $newId]);
        $this->jsonResponse(['status' => 'success', 'message' => 'Address saved.', 'data' => $s2->fetch(PDO::FETCH_ASSOC)]);
    }

    // ─── GET: Calculate delivery fee based on distance ─────────────────────────
    private function getDeliveryFee() {
        $this->requireUser();
        $userId    = (int)$_SESSION['user_id'];
        $addressId = intval($_GET['address_id'] ?? 0);
        $ownerId   = intval($_GET['owner_id']   ?? 0);

        if (!$addressId) {
            $this->jsonResponse(['status' => 'success', 'distance_km' => null, 'delivery_fee' => 80]);
            return;
        }

        $stmt = $this->db->prepare("SELECT latitude, longitude FROM user_addresses WHERE id = :id AND user_id = :uid");
        $stmt->execute([':id' => $addressId, ':uid' => $userId]);
        $addr = $stmt->fetch(PDO::FETCH_ASSOC);

        $merchantLat = $merchantLng = null;
        if ($ownerId) {
            $s2 = $this->db->prepare("SELECT latitude, longitude FROM business_applications WHERE user_id = :uid AND status = 'Approved' LIMIT 1");
            $s2->execute([':uid' => $ownerId]);
            $biz = $s2->fetch(PDO::FETCH_ASSOC);
            $merchantLat = isset($biz['latitude'])  ? (float)$biz['latitude']  : null;
            $merchantLng = isset($biz['longitude']) ? (float)$biz['longitude'] : null;
        }

        $distanceKm = null;
        if ($addr && $addr['latitude'] && $addr['longitude'] && $merchantLat && $merchantLng) {
            $distanceKm = $this->haversine(
                (float)$addr['latitude'], (float)$addr['longitude'],
                $merchantLat, $merchantLng
            );
        }

        $this->jsonResponse([
            'status'       => 'success',
            'distance_km'  => $distanceKm !== null ? round($distanceKm, 2) : null,
            'delivery_fee' => $this->feeFromDistance($distanceKm),
        ]);
    }

    private function haversine(float $lat1, float $lng1, float $lat2, float $lng2): float {
        $R    = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a    = sin($dLat / 2) ** 2 + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
        return $R * 2 * asin(sqrt($a));
    }

    private function feeFromDistance(?float $km): float {
        if ($km === null) return 80;
        if ($km <= 1)     return 40;
        if ($km <= 3)     return 60;
        if ($km <= 5)     return 80;
        if ($km <= 10)    return 120;
        if ($km <= 20)    return 180;
        return 250;
    }

    // ─── POST: Place the order ─────────────────────────────────────────────────
    private function placeOrder() {
        $userId = $this->requireUser();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid request method.']);
            return;
        }

        $raw   = file_get_contents('php://input');
        $input = !empty($raw) ? json_decode($raw, true) : $_POST;
        if (!is_array($input)) $input = [];

        $addressId      = intval($input['address_id'] ?? 0);
        $deliveryMethod = in_array($input['delivery_method'] ?? '', ['lalamove', 'pickup'])
                          ? $input['delivery_method'] : 'lalamove';
        $paymentMethod  = in_array($input['payment_method'] ?? '', ['cod', 'online'])
                          ? $input['payment_method'] : '';
        $notes          = htmlspecialchars(strip_tags(substr(trim($input['notes'] ?? ''), 0, 300)));
        $deliveryFee    = $deliveryMethod === 'pickup' ? 0.0 : (float)($input['delivery_fee'] ?? 0);
        $distanceKm     = isset($input['distance_km']) && $input['distance_km'] !== null
                          ? (float)$input['distance_km'] : null;
        $cartItemIds    = array_values(array_filter(array_map('intval', (array)($input['cart_item_ids'] ?? []))));

        if (empty($cartItemIds)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'No items selected for checkout.']);
            return;
        }
        if ($deliveryMethod !== 'pickup' && !$addressId) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Please select a delivery address.']);
            return;
        }
        if (empty($paymentMethod)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Please choose a payment method.']);
            return;
        }

        // Re-fetch items from DB (server-side ownership check)
        $placeholders = implode(',', $cartItemIds);
        $sql = "SELECT c.id AS cart_item_id, c.product_id, c.quantity,
                       p.product_name, p.price, p.owner_id,
                       COALESCE(ba.business_name, u.full_name) AS company_name
                FROM cart_items c
                JOIN products p ON p.id = c.product_id
                JOIN users u ON u.id = p.owner_id
                LEFT JOIN business_applications ba ON ba.user_id = p.owner_id AND ba.status = 'Approved'
                WHERE c.id IN ({$placeholders}) AND c.user_id = :uid";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':uid' => $userId]);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($items)) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Items not found or do not belong to your account.']);
            return;
        }

        try {
            $orderId = $this->orderModel->placeOrder(
                $userId, $addressId ?: null, $deliveryMethod,
                $paymentMethod, $deliveryFee, $notes, $distanceKm, $items
            );

            // Notify each merchant
            $ownerIds = array_unique(array_filter(array_column($items, 'owner_id')));
            $clientStmt = $this->db->prepare("SELECT full_name FROM users WHERE id = :uid");
            $clientStmt->execute([':uid' => $userId]);
            $client     = $clientStmt->fetch(PDO::FETCH_ASSOC);
            $clientName = $client['full_name'] ?? 'A customer';

            $itemsTotal  = array_sum(array_map(fn($i) => (float)$i['price'] * (int)$i['quantity'], $items));
            $totalAmount = $itemsTotal + $deliveryFee;

            foreach ($ownerIds as $ownerId) {
                $this->notifModel->create(
                    (int)$ownerId,
                    'New Order Received',
                    "Order #{$orderId} from {$clientName} — ₱" . number_format($totalAmount, 2),
                    'success',
                    null
                );
            }

            $this->jsonResponse(['status' => 'success', 'message' => 'Order placed successfully!', 'order_id' => $orderId]);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    // ─── GET: Single order (for tracking page) ─────────────────────────────────
    private function getOrder() {
        $userId  = $this->requireUser();
        $orderId = intval($_GET['order_id'] ?? 0);
        if (!$orderId) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Missing order ID.']);
            return;
        }
        $order = $this->orderModel->getOrder($orderId, $userId);
        if (!$order) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Order not found.']);
            return;
        }
        $items = $this->orderModel->getOrderItems($orderId);
        $this->jsonResponse(['status' => 'success', 'data' => $order, 'items' => $items]);
    }

    // ─── GET: Order history list ────────────────────────────────────────────────
    private function getOrders() {
        $userId = $this->requireUser();
        $orders = $this->orderModel->getUserOrders($userId);
        $this->jsonResponse(['status' => 'success', 'data' => $orders]);
    }

    private function jsonResponse(array $data, int $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

if (basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new CheckoutController();
    $controller->handleRequest();
}
?>
