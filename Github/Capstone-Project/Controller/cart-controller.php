<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Config/session.php';
require_once __DIR__ . '/../Model/cart.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class CartController {
    private $cartModel;

    public function __construct() {
        $database = new Database();
        $db = $database->connect();
        $this->cartModel = new Cart($db);
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? null;
        switch ($action) {
            case 'get':
                $this->getCart();
                break;
            case 'add':
                $this->addToCart();
                break;
            case 'update':
                $this->updateCart();
                break;
            case 'remove':
                $this->removeFromCart();
                break;
            case 'clear':
                $this->clearCart();
                break;
            case 'count':
                $this->getCartCount();
                break;
            default:
                $this->jsonResponse(['status' => 'error', 'message' => 'Invalid action']);
                break;
        }
    }

    private function requireUser() {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Please log in to continue.'], 401);
            exit;
        }
        return $_SESSION['user_id'];
    }

    private function getCart() {
        $userId = $this->requireUser();
        try {
            $items = $this->cartModel->getCartItems($userId);
            $total = array_reduce($items, fn($carry, $item) => $carry + ($item['price'] * $item['quantity']), 0);
            $this->jsonResponse(['status' => 'success', 'data' => $items, 'total' => $total]);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function getCartCount() {
        $userId = $this->requireUser();
        try {
            $count = $this->cartModel->getCartCount($userId);
            $this->jsonResponse(['status' => 'success', 'count' => (int)$count]);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function addToCart() {
        $userId = $this->requireUser();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }
        $productId = intval($_POST['product_id'] ?? 0);
        $quantity  = intval($_POST['quantity'] ?? 1);
        if (!$productId || $quantity < 1) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid product or quantity']);
            return;
        }
        try {
            $this->cartModel->addToCart($userId, $productId, $quantity);
            $count = $this->cartModel->getCartCount($userId);
            $this->jsonResponse(['status' => 'success', 'message' => 'Added to cart!', 'count' => (int)$count]);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function updateCart() {
        $userId = $this->requireUser();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }
        $cartId   = intval($_POST['cart_id'] ?? 0);
        $quantity = intval($_POST['quantity'] ?? 1);
        if (!$cartId) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid cart item']);
            return;
        }
        try {
            $this->cartModel->updateQuantity($cartId, $userId, $quantity);
            $this->jsonResponse(['status' => 'success', 'message' => 'Cart updated']);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function removeFromCart() {
        $userId = $this->requireUser();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid request method']);
            return;
        }
        $cartId = intval($_POST['cart_id'] ?? 0);
        if (!$cartId) {
            $this->jsonResponse(['status' => 'error', 'message' => 'Invalid cart item']);
            return;
        }
        try {
            $this->cartModel->removeFromCart($cartId, $userId);
            $count = $this->cartModel->getCartCount($userId);
            $this->jsonResponse(['status' => 'success', 'message' => 'Item removed', 'count' => (int)$count]);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function clearCart() {
        $userId = $this->requireUser();
        try {
            $this->cartModel->clearCart($userId);
            $this->jsonResponse(['status' => 'success', 'message' => 'Cart cleared']);
        } catch (Exception $e) {
            $this->jsonResponse(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function jsonResponse($data, $code = 200) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

$controller = new CartController();
$controller->handleRequest();
?>
