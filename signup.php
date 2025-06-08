<?php
require_once 'config.php';

$message = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';    
    $email = $_POST['email'] ?? '';
    $phonenumber = $_POST['phonenumber'] ?? '';
    $address = $_POST['address'] ?? '';

    $passwordconfirm = $_POST['confirmpassword'] ?? '';


    if($passwordconfirm !== $password){
        $message = 'Password does not match';
    }
    

    $userData = [
    'username'    => $username,
    'email'       => $email,
    'phonenumber' => $phonenumber,
    'address'     => $address,
    'password'    => $password
];


if (createUser($userData)) {       
        header("Location: login.php?signup=success");
        exit;
    } else {
        $message = 'Failed to create user. Try again.';
    }

}



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
                    <h2>Signup</h2>                    
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"])?>" method="POST">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phonenumber">Phone Number</label>
                            <input type="tel" id="phonenumber" name="phonenumber" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>

                        <?php if ($message != ''): ?>
                            <p style="color:red;"><php? htmlspecialchars($message) ?></p>
                        <?php endif; ?>
                        
                        <div class="form-group">
                            <label for="password">Password Confirm</label>
                            <input type="password" id="confirmpassword" name="confirmpassword" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-large">Signup</button>
                        <div>
                            <p>already have acount? <a href="login.php">login</a></p>
                        </div>
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