<?php
require_once '../config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start or resume session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

// Log function
function logDebug($message, $data = []) {
    error_log(date('Y-m-d H:i:s') . ' - ' . $message . ' - ' . json_encode($data));
}

logDebug('Request received', [
    'method' => $_SERVER['REQUEST_METHOD'],
    'get' => $_GET,
    'post' => $_POST,
    'session' => $_SESSION
]);

// Handle GET requests (checking status and count)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset($_GET['action'])) {
        if ($_GET['action'] === 'check' && isset($_GET['product_id'])) {
            $inWishlist = isInWishlist($_GET['product_id']);
            logDebug('Check wishlist status', ['product_id' => $_GET['product_id'], 'inWishlist' => $inWishlist]);
            echo json_encode(['inWishlist' => $inWishlist]);
            exit();
        } else if ($_GET['action'] === 'count') {
            $wishlist = getWishlist();
            $count = count($wishlist);
            logDebug('Get wishlist count', ['count' => $count]);
            echo json_encode(['count' => $count]);
            exit();
        }
    }
    logDebug('Invalid GET request');
    echo json_encode(['error' => 'Invalid request']);
    exit();
}

// Handle POST requests (add/remove)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['action']) || !isset($_POST['product_id'])) {
        logDebug('Invalid POST request - missing parameters');
        echo json_encode(['error' => 'Invalid request - missing parameters']);
        exit();
    }

    if (!isLoggedIn()) {
        logDebug('User not logged in');
        echo json_encode(['error' => 'Please login to manage wishlist']);
        exit();
    }

    $action = $_POST['action'];
    $productId = $_POST['product_id'];

    logDebug('Processing wishlist action', [
        'action' => $action,
        'product_id' => $productId,
        'user' => $_SESSION['user']
    ]);

    if ($action === 'add') {
        $result = addToWishlist($productId);
    } else if ($action === 'remove') {
        $result = removeFromWishlist($productId);
    } else {
        $result = ['error' => 'Invalid action'];
    }

    logDebug('Action result', $result);
    echo json_encode($result);
    exit();
}

logDebug('Invalid request method');
echo json_encode(['error' => 'Invalid request method']);
?>
