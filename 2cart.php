<?php
require_once 'config.php';  

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!isLoggedIn()) {
        http_response_code(401);
        echo json_encode(['error' => 'Unauthorized']);
        exit;
    }

    $user = getCurrentUser();
    $userid = $user['id'];

    $pdo = getConnection();

    if ($_POST['action'] === 'save_cart') {
        $cartJson = $_POST['cart'] ?? '[]';
        $cartItems = json_decode($cartJson, true);

        if (!is_array($cartItems)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid cart data']);
            exit;
        }

        try {
            $pdo->beginTransaction();

            // Delete old pending orders
            $delStmt = $pdo->prepare("DELETE FROM orders WHERE userid = ? AND orderstatus = 'pending'");
            $delStmt->execute([$userid]);

            // Insert new pending orders
            $insStmt = $pdo->prepare("INSERT INTO orders (userid, productid, quantity, purchaseprice, orderstatus) VALUES (?, ?, ?, ?, 'pending')");

            foreach ($cartItems as $item) {
                if (!isset($item['id'], $item['quantity'])) continue;

                $prod = getProductById($item['id']);
                if (!$prod) continue;

                $priceToUse = ($prod['discount_price'] !== null && $prod['discount_price'] > 0) ? $prod['discount_price'] : $prod['price'];
                $quantity = max(1, intval($item['quantity']));

                $insStmt->execute([$userid, $item['id'], $quantity, $priceToUse]);
            }

            $pdo->commit();

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            $pdo->rollBack();
            http_response_code(500);
            echo json_encode(['error' => 'Failed to save cart: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($_POST['action'] === 'load_cart') {
        try {
            $stmt = $pdo->prepare("
                SELECT o.productid AS id, p.name, p.price, p.discount_price, o.quantity
                FROM orders o
                JOIN products p ON o.productid = p.id
                WHERE o.userid = ? AND o.orderstatus = 'pending'
            ");
            $stmt->execute([$userid]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $cart = [];
            foreach ($orders as $order) {
                $priceToUse = ($order['discount_price'] !== null && $order['discount_price'] > 0) ? $order['discount_price'] : $order['price'];
                $cart[] = [
                    'id' => intval($order['id']),
                    'cartItemId' => $order['id'] . '__', // keep your format if needed
                    'name' => $order['name'],
                    'price' => floatval($priceToUse),
                    'quantity' => intval($order['quantity']),
                    'color' => '',
                    'size' => ''
                ];
            }
            echo json_encode(['cart' => $cart]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to load cart: ' . $e->getMessage()]);
        }
        exit;
    }

    if ($_POST['action'] === 'clear_cart') {
        try {
            $delStmt = $pdo->prepare("DELETE FROM orders WHERE userid = ? AND orderstatus = 'pending'");
            $delStmt->execute([$userid]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to clear cart: ' . $e->getMessage()]);
        }
        exit;
    }

    http_response_code(400);
    echo json_encode(['error' => 'Unknown action']);
    exit;
}

// If GET request, serve your HTML page below as normal
header('Content-Type: text/html; charset=utf-8');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <!-- your head stuff -->
</head>
<body>
  <!-- your cart page HTML -->

<script src="script.js"></script>
<script>
$(function() {
  <?php if (isLoggedIn()): ?>
  $.post('cart.php', {action: 'load_cart'}, function(res) {
    if (res.cart) {
      localStorage.setItem('cart', JSON.stringify(res.cart));
      updateCartCount();
      updateCartDisplay();
    }
  }, 'json');
  <?php endif; ?>

  function syncCartToDB() {
    if (!<?php echo isLoggedIn() ? 'true' : 'false'; ?>) return;
    const cart = localStorage.getItem('cart') || '[]';
    $.post('cart.php', {action: 'save_cart', cart: cart});
  }

  // Override your existing cart update functions to call syncCartToDB()
  // Example:
  const oldAddToCart = window.addToCart;
  window.addToCart = function(id, name, price, variants) {
    oldAddToCart(id, name, price, variants);
    syncCartToDB();
  };
  // Repeat similarly for removeFromCart, updateCartQuantity etc.
});
</script>

</body>
</html>
