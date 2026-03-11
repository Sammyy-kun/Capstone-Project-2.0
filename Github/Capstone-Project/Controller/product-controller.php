<?php
require_once __DIR__ . '/../Config/database.php';
require_once __DIR__ . '/../Model/product.php';
require_once __DIR__ . '/../Model/serial_number.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class ProductController {
    private $productModel;
    private $serialModel;
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->productModel = new Product($this->db);
        $this->serialModel = new SerialNumber($this->db);
    }

    public function handleRequest() {
        $action = $_GET['action'] ?? null;
        
        switch ($action) {
            case 'add':
                $this->addProduct();
                break;
            case 'get':
                $this->getProductDetails();
                break;
            case 'list_owner':
                $this->listOwnerProducts();
                break;
            case 'list_all':
                $this->listAllProducts();
                break;
            case 'add_serials':
                 $this->addSerials();
                 break;
            case 'list_serials':
                 $this->listSerials();
                 break;
            case 'edit':
                 $this->editProduct();
                 break;
            case 'delete':
                 $this->deleteProduct();
                 break;
            case 'list_store':
                 $this->listStoreProducts();
                 break;
            case 'store_info':
                 $this->getStoreInfo();
                 break;
            case 'list_by_store':
                 $this->listByStore();
                 break;
            default:
                $this->jsonResponse(["status" => "error", "message" => "Invalid action"]);
                break;
        }
    }

    public function addProduct() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->jsonResponse(["status" => "error", "message" => "Invalid request method"]);
            return;
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized. Only owners can add products."]);
             return;
        }

        $owner_id = $_SESSION['user_id'];
        $name = trim($_POST['product-name'] ?? '');
        $desc = trim($_POST['product-description'] ?? '');
        $price = $_POST['price'] ?? 0;
        $qty = $_POST['stock'] ?? 0;
        $category = trim($_POST['category'] ?? '');

        if (empty($name) || $price <= 0 || $qty < 0) {
             $this->jsonResponse(["status" => "error", "message" => "Invalid input data."]);
             return;
        }

        // Handle up to 3 image uploads
        $uploadDir = __DIR__ . '/../Public/uploads/products/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        $uploadedImages = [null, null, null];
        for ($i = 0; $i < 3; $i++) {
            $fileKey = 'image_' . ($i + 1);
            if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION);
                $filename = 'prod_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $targetFile = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetFile)) {
                    $uploadedImages[$i] = '../../../Public/uploads/products/' . $filename;
                }
            }
        }

        if ($this->productModel->addProduct($owner_id, $name, $desc, $price, $qty, $uploadedImages[0], $uploadedImages[1], $uploadedImages[2], $category)) {
            $this->jsonResponse(["status" => "success", "message" => "Product added successfully."]);
        } else {
             $this->jsonResponse(["status" => "error", "message" => "Failed to add product."]);
        }
    }

    public function editProduct() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->jsonResponse(["status" => "error", "message" => "Invalid request method"]);
            return;
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized. Only owners can edit products."]);
             return;
        }

        $owner_id = $_SESSION['user_id'];
        $product_id = $_POST['id'] ?? 0;
        $name = trim($_POST['product-name'] ?? '');
        $desc = trim($_POST['product-description'] ?? '');
        $price = $_POST['price'] ?? 0;
        $qty = $_POST['stock'] ?? 0;
        $category = trim($_POST['category'] ?? '');

        if ($product_id <= 0 || empty($name) || $price <= 0 || $qty < 0) {
             $this->jsonResponse(["status" => "error", "message" => "Invalid input data."]);
             return;
        }

        // Handle up to 3 image uploads
        $uploadDir = __DIR__ . '/../Public/uploads/products/';
        if (!is_dir($uploadDir)) {
            @mkdir($uploadDir, 0777, true);
        }

        $uploadedImages = [null, null, null];
        for ($i = 0; $i < 3; $i++) {
            $fileKey = 'image_' . ($i + 1);
            if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES[$fileKey]['name'], PATHINFO_EXTENSION);
                $filename = 'prod_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
                $targetFile = $uploadDir . $filename;
                
                if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetFile)) {
                    $uploadedImages[$i] = '../../../Public/uploads/products/' . $filename;
                }
            }
        }

        if ($this->productModel->editProduct($product_id, $owner_id, $name, $desc, $price, $qty, $uploadedImages[0], $uploadedImages[1], $uploadedImages[2], $category)) {
            $this->jsonResponse(["status" => "success", "message" => "Product updated successfully."]);
        } else {
             $this->jsonResponse(["status" => "error", "message" => "Failed to update product."]);
        }
    }

    public function getProductDetails() {
        $id = $_GET['id'] ?? 0;
        if ($id <= 0) {
            $this->jsonResponse(["status" => "error", "message" => "Invalid product ID"]);
            return;
        }
        $product = $this->productModel->getProductById($id);
        if ($product) {
            $this->jsonResponse(["status" => "success", "data" => $product]);
        } else {
            $this->jsonResponse(["status" => "error", "message" => "Product not found"]);
        }
    }

    public function deleteProduct() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            $this->jsonResponse(["status" => "error", "message" => "Invalid request method"]);
            return;
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized. Only owners can delete products."]);
             return;
        }

        $owner_id = $_SESSION['user_id'];
        $input = $_POST;
        if (empty($input)) {
             $json = file_get_contents('php://input');
             $input = json_decode($json, true);
        }

        $product_id = $input['id'] ?? 0;

        if ($product_id <= 0) {
            $this->jsonResponse(["status" => "error", "message" => "Invalid product ID."]);
            return;
        }

        if ($this->productModel->deleteProduct($product_id, $owner_id)) {
            $this->jsonResponse(["status" => "success", "message" => "Product deleted successfully."]);
        } else {
             $this->jsonResponse(["status" => "error", "message" => "Failed to delete product."]);
        }
    }

    public function listOwnerProducts() {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(["status" => "error", "message" => "Unauthorized"]);
            return;
        }
        $products = $this->productModel->getOwnerProducts($_SESSION['user_id']);
        $this->jsonResponse(["status" => "success", "data" => $products]);
    }

    public function listAllProducts() {
        $products = $this->productModel->getAllProducts();
        $this->jsonResponse(["status" => "success", "data" => $products]);
    }

    public function addSerials() {
        if ($_SERVER["REQUEST_METHOD"] !== "POST") {
            return;
        }

        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
             $this->jsonResponse(["status" => "error", "message" => "Unauthorized."]);
             return;
        }

        $input = $_POST;
        if (empty($input)) {
             $json = file_get_contents('php://input');
             $input = json_decode($json, true);
        }

        $product_id = $input['product_id'] ?? 0;
        $serials_str = $input['serials'] ?? '';
        
        // Serials can be comma-separated or an array
        $serials = is_array($serials_str) ? $serials_str : explode(',', $serials_str);

        if ($product_id <= 0 || empty($serials)) {
            $this->jsonResponse(["status" => "error", "message" => "Invalid product or serials."]);
            return;
        }

        $count = $this->serialModel->addSerials($product_id, $serials);
        $this->jsonResponse(["status" => "success", "message" => "$count serial numbers added."]);
    }

    public function listSerials() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
            $this->jsonResponse(["status" => "error", "message" => "Unauthorized"]);
            return;
        }
        $product_id = $_GET['product_id'] ?? 0;
        $serials = $this->serialModel->getByProduct($product_id);
        $this->jsonResponse(["status" => "success", "data" => $serials]);
    }

    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function listStoreProducts() {
        $owner_id = intval($_GET['owner_id'] ?? 0);
        if (!$owner_id) {
            $this->jsonResponse(["status" => "error", "message" => "Invalid store"]);
            return;
        }
        $products = $this->productModel->getStoreProducts($owner_id);
        $this->jsonResponse(["status" => "success", "data" => $products]);
    }

    public function getStoreInfo() {
        $owner_id = intval($_GET['owner_id'] ?? 0);
        if (!$owner_id) {
            $this->jsonResponse(["status" => "error", "message" => "Invalid store"]);
            return;
        }
        $info = $this->productModel->getStoreOwnerInfo($owner_id);
        if (!$info) {
            $this->jsonResponse(["status" => "error", "message" => "Store not found"]);
            return;
        }
        $this->jsonResponse(["status" => "success", "data" => $info]);
    }

    public function listByStore() {
        $limit    = intval($_GET['limit']    ?? 5);
        $offset   = intval($_GET['offset']   ?? 0);
        $search   = trim($_GET['search']     ?? '');
        $category = trim($_GET['category']   ?? '');
        $stores = $this->productModel->getStoresWithProducts($limit, $offset, $search, $category);
        $total  = $this->productModel->countStoresWithProducts($search, $category);
        $this->jsonResponse(["status" => "success", "data" => $stores, "total" => $total]);
    }
}

// Simple router if accessed directly
if (basename(__FILE__) == basename($_SERVER['SCRIPT_FILENAME'])) {
    $controller = new ProductController();
    $controller->handleRequest();
}
?>
