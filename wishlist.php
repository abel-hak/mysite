<?php
require_once 'config.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Handle remove from wishlist
if (isset($_POST['remove']) && isset($_POST['product_id'])) {
    $result = removeFromWishlist($_POST['product_id']);
    if ($result['success']) {
        header('Location: wishlist.php?msg=removed');
        exit();
    }
}

$wishlistItems = getWishlist();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Shop - Your Online Store</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .wishlist-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .wishlist-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .wishlist-item {
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
            position: relative;
        }
        .wishlist-item img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 4px;
        }
        .remove-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #ff4444;
            color: white;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        .remove-btn:hover {
            background: #cc0000;
            transform: scale(1.1);
            box-shadow: 0 3px 6px rgba(0,0,0,0.3);
        }
        .empty-wishlist {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        .action-buttons button {
            flex: 1;
            padding: 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .add-to-cart {
            background: #4CAF50;
            color: white;
        }
        .add-to-cart:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
        <?php include 'includes/header.php'; ?>

    <div class="wishlist-container">
        <h1>My Wishlist</h1>
        
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'removed'): ?>
            <div class="alert alert-success">Item removed from wishlist successfully!</div>
        <?php endif; ?>

        <?php if (empty($wishlistItems)): ?>
            <div class="empty-wishlist">
                <i class="fas fa-heart" style="font-size: 48px; color: #ddd;"></i>
                <h2>Your wishlist is empty</h2>
                <p>Browse our products and add items to your wishlist!</p>
                <a href="index.php" class="btn">Browse Products</a>
            </div>
        <?php else: ?>
            <div class="wishlist-grid">
                <?php foreach ($wishlistItems as $item): ?>
                    <div class="wishlist-item">
                        <form method="POST">
                            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['id']); ?>">
                            <button type="submit" name="remove" class="remove-btn">
                                <i class="fas fa-times"></i>
                            </button>
                        </form>
                        <img src="images/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                        <p class="price"><?php echo formatPrice($item['price']); ?></p>
                        <div class="action-buttons">
                            <button class="add-to-cart btn btn-primary" 
                                data-id="<?php echo htmlspecialchars($item['id']); ?>" 
                                data-name="<?php echo htmlspecialchars($item['name']); ?>" 
                                data-price="<?php echo htmlspecialchars($item['discount_price'] ?: $item['price']); ?>">
                                Add to Cart
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

        <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> E-Shop. All rights reserved. | Practice Project for XAMPP</p>
        </div>
    </footer>
    <script src="script.js"></script>
</body>
</html>
