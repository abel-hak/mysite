<?php
require_once __DIR__ . '/../config.php';
$cartCount = getUserCartCount();
?>
<header class="header">
    <div class="container">
        <div class="nav-brand">
            <h1><a href="index.php">E-Shop</a></h1>
        </div>
        <nav class="nav-menu">
            <a href="index.php">Home</a>
            <a href="cart.php" <?= basename($_SERVER['PHP_SELF']) === 'cart.php' ? 'class="active"' : '' ?>>Cart (<span id="cart-count"><?= $cartCount ?></span>)</a>
            <a href="wishlist.php" <?= basename($_SERVER['PHP_SELF']) === 'wishlist.php' ? 'class="active"' : '' ?>>Wishlist (<span id="wishlist-count">0</span>)</a>
            <?php if (isLoggedIn()): ?>
                <?php if (isAdmin()): ?>
                    <a href="admin.php" <?= basename($_SERVER['PHP_SELF']) === 'admin.php' ? 'class="active"' : '' ?>>Admin</a>
                <?php endif; ?>
                <a href="login.php?action=logout">Logout</a>
            <?php else: ?>
                <a href="login.php" <?= basename($_SERVER['PHP_SELF']) === 'login.php' ? 'class="active"' : '' ?>>Login</a>
                <a href="signup.php" <?= basename($_SERVER['PHP_SELF']) === 'signup.php' ? 'class="active"' : '' ?>>Signup</a>
            <?php endif; ?>
        </nav>
        <div class="nav-toggle">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</header>
