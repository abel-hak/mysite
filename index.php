<?php
require_once 'config.php';

$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$products = getProducts($search, $category);
$categories = getCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Shop - Your Online Store</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <main class="main">
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h2>
                        Welcome to E-Shop
                        <?php if (isLoggedIn()): ?>
                            , <?= htmlspecialchars($_SESSION['user']['username']) ?>!
                        <?php endif; ?>
                    </h2>
                    <p>Discover amazing products at great prices</p>
                </div>
            </div>
        </section>


        <section class="filters">
            <div class="container">
                <form method="GET" class="filter-form">
                    <div class="search-box">
                        <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
                        <button type="submit">Search</button>
                    </div>
                    <div class="category-filter">
                        <select name="category" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                                    <?= ucfirst(htmlspecialchars($cat)) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
        </section>

        <section class="products">
            <div class="container">
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                        <div class="product-card" data-id="<?= $product['id'] ?>">
                            <div class="product-image">
                                <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                                <button class="wishlist-btn<?= isInWishlist($product['id']) ? ' active' : '' ?>" data-id="<?= $product['id'] ?>">
                                    <?= isInWishlist($product['id']) ? '♥' : '♡' ?>
                                </button>
                            </div>
                            <div class="product-info">
                                <h3><?= htmlspecialchars($product['name']) ?></h3>
                                <p class="product-price">
                                    <?= formatPrice($product['price'], $product['discount_price']) ?>
                                </p>
                                <p class="product-stock" style="color: <?= $product['stock'] > 0 ? '#2c5282' : '#e53e3e' ?>">
                                    <?= $product['stock'] > 0 ? "Stock: {$product['stock']}" : "Out of Stock" ?>
                                </p>
                                <div class="product-actions">
                                    <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-secondary">View Details</a>
                                    <button class="btn btn-primary add-to-cart" 
                                        data-id="<?= $product['id'] ?>" 
                                        data-name="<?= htmlspecialchars($product['name']) ?>" 
                                        data-price="<?= $product['discount_price'] ?: $product['price'] ?>" 
                                        <?= $product['stock'] <= 0 ? 'disabled style="opacity: 0.5; cursor: not-allowed;"' : '' ?>
                                    >
                                        <?= $product['stock'] > 0 ? 'Add to Cart' : 'Out of Stock' ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (empty($products)): ?>
                    <div class="no-products">
                        <p>No products found. Try adjusting your search or filter criteria.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>

    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> E-Shop. All rights reserved. | Practice Project for XAMPP</p>
        </div>
    </footer>
    <script src="script.js"></script>
</body>
</html>