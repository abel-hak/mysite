<?php
// Database Configuration for XAMPP
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'NewSecurePassword123!');
define('DB_NAME', 'ecommerce_db');

// Create database connection
function getConnection() {
    try {
        $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// --- Password Hashing & Verification ---
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// --- Input Validation Helpers ---
function validate_string($input, $min = 1, $max = 255) {
    $input = trim($input);
    if (strlen($input) < $min || strlen($input) > $max) return false;
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}
function validate_int($input, $min = null, $max = null) {
    if (!filter_var($input, FILTER_VALIDATE_INT)) return false;
    $val = (int)$input;
    if ($min !== null && $val < $min) return false;
    if ($max !== null && $val > $max) return false;
    return $val;
}
function validate_float($input, $min = null, $max = null) {
    if (!filter_var($input, FILTER_VALIDATE_FLOAT)) return false;
    $val = (float)$input;
    if ($min !== null && $val < $min) return false;
    if ($max !== null && $val > $max) return false;
    return $val;
}
function validate_email($input) {
    $input = trim($input);
    return filter_var($input, FILTER_VALIDATE_EMAIL) ? $input : false;
}

// --- CSRF Token Helpers ---
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function check_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// --- Ensure CSRF token exists on session start ---
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    generate_csrf_token();
}


// Simulated users data (replace with database in production)
// $users = [
//     'admin' => ['password' => 'admin123', 'role' => 'admin'],
//     'customer' => ['password' => 'customer123', 'role' => 'customer']
// ];


function userExists($username) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetchColumn() > 0;
}

function getUser($username) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// --- User Creation with Password Hashing ---
function createUser($userData) {
    $pdo = getConnection();
    $hashedPassword = hashPassword($userData['password']);
    $role = isset($userData['role']) ? $userData['role'] : 'customer';
    $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, useraddress, pwd, role) VALUES (?, ?, ?, ?, ?, ?)");
    return $stmt->execute([
        $userData['username'],
        $userData['email'],
        $userData['phone'],
        $userData['useraddress'],
        $hashedPassword,
        $role
    ]);
}

// --- User Authentication with Password Verify ---
function authenticateUser($username, $password) {
    $user = getUser($username);
    if ($user && isset($user['pwd']) && verifyPassword($password, $user['pwd'])) {
        return $user;
    }
    return false;
}

