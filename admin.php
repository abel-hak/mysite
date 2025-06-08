<?php
require_once 'config.php';

// Check if user is admin
if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add_product':
                // TODO: Add product to database
                // $pdo = getConnection();
                // $stmt = $pdo->prepare("INSERT INTO products (name, description, price, discount_price, stock, category, variants) VALUES (?, ?, ?, ?, ?, ?, ?)");
                // $variants = json_encode(['colors' => explode(',', $_POST['colors']), 'sizes' => explode(',', $_POST['sizes'])]);
                // $stmt->execute([$_POST['name'], $_POST['description'], $_POST['price'], $_POST['discount_price'], $_POST['stock'], $_POST['category'], $variants]);
                $message = 'Product would be added to database (demo mode)';
                break;
                
            case 'delete_product':
                // TODO: Delete product from database
                // $pdo = getConnection();
                // $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
                // $stmt->execute([$_POST['product_id']]);
                $message = 'Product would be deleted from database (demo mode)';
                break;
        }
    }
}

$products = getProducts();
$categories = getCategories();

// Sample orders (in production, these would come from database)
$orders = [
    ['id' => 1, 'customer' => 'John Doe', 'total' => 89.99, 'status' => 'completed', 'date' => '2025-01-15'],
    ['id' => 2, 'customer' => 'Jane Smith', 'total' => 64.98, 'status' => 'pending', 'date' => '2025-01-16']
];
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
                                <th>ID</th>
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
                                    <td><?= $product['id'] ?></td>
                                    <td><?= htmlspecialchars($product['name']) ?></td>
                                    <td><?= htmlspecialchars($product['category']) ?></td>
                                    <td><?= formatPrice($product['price'], $product['discount_price']) ?></td>
                                    <td><?= $product['stock'] ?></td>
                                    <td>
                                        <button class="btn btn-small btn-secondary edit-product" data-id="<?= $product['id'] ?>">Edit</button>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="delete_product">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <button type="submit" class="btn btn-small btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
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
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['customer']) ?></td>
                                    <td>$<?= number_format($order['total'], 2) ?></td>
                                    <td><span class="status status-<?= $order['status'] ?>"><?= ucfirst($order['status']) ?></span></td>
                                    <td><?= $order['date'] ?></td>
                                    <td>
                                        <button class="btn btn-small btn-secondary">View Details</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="tab-content" id="add-product-tab">
                <h2>Add New Product</h2>
                <form method="POST" class="product-form">
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
                            <label for="product-image">Product Image</label>
                            <input type="file" id="product-image" name="image" accept="image/*">
                            <small>Note: Image upload is simulated in this demo</small>
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
</body>
</html>