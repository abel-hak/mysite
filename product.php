<?php
require_once 'config.php';

$productId = $_GET['id'] ?? 0;
$product = getProductById($productId);

if (!$product) {
    header('Location: index.php');
    exit;
}

// $variants = json_decode($product['variants'], true) ?: ['colors' => [], 'sizes' => []];

// Sample reviews (in production, these would come from database)
$reviews = [
    ['name' => 'John D.', 'rating' => 5, 'comment' => 'Excellent product! Highly recommended.'],
    ['name' => 'Sarah M.', 'rating' => 4, 'comment' => 'Good quality, fast shipping.'],
    ['name' => 'Mike R.', 'rating' => 5, 'comment' => 'Exactly what I was looking for.']
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - E-Shop</title>
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
                    <?php if (isAdmin()): ?>
                        <a href="admin.php">Admin</a>
                    <?php endif; ?>
                    <a href="login.php?action=logout">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </nav>
            <div class="nav-toggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <div class="breadcrumb">
                <a href="index.php">Home</a> > <a href="index.php?category=<?= htmlspecialchars($product['category']) ?>"><?= ucfirst(htmlspecialchars($product['category'])) ?></a> > <?= htmlspecialchars($product['name']) ?>
            </div>

            <div class="product-detail">
                <div class="product-gallery">
                    <div class="main-image">
                        <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                    </div>
                </div>

                <div class="product-info">
                    <h1><?= htmlspecialchars($product['name']) ?></h1>
                    <div class="product-price">
                        <?= formatPrice($product['price'], $product['discount_price']) ?>
                    </div>
                    
                    <div class="stock-status">
                        <?php if ($product['stock'] > 0): ?>
                            <span class="in-stock">✓ In Stock (<?= $product['stock'] ?> available)</span>
                        <?php else: ?>
                            <span class="out-of-stock">Out of Stock</span>
                        <?php endif; ?>
                    </div>

                    <div class="product-description">
                        <p><?= htmlspecialchars($product['description']) ?></p>
                    </div>

                    <form id="product-form" class="product-options">
                        <?php if (!empty($variants['colors'])): ?>
                            <div class="variant-group">
                                <label>Color:</label>
                                <select name="color" required>
                                    <option value="">Select Color</option>
                                    <?php foreach ($variants['colors'] as $color): ?>
                                        <option value="<?= htmlspecialchars($color) ?>"><?= htmlspecialchars($color) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($variants['sizes'])): ?>
                            <div class="variant-group">
                                <label>Size:</label>
                                <select name="size" required>
                                    <option value="">Select Size</option>
                                    <?php foreach ($variants['sizes'] as $size): ?>
                                        <option value="<?= htmlspecialchars($size) ?>"><?= htmlspecialchars($size) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="quantity-group">
                            <label>Quantity:</label>
                            <input type="number" name="quantity" value="1" min="1" max="<?= $product['stock'] ?>">
                        </div>

                        <div class="product-actions">
                            <button type="button" class="btn btn-primary btn-large add-to-cart" 
                                    data-id="<?= $product['id'] ?>" 
                                    data-name="<?= htmlspecialchars($product['name']) ?>" 
                                    data-price="<?= $product['discount_price'] ?: $product['price'] ?>"
                                    <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
                                Add to Cart
                            </button>
                            <button type="button" class="btn btn-outline wishlist-btn" data-id="<?= $product['id'] ?>">
                                Add to Wishlist
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="product-reviews">
                <h3>Customer Reviews</h3>
                <div class="reviews-list">
                    <?php foreach ($reviews as $review): ?>
                        <div class="review">
                            <div class="review-header">
                                <span class="reviewer-name"><?= htmlspecialchars($review['name']) ?></span>
                                <div class="rating">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <span class="star <?= $i <= $review['rating'] ? 'filled' : '' ?>">★</span>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="review-comment"><?= htmlspecialchars($review['comment']) ?></p>
                        </div>
                    <?php endforeach; ?>
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
</body>
</html>