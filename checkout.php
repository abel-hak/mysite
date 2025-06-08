<?php 
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process order (in production, save to database)
    $orderData = [
        'customer_name' => $_POST['name'] ?? '',
        'customer_email' => $_POST['email'] ?? '',
        'customer_phone' => $_POST['phone'] ?? '',
        'shipping_address' => $_POST['address'] ?? '',
        'shipping_method' => $_POST['shipping'] ?? 'standard',
        'payment_method' => $_POST['payment'] ?? 'cash',
        'total_amount' => $_POST['total'] ?? 0,
        'discount_code' => $_POST['discount_code'] ?? null,
        'discount_amount' => $_POST['discount_amount'] ?? 0,
        'order_items' => $_POST['items'] ?? '[]'
    ];
    
    // TODO: Save order to database
    // $pdo = getConnection();
    // $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_email, ...) VALUES (?, ?, ...)");
    // $stmt->execute([...]);
    
    $orderId = rand(1000, 9999); // Simulate order ID
    echo json_encode(['success' => true, 'order_id' => $orderId]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - E-Shop</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="nav-brand">
                <h1><a href="index.php">E-Shop</a></h1>
            </div>
            <nav class="nav-menu">
                <a href="index.php">Home</a>
                <a href="cart.php">Cart (<span id="cart-count">0</span>)</a>
                <a href="#" id="wishlist-link">Wishlist (<span id="wishlist-count">0</span>)</a>
                <?php if (isLoggedIn()): ?>
                    <a href="login.php?action=logout">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <h1>Checkout</h1>
            
            <div class="checkout-container">
                <div class="checkout-steps">
                    <div class="step active" data-step="1">
                        <span class="step-number">1</span>
                        <span class="step-title">Shipping Information</span>
                    </div>
                    <div class="step" data-step="2">
                        <span class="step-number">2</span>
                        <span class="step-title">Shipping Method</span>
                    </div>
                    <div class="step" data-step="3">
                        <span class="step-number">3</span>
                        <span class="step-title">Payment</span>
                    </div>
                    <div class="step" data-step="4">
                        <span class="step-number">4</span>
                        <span class="step-title">Review Order</span>
                    </div>
                </div>

                <form id="checkout-form" class="checkout-form">
                    <div class="step-content active" data-step="1">
                        <h3>Shipping Information</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone">
                            </div>
                            <div class="form-group full-width">
                                <label for="address">Shipping Address *</label>
                                <textarea id="address" name="address" required placeholder="Street address, city, state, zip code"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="step-content" data-step="2">
                        <h3>Shipping Method</h3>
                        <div class="shipping-options">
                            <label class="shipping-option">
                                <input type="radio" name="shipping" value="standard" checked>
                                <div class="option-content">
                                    <strong>Standard Shipping</strong>
                                    <span class="shipping-time">5-7 business days</span>
                                    <span class="shipping-price">Free</span>
                                </div>
                            </label>
                            <label class="shipping-option">
                                <input type="radio" name="shipping" value="express">
                                <div class="option-content">
                                    <strong>Express Shipping</strong>
                                    <span class="shipping-time">2-3 business days</span>
                                    <span class="shipping-price">$9.99</span>
                                </div>
                            </label>
                            <label class="shipping-option">
                                <input type="radio" name="shipping" value="overnight">
                                <div class="option-content">
                                    <strong>Overnight Shipping</strong>
                                    <span class="shipping-time">1 business day</span>
                                    <span class="shipping-price">$19.99</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="step-content" data-step="3">
                        <h3>Payment Method</h3>
                        <div class="payment-options">
                            <label class="payment-option">
                                <input type="radio" name="payment" value="cash" checked>
                                <div class="option-content">
                                    <strong>Cash on Delivery</strong>
                                    <span>Pay when you receive your order</span>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment" value="card">
                                <div class="option-content">
                                    <strong>Credit/Debit Card</strong>
                                    <span>Secure online payment (Demo)</span>
                                </div>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment" value="paypal">
                                <div class="option-content">
                                    <strong>PayPal</strong>
                                    <span>Pay with your PayPal account (Demo)</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="step-content" data-step="4">
                        <h3>Review Your Order</h3>
                        <div id="order-review">
                            <div class="review-section">
                                <h4>Order Items</h4>
                                <div id="review-items"></div>
                            </div>
                            <div class="review-section">
                                <h4>Shipping Information</h4>
                                <div id="review-shipping"></div>
                            </div>
                            <div class="review-section">
                                <h4>Order Summary</h4>
                                <div id="review-summary"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-navigation">
                        <button type="button" id="prev-btn" class="btn btn-outline" style="display: none;">Previous</button>
                        <button type="button" id="next-btn" class="btn btn-primary">Next</button>
                        <button type="submit" id="place-order-btn" class="btn btn-primary" style="display: none;">Place Order</button>
                    </div>
                </form>

                <div class="checkout-sidebar">
                    <div class="order-summary">
                        <h3>Order Summary</h3>
                        <div id="checkout-items"></div>
                        <div class="summary-totals">
                            <div class="summary-row">
                                <span>Subtotal:</span>
                                <span id="checkout-subtotal">$0.00</span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping:</span>
                                <span id="checkout-shipping">$0.00</span>
                            </div>
                            <div class="summary-row total">
                                <span>Total:</span>
                                <span id="checkout-total">$0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Order Confirmation Modal -->
    <div id="order-modal" class="modal">
        <div class="modal-content">
            <h2>Order Confirmed!</h2>
            <p>Thank you for your order. Your order number is: <strong id="order-number"></strong></p>
            <p>You will receive a confirmation email shortly.</p>
            <button class="btn btn-primary" onclick="window.location.href='index.php'">Continue Shopping</button>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 E-Shop. All rights reserved. | Practice Project for XAMPP</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>