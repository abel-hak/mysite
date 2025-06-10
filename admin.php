<?php
session_start();
require_once 'config.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check for all POST actions
    if (!isset($_POST['csrf_token']) || !check_csrf_token($_POST['csrf_token'])) {
        $message = 'Invalid CSRF token. Please reload and try again.';
    } else if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_product':
                // Input validation
                $name = validate_string($_POST['name'] ?? '', 2, 100);
                $description = validate_string($_POST['description'] ?? '', 2, 1000);
                $price = validate_float($_POST['price'] ?? '');
                $discount_price = isset($_POST['discount_price']) ? validate_float($_POST['discount_price']) : 0;
                $stock = validate_int($_POST['stock'] ?? '');
                $category = validate_string($_POST['category'] ?? '', 2, 100);
                if ($category === 'new' && !empty($_POST['new_category'])) {
                    $category = validate_string(trim($_POST['new_category']), 2, 100);
                }
                $image = !empty($_POST['image_filename']) ? validate_string(trim($_POST['image_filename']), 2, 255) : 'dummy1.png';
                if (!$name || !$description || $price === false || $stock === false || !$category) {
                    $message = 'Invalid product data. Please check your inputs.';
                    break;
                }
                $pdo = getConnection();
                $stmt = $pdo->prepare("INSERT INTO products (name, description, price, discount_price, stock, category, image, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
                $stmt->execute([
                    $name,
                    $description,
                    $price,
                    $discount_price ?? 0,
                    $stock,
                    $category,
                    $image
                ]);
                $message = 'Product added to database.';
                break;

            case 'edit_product':
                // Input validation
                $name = validate_string($_POST['name'] ?? '', 2, 100);
                $description = validate_string($_POST['description'] ?? '', 2, 1000);
                $price = validate_float($_POST['price'] ?? '');
                $discount_price = isset($_POST['discount_price']) ? validate_float($_POST['discount_price']) : 0;
                $stock = validate_int($_POST['stock'] ?? '');
                $category = validate_string($_POST['category'] ?? '', 2, 100);
                $product_id = validate_int($_POST['product_id'] ?? '');
                if (!$name || !$description || $price === false || $stock === false || !$category || $product_id === false) {
                    $message = 'Invalid product data. Please check your inputs.';
                    break;
                }
                $pdo = getConnection();
                $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, discount_price = ?, stock = ?, category = ? WHERE id = ?");
                $stmt->execute([
                    $name,
                    $description,
                    $price,
                    $discount_price ?? 0,
                    $stock,
                    $category,
                    $product_id
                ]);
                $message = 'Product updated.';
                break;

            case 'delete_product':
                $product_id = validate_int($_POST['product_id'] ?? '');
                if ($product_id === false) {
                    $message = 'Invalid product ID.';
                    break;
                }
                // Delete all orders for this product first to avoid foreign key error
                $pdo = getConnection();
                $stmt = $pdo->prepare("DELETE FROM orders WHERE productid = ?");
                $stmt->execute([$product_id]);
                // Now delete the product
                $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
                $stmt->execute([$product_id]);
                $message = 'Product and related orders deleted.';
                break;

            case 'add_order':
                $userid = validate_int($_POST['userid'] ?? '');
                $productid = validate_int($_POST['productid'] ?? '');
                $quantity = validate_int($_POST['quantity'] ?? '');
                $purchaseprice = validate_float($_POST['purchaseprice'] ?? '');
                $orderstatus = validate_string($_POST['orderstatus'] ?? 'pending', 2, 20);
                if ($userid === false || $productid === false || $quantity === false || $purchaseprice === false || !$orderstatus) {
                    $message = 'Invalid order data.';
                    break;
                }
                // Add new order from admin panel
                $pdo = getConnection();
                $stmt = $pdo->prepare("INSERT INTO orders (userid, productid, quantity, purchaseprice, createdat, updatedat, orderstatus) VALUES (?, ?, ?, ?, NOW(), NOW(), ?)");
                $stmt->execute([
                    $userid,
                    $productid,
                    $quantity,
                    $purchaseprice,
                    $orderstatus
                ]);
                $message = 'Order added.';
                break;
        }
    }
}

$products = getProducts();
$categories = getCategories();

