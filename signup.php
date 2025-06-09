<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'config.php';



$message = '';
$validation_errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF check
    if (!isset($_POST['csrf_token']) || !check_csrf_token($_POST['csrf_token'])) {
        $message = 'Invalid CSRF token. Please reload and try again.';
    } else {
        // Input validation
        $username = validate_string($_POST['username'] ?? '', 3, 30);
        $password = $_POST['password'] ?? '';
        $passwordconfirm = $_POST['confirmpassword'] ?? '';
        $email = validate_email($_POST['email'] ?? '');
        $phonenumber = $_POST['phonenumber'] ?? '';
// Phone must be 10 digits, start with 09 or 07, and contain only numbers
if (!preg_match('/^(09|07)[0-9]{8}$/', $phonenumber)) {
    $validation_errors['phonenumber'] = 'Phone number must be 10 digits, start with 09 or 07, and contain only numbers.';
}
        $address = validate_string($_POST['address'] ?? '', 3, 50);

        if (!$username) {
            $validation_errors['username'] = 'Please enter a valid username.';
        } elseif (userExists($username)) {
            $validation_errors['username'] = 'This username is already taken.';
        }
        if (!$email) {
            $validation_errors['email'] = 'Please enter a valid email.';
        }
        if (!$phonenumber) {
            $validation_errors['phonenumber'] = 'Please enter a valid phone number.';
        }
        if (!$address) {
            $validation_errors['address'] = 'Please enter a valid address.';
        }
        if ($password !== $passwordconfirm) {
            $validation_errors['password'] = 'Password does not match.';
        }
        if (strlen($password) < 6) {
            $validation_errors['password'] = 'Password must be at least 6 characters.';
        }

        if (empty($validation_errors)) {
            $userData = [
                'username'    => $username,
                'email'       => $email,
                'phone'       => $phonenumber,
                'useraddress' => $address,
                'password'    => $password
            ];
            if (createUser($userData)) {
                header("Location: login.php?signup=success");
                exit;
            } else {
                $message = 'Failed to create user. Try again.';
                global $pdo;
                if (isset($pdo)) {
                    $errorInfo = $pdo->errorInfo();
                    $message .= '<br>SQL Error: ' . htmlspecialchars(print_r($errorInfo, true));
                }
            }
        }
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
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                            <?php if (isset($validation_errors['username'])): ?>
                                <p style="color:red;"><?= $validation_errors['username'] ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                            <?php if (isset($validation_errors['email'])): ?>
                                <p style="color:red;"><?= $validation_errors['email'] ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="phonenumber">Phone Number</label>
                            <input type="tel" id="phonenumber" name="phonenumber" required value="<?= isset($_POST['phonenumber']) ? htmlspecialchars($_POST['phonenumber']) : '' ?>">
                            <?php if (isset($validation_errors['phonenumber'])): ?>
                                <p style="color:red;"><?= $validation_errors['phonenumber'] ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" required value="<?= isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '' ?>">
                            <?php if (isset($validation_errors['address'])): ?>
                                <p style="color:red;"><?= $validation_errors['address'] ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                            <?php if (isset($validation_errors['password'])): ?>
                                <p style="color:red;"><?= $validation_errors['password'] ?></p>
                            <?php endif; ?>
                        </div>
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