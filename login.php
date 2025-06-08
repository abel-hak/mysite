<?php
require_once 'config.php';

$message = '';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    

    $user = getUser($username);
    echo '<pre>'; print_r($user); echo '</pre>';

    if ($user && $password === $user['pass']) {
        $_SESSION['user'] = ['username' => $username, 'role' => $user['role']];
        
        if ($user['role'] === 'admin') {
            header('Location: admin.php');
        } else {
            header('Location: index.php');
        }
        exit;
    } else {
        $message = 'Invalid username or password';
    }
}


// if ($user && password_verify($password, $user['pass'])) {
//     // Login success: set session, redirect
// } else {
//     $message = 'Invalid username or password';
// }

if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - E-Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <div class="nav-brand">
                <h1><a href="index.php">E-Shop</a></h1>
            </div>
            <nav class="nav-menu">
                <a href="index.php">Home</a>
                <a href="cart.php">Cart</a>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <div class="login-container">
                <div class="login-form">
                    <h2>Login</h2>
                    
                    <?php if ($message): ?>
                        <div class="alert alert-error"><?= htmlspecialchars($message) ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-large">Login</button>
                    </form>
                    
                </div>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 E-Shop. All rights reserved. | Practice Project for XAMPP</p>
        </div>
    </footer>
</body>
</html>