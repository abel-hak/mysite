<?php
require_once 'config.php';

// Require login for cart access
if (!isset($_SESSION['user'])) {
    header('Location: login.php?redirect=cart');
    exit;
}


// Handle AJAX request
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    
    // Handle stock validation
    if (isset($_POST['action']) && $_POST['action'] === 'validate_stock') {
        try {
            $userid = getUser($_SESSION['user']['username'])['id'];
            $pdo = getConnection();
            
            // Get cart items with current stock levels
            $stmt = $pdo->prepare("
                SELECT o.productid, p.name, o.quantity, p.stock
                FROM orders o
                JOIN products p ON p.id = o.productid
                WHERE o.userid = ? AND o.orderstatus = 'pending'
            ");
            $stmt->execute([$userid]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Check stock for each item
            foreach ($items as $item) {
                if ($item['quantity'] > $item['stock']) {
                    echo json_encode([
                        'success' => false,
                        'message' => "Insufficient stock for {$item['name']}. Available: {$item['stock']}"
                    ]);
                    exit;
                }
            }
            
            echo json_encode(['success' => true]);
            exit;
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }
}

// Handle AJAX request for removing items
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    
    // Handle remove from cart
    if (isset($_POST['action']) && $_POST['action'] === 'remove' && isset($_POST['order_id'])) {
        $result = removeFromCart($_POST['order_id']);
        if ($result['success']) {
            $result['cartCount'] = getUserCartCount();
        }
        echo json_encode($result);
        exit;
    }

    // Handle finalize order (update status, decrement stock)
    if (isset($_POST['action']) && $_POST['action'] === 'finalize') {
        try {
            $userid = getUser($_SESSION['user']['username'])['id'];
            $pdo = getConnection();
            $pdo->beginTransaction();
            
            // Validate stock one final time
            $stmt = $pdo->prepare("
                SELECT o.productid, p.name, o.quantity, p.stock
                FROM orders o
                JOIN products p ON p.id = o.productid
                WHERE o.userid = ? AND o.orderstatus = 'pending'
                FOR UPDATE");
            $stmt->execute([$userid]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($items as $item) {
                if ($item['quantity'] > $item['stock']) {
                    $pdo->rollBack();
                    echo json_encode([
                        'success' => false,
                        'message' => "Insufficient stock for {$item['name']}. Available: {$item['stock']}"
                    ]);
                    exit;
                }
            }
            
            // Update stock levels with validation
            $updateStock = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");
            foreach ($items as $item) {
                $result = $updateStock->execute([$item['quantity'], $item['productid'], $item['quantity']]);
                if ($updateStock->rowCount() === 0) {
                    // If no rows were updated, it means stock is insufficient
                    $pdo->rollBack();
                    echo json_encode([
                        'success' => false,
                        'message' => "Sorry, {$item['name']} is now out of stock or has insufficient quantity."
                    ]);
                    exit;
                }
            }
            
            // Update order status
            try {
                // First try with payment_method column
                $paymentMethod = $_POST['payment_method'] ?? 'cash';
                $updateOrders = $pdo->prepare("
                    UPDATE orders 
                    SET orderstatus = 'completed', 
                        payment_method = ?
                    WHERE userid = ? AND orderstatus = 'pending'");
                $updateOrders->execute([$paymentMethod, $userid]);
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), "Unknown column 'payment_method'") !== false) {
                    // If payment_method column doesn't exist, just update status
                    $updateOrders = $pdo->prepare("
                        UPDATE orders 
                        SET orderstatus = 'completed'
                        WHERE userid = ? AND orderstatus = 'pending'");
                    $updateOrders->execute([$userid]);
                } else {
                    throw $e; // Re-throw if it's a different error
                }
            }
            
            $pdo->commit();
            echo json_encode(['success' => true]);
            exit;
            
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
        }
    }
    
    // Handle adding items to cart
    if (isset($_POST['id']) && isset($_POST['name']) && isset($_POST['price'])) {
        $data = [
            'id'    => $_POST['id'],
            'name'  => $_POST['name'],
            'price' => $_POST['price']
        ];
        $result = createOrder($data);
        echo json_encode([
            'success' => true,
            'cartCount' => getUserCartCount(),
            'message' => 'Item added to cart successfully'
        ]);
        exit;
    }
}

// Get user ID and cart items
$userid = getUser($_SESSION['user']['username'])['id'];
$sql = "
    SELECT 
        MIN(o.id) as order_id,
        o.productid,
        SUM(o.quantity) as quantity,
        o.purchaseprice,
        p.name as product_name,
        p.image as product_image,
        SUM(o.quantity * o.purchaseprice) as total_price
    FROM orders o
    JOIN products p ON p.id = o.productid
    WHERE o.userid = :userid AND o.orderstatus = 'pending'
    GROUP BY o.productid, o.purchaseprice, p.name, p.image
";
$pdo = getConnection();
$stmt = $pdo->prepare($sql);
$stmt->execute(['userid' => $userid]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get cart totals
$cartTotals = getCartTotal($userid);

// Now $cartItems is an array of associative arrays containing order details
// print_r($cartItems);

function getproductname($pId){
    $product = getProductById($pId);
    return $product['name'];
}

function getproductImage($pId){
    $product = getProductById($pId);
    return $product['image'];
}

// Now $results is an associative array keyed by productid
// print_r();


?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - E-Shop</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="cart-enhance.js"></script>
    <style>
        .order-card{
            width:550px;
            border: 1px solid rgba(0, 0, 0, 0.11);
            padding: 6px;
            margin: 10px;

            display: flex;
            align-items: center;
            gap: 24px;
            /* Remove justify-content: space-between for better alignment */
            box-shadow: var(--shadow);
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="main">
        <div class="container">
            <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
            <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 1rem; margin-bottom: 1rem; border-radius: 4px; text-align: center;">
                Order completed successfully! Your cart is now empty.
            </div>
            <?php endif; ?>

            
            <h1>Shopping Cart</h1>
            
            <div class="cart-container">

            
                <div class="cart-items">
                    <div id="cart-content">
                        <?php if (empty($cartItems)): ?>
                            <div class="empty-cart">
                                <p>Your cart is empty</p>
                                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($cartItems as $item): ?>
                                <div class="order-card" data-order-id="<?= $item['order_id'] ?>">
                                    <div class="order-image">
                                        <img class="orderImg" src="images/<?= htmlspecialchars($item['product_image']) ?>" 
                                             alt="<?= htmlspecialchars($item['product_name']) ?>">
                                    </div>
                                    <div style="flex: 1; display: flex; align-items: center; justify-content: space-between;">
                                        <div class="order-details" style="margin-right: 16px;">
                                            <h3><?= htmlspecialchars($item['product_name']) ?></h3>
                                            <p><strong>Quantity:</strong> <?= htmlspecialchars($item['quantity']) ?></p>
                                            <p><strong>Price:</strong> ETB <?= number_format($item['purchaseprice'], 2) ?></p>
                                            <p><strong>Total:</strong> ETB <?= number_format($item['purchaseprice'] * $item['quantity'], 2) ?></p>
                                        </div>
                                        <button class="remove-from-cart btn btn-danger" data-order-id="<?= $item['order_id'] ?>" style="height: 40px; align-self: flex-start; margin-left: 16px;">
                                            Remove
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="cart-sidebar">
                    <div class="cart-summary">
                        <h3>Order Summary</h3>
                        <div class="summary-row">
                            <span>Items (<?= $cartTotals['item_count'] ?>):</span>
                            <span id="subtotal">ETB <?= number_format($cartTotals['subtotal'], 2) ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping:</span>
                            <span id="shipping">ETB <?= number_format($cartTotals['shipping'], 2) ?></span>
                        </div>
                        <div class="summary-row discount-section" style="display: none;">
                            <span>Discount:</span>
                            <span id="discount">-ETB 0.00</span>
                        </div>
                        <div class="summary-row total">
                            <span>Total:</span>
                            <span id="total">ETB <?= number_format($cartTotals['total'], 2) ?></span>
                        </div>
                        
                        <div class="discount-code">
                            <input type="text" id="discount-input" placeholder="Enter discount code">
                            <button id="apply-discount" class="btn btn-secondary">Apply</button>
                        </div>
                        
                        <div class="cart-actions">
                            <a href="index.php" class="btn btn-outline">Continue Shopping</a>
                            <?php if (!empty($cartItems)): ?>
                                <button class="btn btn-primary" id="proceed-checkout">Proceed to Checkout</button>
                            <?php endif; ?>
                        </div>

                        <!-- Payment Methods Section (hidden by default) -->
                        <div id="payment-methods" style="display:none; flex-direction:column; gap:20px; padding:24px; background:#f9f9f9; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,0.04); margin-top:32px;">
                            <h3>Select Payment Method</h3>
                            <div class="payment-options" style="display:flex; flex-direction:column; gap:12px;">
                                <label class="payment-option" style="display:flex; align-items:center; gap:8px; padding:12px; border:1px solid #ddd; border-radius:6px; cursor:pointer;">
                                    <input type="radio" name="payment_method" value="credit_card">
                                    <span>Credit Card</span>
                                </label>
                                <label class="payment-option" style="display:flex; align-items:center; gap:8px; padding:12px; border:1px solid #ddd; border-radius:6px; cursor:pointer;">
                                    <input type="radio" name="payment_method" value="paypal">
                                    <span>PayPal</span>
                                </label>
                                <label class="payment-option" style="display:flex; align-items:center; gap:8px; padding:12px; border:1px solid #ddd; border-radius:6px; cursor:pointer;">
                                    <input type="radio" name="payment_method" value="cash">
                                    <span>Cash on Delivery</span>
                                </label>
                            </div>
                            <div id="stock-error" style="display:none; color:red; margin-top:10px;"></div>
                            <button id="finish-order" class="btn btn-primary" style="margin-top:16px;" disabled>Complete Order</button>
                            <div id="order-success" style="display:none; color:green; font-weight:bold; margin-top:10px; text-align:center;">Order completed successfully!</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 E-Shop. All rights reserved. | Practice Project for XAMPP</p>
        </div>
    </footer>

    <script src="script.js"></script>
    <script src="cart-enhance.js"></script>
</body>
</html>