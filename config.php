<?php
// Database Configuration for XAMPP
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
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

// Simulated users data (replace with database in production)
// $users = [
//     'admin' => ['password' => 'admin123', 'role' => 'admin'],
//     'customer' => ['password' => 'customer123', 'role' => 'customer']
// ];


function getUser($username) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getOrders(){

    $username = $_SESSION['user']['username'];
    
    $userinfo = getUser($username);
    $userId = $userinfo['id'];

    $pdo = getConnection();
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE userid = ?");
    $stmt->execute([$userid]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function createOrder(array $product) {

    if (!isset($_SESSION['user']['username'])) {
        header('Location: signup.php');
        die(); 
    }

    $username = $_SESSION['user']['username'];

    $userinfo = getUser($username);
    $userId = $userinfo['id'];

    $productId = $product['id'];
    $price = $product['price'];
    $name = $product['name'];
    $quantity = 1; // You can change this if quantity is dynamic
    $orderStatus = 'pending';

    try {
        $pdo = getConnection();

        $stmt = $pdo->prepare("INSERT INTO orders (userid, productid, quantity, purchaseprice, orderstatus)
                               VALUES (:userid, :productid, :quantity, :price, :status)");
        $stmt->execute([
            ':userid'     => $userId,
            ':productid'  => $productId,
            ':quantity'   => $quantity,
            ':price'      => $price,
            ':status'     => $orderStatus
        ]);

        return ['success' => true, 'order_id' => $pdo->lastInsertId()];
    } catch (PDOException $e) {
        return ['error' => $e->getMessage()];
    }
}


function createUser($userData) {
    $pdo = getConnection();

    // Hash the password before storing
    // $hashedPassword = password_hash($userData['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (username, email, phone, useraddress, pass) VALUES (?, ?, ?, ?, ?)");
    
    return $stmt->execute([
        $userData['username'],
        $userData['email'],
        $userData['phonenumber'],
        $userData['address'],
        $userData['password']
    ]);
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
session_start();

function isLoggedIn() {
    return isset($_SESSION['user']);
}

function isAdmin() {
    return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
}

function getCurrentUser() {
    return $_SESSION['user'] ?? null;
}
?>