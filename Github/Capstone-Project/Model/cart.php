<?php
class Cart {
    private $conn;
    private $table = "cart_items";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getCartItems($user_id) {
        $sql = "SELECT c.*, p.product_name, p.price, p.image_url, p.qty as stock, p.owner_id 
                FROM {$this->table} c
                JOIN products p ON c.product_id = p.id
                WHERE c.user_id = :uid";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':uid' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCartCount($user_id) {
        $sql = "SELECT SUM(quantity) as count FROM {$this->table} WHERE user_id = :uid";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':uid' => $user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    public function addToCart($user_id, $product_id, $quantity = 1) {
        // Check if product exists and has stock
        $stmt = $this->conn->prepare("SELECT qty FROM products WHERE id = :pid");
        $stmt->execute([':pid' => $product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            throw new Exception("Product not found.");
        }
        
        // Check if item already in cart
        $stmt = $this->conn->prepare("SELECT id, quantity FROM {$this->table} WHERE user_id = :uid AND product_id = :pid");
        $stmt->execute([':uid' => $user_id, ':pid' => $product_id]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing) {
            $newQuantity = $existing['quantity'] + $quantity;
            if ($newQuantity > $product['qty']) {
                 throw new Exception("Cannot add more to cart. Max stock reached.");
            }
            $stmt = $this->conn->prepare("UPDATE {$this->table} SET quantity = :q WHERE id = :id");
            return $stmt->execute([':q' => $newQuantity, ':id' => $existing['id']]);
        } else {
            if ($quantity > $product['qty']) {
                throw new Exception("Cannot add more to cart. Max stock reached.");
            }
            $stmt = $this->conn->prepare("INSERT INTO {$this->table} (user_id, product_id, quantity) VALUES (:uid, :pid, :q)");
            return $stmt->execute([
                ':uid' => $user_id,
                ':pid' => $product_id,
                ':q' => $quantity
            ]);
        }
    }

    public function updateQuantity($cart_id, $user_id, $quantity) {
        // First get the product id to check stock
        $stmt = $this->conn->prepare("SELECT product_id FROM {$this->table} WHERE id = :cid AND user_id = :uid");
        $stmt->execute([':cid' => $cart_id, ':uid' => $user_id]);
        $cartItem = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cartItem) {
            throw new Exception("Cart item not found.");
        }

        $stmt = $this->conn->prepare("SELECT qty FROM products WHERE id = :pid");
        $stmt->execute([':pid' => $cartItem['product_id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($quantity > $product['qty']) {
             throw new Exception("Max stock reached.");
        }

        if ($quantity <= 0) {
            return $this->removeFromCart($cart_id, $user_id);
        }

        $stmt = $this->conn->prepare("UPDATE {$this->table} SET quantity = :q WHERE id = :cid AND user_id = :uid");
        return $stmt->execute([':q' => $quantity, ':cid' => $cart_id, ':uid' => $user_id]);
    }

    public function removeFromCart($cart_id, $user_id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE id = :cid AND user_id = :uid");
        return $stmt->execute([':cid' => $cart_id, ':uid' => $user_id]);
    }

    public function clearCart($user_id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE user_id = :uid");
        return $stmt->execute([':uid' => $user_id]);
    }
}
?>
