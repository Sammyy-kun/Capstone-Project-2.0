<?php
class Order {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function placeOrder($user_id, $address_id, $delivery_method, $payment_method, $delivery_fee, $notes, $distance_km, $items) {
        $totalQty = array_sum(array_column($items, 'quantity'));
        if ($totalQty > 200) {
            throw new Exception("Cart limit exceeded. Max 200 items per order.");
        }

        $itemsTotal  = array_sum(array_map(fn($i) => (float)$i['price'] * (int)$i['quantity'], $items));
        $totalAmount = $itemsTotal + (float)$delivery_fee;

        $this->conn->beginTransaction();
        try {
            $sql = "INSERT INTO orders (user_id, address_id, delivery_method, payment_method, delivery_fee, notes, distance_km, total_amount, status)
                    VALUES (:uid, :aid, :dm, :pm, :df, :notes, :dkm, :total, 'Pending')";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([
                ':uid'   => $user_id,
                ':aid'   => $address_id ?: null,
                ':dm'    => $delivery_method,
                ':pm'    => $payment_method,
                ':df'    => $delivery_fee,
                ':notes' => $notes ?: null,
                ':dkm'   => $distance_km ?: null,
                ':total' => $totalAmount,
            ]);
            $orderId = (int)$this->conn->lastInsertId();

            $iSql  = "INSERT INTO order_items (order_id, product_id, owner_id, product_name, company_name, quantity, price)
                      VALUES (:oid, :pid, :owner_id, :pname, :cname, :qty, :price)";
            $iStmt = $this->conn->prepare($iSql);
            foreach ($items as $item) {
                $iStmt->execute([
                    ':oid'      => $orderId,
                    ':pid'      => (int)$item['product_id'],
                    ':owner_id' => isset($item['owner_id']) ? (int)$item['owner_id'] : null,
                    ':pname'    => $item['product_name'] ?? '',
                    ':cname'    => $item['company_name'] ?? '',
                    ':qty'      => (int)$item['quantity'],
                    ':price'    => (float)$item['price'],
                ]);
            }

            // Remove processed cart items
            $cartIds = array_filter(array_map(fn($i) => (int)($i['cart_item_id'] ?? 0), $items));
            if (!empty($cartIds)) {
                $placeholders = implode(',', $cartIds);
                $this->conn->exec("DELETE FROM cart_items WHERE id IN ({$placeholders}) AND user_id = " . (int)$user_id);
            }

            $this->conn->commit();
            return $orderId;
        } catch (Exception $e) {
            $this->conn->rollBack();
            throw $e;
        }
    }

    public function getOrder($order_id, $user_id) {
        $sql = "SELECT o.*,
                       ua.recipient_name, ua.phone, ua.street, ua.barangay,
                       ua.city, ua.province, ua.zip_code, ua.label as address_label
                FROM orders o
                LEFT JOIN user_addresses ua ON ua.id = o.address_id
                WHERE o.id = :oid AND o.user_id = :uid";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':oid' => (int)$order_id, ':uid' => (int)$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getOrderItems($order_id) {
        $sql = "SELECT oi.*, p.image_url
                FROM order_items oi
                LEFT JOIN products p ON p.id = oi.product_id
                WHERE oi.order_id = :oid";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':oid' => (int)$order_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getUserOrders($user_id) {
        $sql = "SELECT o.*,
                       (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) as item_count,
                       (SELECT oi2.product_name FROM order_items oi2 WHERE oi2.order_id = o.id LIMIT 1) as first_item,
                       (SELECT p.image_url FROM order_items oi3 JOIN products p ON p.id = oi3.product_id WHERE oi3.order_id = o.id LIMIT 1) as first_image
                FROM orders o
                WHERE o.user_id = :uid
                ORDER BY o.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':uid' => (int)$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOwnerIdsByOrder($order_id) {
        $sql = "SELECT DISTINCT owner_id FROM order_items WHERE order_id = :oid AND owner_id IS NOT NULL";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':oid' => (int)$order_id]);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>