function getOrders(){

    $username = $_SESSION['user']['username'];
    
    $userinfo = getUser($username);
    $userId = $userinfo['id'];

    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE userid = ?");
    $stmt->execute([$userId]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createOrder(array $product) {
    if (!isset($_SESSION['user']['username'])) {
        return ['error' => 'Please login to add items to cart'];
    }

    $username = $_SESSION['user']['username'];
    $userinfo = getUser($username);
    $userId = $userinfo['id'];

    $productId = $product['id'];
    $price = $product['price'];
    $name = $product['name'];
    $orderStatus = 'pending';
    try {
        $pdo = getConnection();
        $userid = getUser($_SESSION['user']['username'])['id'];

        // First, check current stock
        $stmt = $pdo->prepare("SELECT stock, name FROM products WHERE id = ?");
        $stmt->execute([$product['id']]);
        $productInfo = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$productInfo || $productInfo['stock'] <= 0) {
            return ['error' => "Sorry, {$productInfo['name']} is out of stock."];
        }

        // Check if product already exists in cart
        $stmt = $pdo->prepare("SELECT id, quantity FROM orders WHERE userid = ? AND productid = ? AND orderstatus = 'pending'");
        $stmt->execute([$userid, $product['id']]);
        $existingOrder = $stmt->fetch(PDO::FETCH_ASSOC);

        // Calculate new quantity
        $quantity = isset($product['quantity']) ? (int)$product['quantity'] : 1;
        $newQuantity = $existingOrder ? $existingOrder['quantity'] + $quantity : $quantity;

        // Check if new quantity exceeds available stock
        if ($newQuantity > $productInfo['stock']) {
            return ['error' => "Sorry, only {$productInfo['stock']} units of {$productInfo['name']} available."];
        }

        if ($existingOrder) {
            // Update quantity of existing order
            $stmt = $pdo->prepare("UPDATE orders SET quantity = ? WHERE id = ?");
            $success = $stmt->execute([$newQuantity, $existingOrder['id']]);
        } else {
            // Create new order
            $stmt = $pdo->prepare(
                "INSERT INTO orders (userid, productid, quantity, purchaseprice, orderstatus) 
                VALUES (?, ?, ?, ?, 'pending')"
            );
            $success = $stmt->execute([
                $userid,
                $product['id'],
                $quantity,
                $product['price']
            ]);
        }

        return $success ? ['success' => true] : ['error' => 'Failed to update cart'];
    } catch (PDOException $e) {
        error_log('Error in createOrder: ' . $e->getMessage());
        return ['error' => 'Failed to update cart'];
        return ['error' => 'Error adding item to cart'];
    }
}






// Helper functions
function formatPrice($price, $discountPrice = null) {
    if ($discountPrice && $discountPrice < $price) {
        return '<span class="price-original">ETB ' . number_format($price, 2) . '</span> <span class="price-discount">ETB ' . number_format($discountPrice, 2) . '</span>';
    }
    return '<span class="price">ETB ' . number_format($price, 2) . '</span>';
}


function getProducts($search = '', $category = '', $limit = null) {
    $pdo = getConnection();
    $sql = "SELECT * FROM products WHERE 1=1";
    $params = [];
    
    if ($search) {
        $sql .= " AND (name LIKE ? OR description LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    if ($category && $category !== 'all') {
        $sql .= " AND category = ?";
        $params[] = $category;
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if ($limit) {
        $sql .= " LIMIT ?";
        $params[] = $limit;
    }
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductById($id) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getCategories() {
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT DISTINCT category FROM products ORDER BY category");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}


// Session handling (simulated)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function isAdmin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}

// Wishlist Functions
function addToWishlist($productId) {
    if (!isLoggedIn()) {
        return ['error' => 'Please login to add items to wishlist'];
    }

    $username = $_SESSION['user']['username'];
    $userinfo = getUser($username);
    $userId = $userinfo['id'];

    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $stmt->execute([$userId, $productId]);
        return ['success' => true];
    } catch (PDOException $e) {
        // If item is already in wishlist, don't treat as error
        if ($e->getCode() == 23000) { // Duplicate entry error
            return ['success' => true, 'message' => 'Item already in wishlist'];
        }
        return ['error' => $e->getMessage()];
    }
}

function removeFromWishlist($productId) {
    if (!isLoggedIn()) {
        return ['error' => 'Please login to manage wishlist'];
    }

    $username = $_SESSION['user']['username'];
    $userinfo = getUser($username);
    $userId = $userinfo['id'];

    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        return ['success' => true];
    } catch (PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}

function getWishlist() {
    if (!isLoggedIn()) {
        return [];
    }

    $username = $_SESSION['user']['username'];
    $userinfo = getUser($username);
    $userId = $userinfo['id'];

    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare(
            "SELECT p.* FROM products p 
            INNER JOIN wishlist w ON w.product_id = p.id 
            WHERE w.user_id = ? 
            ORDER BY w.created_at DESC"
        );
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return [];
    }
}

function isInWishlist($productId) {
    if (!isLoggedIn()) {
        return false;
    }

    $username = $_SESSION['user']['username'];
    $userinfo = getUser($username);
    $userId = $userinfo['id'];

    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        return $stmt->fetchColumn() > 0;
    } catch (PDOException $e) {
        return false;
    }
}

function getUserCartCount() {
    if (!isLoggedIn()) {
        return 0;
    }
    
    try {
        $userid = getUser($_SESSION['user']['username'])['id'];
        $sql = "SELECT SUM(quantity) as total_items FROM orders WHERE userid = :userid AND orderstatus = 'pending'";
        $pdo = getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['userid' => $userid]);
        return (int)($stmt->fetch(PDO::FETCH_ASSOC)['total_items'] ?? 0);
    } catch (Exception $e) {
        error_log('Error getting cart count: ' . $e->getMessage());
        return 0;
    }
}

function removeFromCart($orderId) {
    if (!isLoggedIn()) {
        return ['error' => 'Please login to manage cart'];
    }

    try {
        $userid = getUser($_SESSION['user']['username'])['id'];
        $pdo = getConnection();
        
        // First verify the order belongs to the user and get its details
        $stmt = $pdo->prepare("SELECT id, productid FROM orders WHERE id = :orderId AND userid = :userid");
        $stmt->execute(['orderId' => $orderId, 'userid' => $userid]);
        $order = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$order) {
            return ['error' => 'Order not found'];
        }

        // Delete all quantities of this product for this user
        $stmt = $pdo->prepare("DELETE FROM orders 
                              WHERE userid = :userid 
                              AND productid = :productid");
        $stmt->execute([
            'userid' => $userid,
            'productid' => $order['productid']
        ]);

        return ['success' => true, 'message' => 'Item removed from cart'];
    } catch (Exception $e) {
        error_log('Error removing from cart: ' . $e->getMessage());
        return ['error' => 'Error removing item from cart'];
    }
}

function getCartTotal($userId) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare(
            "SELECT 
                SUM(o.quantity * o.purchaseprice) as subtotal,
                COUNT(DISTINCT o.id) as item_count
            FROM orders o
            WHERE o.userid = :userid AND o.orderstatus = 'pending'"
        );
        $stmt->execute(['userid' => $userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return [
            'subtotal' => (float)($result['subtotal'] ?? 0),
            'item_count' => (int)($result['item_count'] ?? 0),
            'shipping' => 0, // Free shipping
            'total' => (float)($result['subtotal'] ?? 0) // subtotal + shipping
        ];
    } catch (Exception $e) {
        error_log('Error calculating cart total: ' . $e->getMessage());
        return [
            'subtotal' => 0,
            'item_count' => 0,
            'shipping' => 0,
            'total' => 0
        ];
    }
}
?>