// Fetch all orders from the database for admin
$pdo = getConnection();
$orders = $pdo->query("SELECT o.*, u.username AS customer, p.name AS product_name FROM orders o JOIN users u ON o.userid = u.id JOIN products p ON o.productid = p.id ORDER BY o.createdat DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - E-Shop</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="nav-brand">
                <h1><a href="index.php">E-Shop Admin</a></h1>
            </div>
            <nav class="nav-menu">
                <a href="index.php">View Store</a>
                <a href="login.php?action=logout">Logout</a>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <h1>Admin Dashboard</h1>
            
            <?php if ($message): ?>
                <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            
            <div class="admin-tabs">
                <button class="tab-btn active" data-tab="products">Products</button>
                <button class="tab-btn" data-tab="orders">Orders</button>
                <button class="tab-btn" data-tab="add-product">Add Product</button>
            </div>

            <div class="tab-content active" id="products-tab">
                <h2>Manage Products</h2>
                <div class="products-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
<th>Category</th>
<th>Price</th>
<th>Stock</th>
<th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
<td><?= htmlspecialchars($product['category']) ?></td>
<td><?= formatPrice($product['price'], $product['discount_price']) ?></td>
<td><?= $product['stock'] ?></td>
<td>
                                        <button class="btn btn-small btn-secondary edit-product" data-id="<?= $product['id'] ?>">Edit</button>
                                        <form method="POST" style="display: inline;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                            <input type="hidden" name="action" value="delete_product">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <button type="submit" class="btn btn-small btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                        </form>
                                        <!-- Inline Edit Form (hidden by default) -->
                                        <form method="POST" class="edit-product-form" data-id="<?= $product['id'] ?>" style="display: none; margin-top: 5px;">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                            <input type="hidden" name="action" value="edit_product">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" placeholder="Name" required>
                                            <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>" placeholder="Category" required>
                                            <input type="number" name="price" value="<?= $product['price'] ?>" step="0.01" placeholder="Price" required>
                                            <input type="number" name="discount_price" value="<?= $product['discount_price'] ?>" step="0.01" placeholder="Discount Price">
                                            <input type="number" name="stock" value="<?= $product['stock'] ?>" placeholder="Stock" required>
                                            <input type="text" name="description" value="<?= htmlspecialchars($product['description']) ?>" placeholder="Description" required>
                                            <button type="submit" class="btn btn-small btn-primary">Save</button>
                                            <button type="button" class="btn btn-small btn-secondary cancel-edit">Cancel</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-content" id="orders-tab">
    <h2>Orders</h2>
    <div class="orders-table">
        <table>
            <thead>
                <tr>
                    <th>Customer</th>
<th>Product</th>
<th>Quantity</th>
<th>Price</th>
<th>Status</th>
<th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['customer']) ?></td>
<td><?= htmlspecialchars($order['product_name']) ?></td>
<td><?= $order['quantity'] ?></td>
<td>$<?= number_format($order['purchaseprice'], 2) ?></td>
<td><span class="status status-<?= $order['orderstatus'] ?>"><?= ucfirst($order['orderstatus']) ?></span></td>
<td><?= $order['createdat'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
</div>

            <div class="tab-content" id="add-product-tab">
                <h2>Add New Product</h2>
                <form method="POST" class="product-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="action" value="add_product">
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="product-name">Product Name *</label>
                            <input type="text" id="product-name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="product-category">Category *</label>
                            <select id="product-category" name="category" required>
    <option value="">Select Category</option>
    <?php foreach ($categories as $category): ?>
        <option value="<?= htmlspecialchars($category) ?>"><?= ucfirst(htmlspecialchars($category)) ?></option>
    <?php endforeach; ?>
    <option value="new">Add New Category</option>
</select>
<input type="text" id="new-category-input" name="new_category" placeholder="Enter new category" style="display:none;margin-top:5px;">
                        </div>
                        
                        <div class="form-group">
                            <label for="product-price">Price *</label>
                            <input type="number" id="product-price" name="price" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="product-discount">Discount Price</label>
                            <input type="number" id="product-discount" name="discount_price" step="0.01">
                        </div>
                        
                        <div class="form-group">
                            <label for="product-stock">Stock Quantity *</label>
                            <input type="number" id="product-stock" name="stock" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="product-colors">Colors (comma-separated)</label>
                            <input type="text" id="product-colors" name="colors" placeholder="Red, Blue, Green">
                        </div>
                        
                        <div class="form-group">
                            <label for="product-sizes">Sizes (comma-separated)</label>
                            <input type="text" id="product-sizes" name="sizes" placeholder="S, M, L, XL">
                        </div>
                        
                        <div class="form-group full-width">
                            <label for="product-description">Description *</label>
                            <textarea id="product-description" name="description" required></textarea>
                        </div>
                        
                        <div class="form-group full-width">
    <label for="product-image-filename">Image Filename</label>
    <input type="text" id="product-image-filename" name="image_filename" placeholder="e.g. shoe1.jpg">
    <small>Place your image in the images/ folder and enter the filename here. Example: dummy1.png</small>
</div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Add Product</button>
                </form>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 E-Shop. All rights reserved. | Practice Project for XAMPP</p>
        </div>
    </footer>

    <script src="script.js"></script>
<script>
// Tab switching
$(document).ready(function() {
    $('.tab-btn').click(function() {
        var tab = $(this).data('tab');
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        $('.tab-content').removeClass('active');
        $('#' + tab + '-tab').addClass('active');
    });
    // Edit product inline form
    $('.edit-product').click(function() {
        var id = $(this).data('id');
        $('.edit-product-form').hide();
        $('.edit-product-form[data-id="'+id+'"], .edit-product-form[data-id="'+id+'"] input').show();
    });
    $('.cancel-edit').click(function() {
        $(this).closest('.edit-product-form').hide();
    });
    // Show/hide new category input
    $('#product-category').change(function() {
        if ($(this).val() === 'new') {
            $('#new-category-input').show().prop('required', true);
        } else {
            $('#new-category-input').hide().prop('required', false);
        }
    });
});
</script>
</body>
</html>