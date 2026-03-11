<?php
class Product {
    private $conn;
    private $table = "products";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addProduct($owner_id, $name, $desc, $price, $qty, $img, $img2 = null, $img3 = null, $category = null) {
        $sql = "INSERT INTO {$this->table} (owner_id, product_name, description, category, price, qty, image_url, image_url_2, image_url_3)
                VALUES (:oid, :n, :d, :cat, :p, :q, :i, :i2, :i3)";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":oid" => $owner_id,
            ":n" => $name,
            ":d" => $desc,
            ":cat" => $category ?: null,
            ":p" => $price,
            ":q" => $qty,
            ":i" => $img,
            ":i2" => $img2,
            ":i3" => $img3
        ]);
    }

    public function editProduct($id, $owner_id, $name, $desc, $price, $qty, $img, $img2, $img3, $category = null) {
        $sql = "UPDATE {$this->table} 
                SET product_name = :n, description = :d, category = :cat, price = :p, qty = :q";
        
        $params = [
            ":n" => $name,
            ":d" => $desc,
            ":cat" => $category ?: null,
            ":p" => $price,
            ":q" => $qty,
            ":oid" => $owner_id,
            ":id" => $id
        ];

        // Only update image columns if new images were provided or if we want to overwrite them
        if ($img !== null) {
            $sql .= ", image_url = :i";
            $params[":i"] = $img;
        }
        if ($img2 !== null) {
             $sql .= ", image_url_2 = :i2";
             $params[":i2"] = $img2;
        }
        if ($img3 !== null) {
             $sql .= ", image_url_3 = :i3";
             $params[":i3"] = $img3;
        }

        $sql .= " WHERE id = :id AND owner_id = :oid";
        
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute($params);
    }

    public function deleteProduct($id, $owner_id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id AND owner_id = :oid";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ":id" => $id,
            ":oid" => $owner_id
        ]);
    }

    public function getOwnerProducts($owner_id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE owner_id = :oid");
        $stmt->execute([":oid" => $owner_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStoreFeatureProducts($owner_id) {
        $sql = "SELECT p.*, 
                (SELECT COUNT(*) FROM product_serial_numbers psn WHERE psn.product_id = p.id AND psn.status = 'sold') as sold_count
                FROM {$this->table} p
                WHERE p.owner_id = :oid
                ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([":oid" => $owner_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllProducts() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStoreProducts($owner_id) {
        $sql = "SELECT p.*,
                (SELECT COUNT(*) FROM product_serial_numbers psn WHERE psn.product_id = p.id AND psn.status = 'sold') as sold_count
                FROM {$this->table} p
                WHERE p.owner_id = :oid
                ORDER BY p.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':oid' => $owner_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStoreOwnerInfo($owner_id) {
        $sql = "SELECT u.id, u.full_name, u.username, u.profile_picture,
                (SELECT COUNT(*) FROM products p WHERE p.owner_id = u.id) as product_count,
                ba.business_name
                FROM users u
                LEFT JOIN business_applications ba ON ba.user_id = u.id AND ba.status = 'Approved'
                WHERE u.id = :oid AND u.role = 'owner'
                LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':oid' => $owner_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProductById($id) {
        $sql = "SELECT p.*, 
                (SELECT COUNT(*) FROM product_serial_numbers psn WHERE psn.product_id = p.id AND psn.status = 'sold') as sold_count
                FROM {$this->table} p WHERE p.id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStoresWithProducts($limit = 5, $offset = 0, $search = '', $category = '') {
        $conditions = '';
        $searchParam = '';
        if (!empty($category)) {
            $conditions .= " AND EXISTS (SELECT 1 FROM products p2 WHERE p2.owner_id = u.id AND p2.category = :cat1)";
        } elseif (!empty($search)) {
            $conditions .= " AND EXISTS (SELECT 1 FROM products p2 WHERE p2.owner_id = u.id AND (p2.product_name LIKE :search1 OR p2.description LIKE :search2))";
            $searchParam = '%' . $search . '%';
        }

        $sql = "SELECT u.id, u.full_name, u.username, u.profile_picture,
                ba.business_name,
                (SELECT COUNT(*) FROM products p WHERE p.owner_id = u.id) as product_count
                FROM users u
                LEFT JOIN business_applications ba ON ba.user_id = u.id AND ba.status = 'Approved'
                WHERE u.role = 'owner'
                AND EXISTS (SELECT 1 FROM products p WHERE p.owner_id = u.id)
                {$conditions}
                ORDER BY u.id DESC
                LIMIT :lmt OFFSET :off";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':lmt', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':off', (int)$offset, PDO::PARAM_INT);
        if (!empty($category)) {
            $stmt->bindValue(':cat1', $category);
        } elseif (!empty($search)) {
            $stmt->bindValue(':search1', $searchParam);
            $stmt->bindValue(':search2', $searchParam);
        }
        $stmt->execute();
        $stores = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($stores as &$store) {
            $pSql = "SELECT p.*,
                     (SELECT COUNT(*) FROM product_serial_numbers psn WHERE psn.product_id = p.id AND psn.status = 'sold') as sold_count
                     FROM products p
                     WHERE p.owner_id = :oid";
            $pParams = [':oid' => $store['id']];
            if (!empty($category)) {
                $pSql .= " AND p.category = :cat1";
                $pParams[':cat1'] = $category;
            } elseif (!empty($search)) {
                $pSql .= " AND (p.product_name LIKE :search1 OR p.description LIKE :search2)";
                $pParams[':search1'] = $searchParam;
                $pParams[':search2'] = $searchParam;
            }
            $pSql .= " ORDER BY p.created_at DESC LIMIT 3";
            $pStmt = $this->conn->prepare($pSql);
            $pStmt->execute($pParams);
            $store['products'] = $pStmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $stores;
    }

    public function countStoresWithProducts($search = '', $category = '') {
        $conditions = '';
        if (!empty($category)) {
            $conditions = "AND EXISTS (SELECT 1 FROM products p2 WHERE p2.owner_id = u.id AND p2.category = :cat1)";
        } elseif (!empty($search)) {
            $conditions = "AND EXISTS (SELECT 1 FROM products p2 WHERE p2.owner_id = u.id AND (p2.product_name LIKE :search1 OR p2.description LIKE :search2))";
        }
        $sql = "SELECT COUNT(DISTINCT u.id) as total FROM users u
             WHERE u.role = 'owner'
             AND EXISTS (SELECT 1 FROM products p WHERE p.owner_id = u.id)
             {$conditions}";
        $stmt = $this->conn->prepare($sql);
        if (!empty($category)) {
            $stmt->bindValue(':cat1', $category);
        } elseif (!empty($search)) {
            $searchParam = '%' . $search . '%';
            $stmt->bindValue(':search1', $searchParam);
            $stmt->bindValue(':search2', $searchParam);
        }
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }
}
?>
