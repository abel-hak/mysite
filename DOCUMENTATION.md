# MYSite E-Commerce Technical Documentation

## Table of Contents
1. [System Architecture](#system-architecture)
2. [Core Components](#core-components)
3. [Database Structure](#database-structure)
4. [Authentication System](#authentication-system)
5. [Shopping Cart Implementation](#shopping-cart-implementation)
6. [Checkout Process](#checkout-process)
7. [Wishlist System](#wishlist-system)
8. [Security Features](#security-features)

## System Architecture

### Technology Stack
- **Backend**: PHP 7.4+
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Server**: Apache (XAMPP)

### Directory Structure
```
MYSite/
├── includes/           # Core PHP functions and handlers
│   ├── header.php     # Common header and navigation
│   └── wishlist_handler.php  # Wishlist functionality
├── images/            # Product images
├── config.php        # Database and system configuration
├── *.php files       # Main application pages
└── *.js files        # Frontend functionality
```

## Core Components

### 1. Configuration (config.php)
```php
// Database connection
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'your_password');
define('DB_NAME', 'ecommerce_db');

// Core functions:
- getConnection(): Establishes database connection
- validate_*(): Input validation functions
- generate_csrf_token(): Security token generation
```

### 2. User Management
- **Registration (signup.php)**
  - Validates user input
  - Hashes passwords using PASSWORD_DEFAULT
  - Creates user record in database
  
- **Authentication (login.php)**
  - Verifies credentials
  - Creates session
  - Manages user roles (admin/customer)

### 3. Product Management (product.php)
- Product display
- Category filtering
- Search functionality
- Stock management
- Price calculations with discounts

## Database Structure

### 1. Users Table
```sql
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  username VARCHAR(255),
  email VARCHAR(255),
  pwd VARCHAR(255),
  phone VARCHAR(20),
  useraddress TEXT,
  role ENUM('admin', 'customer'),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### 2. Products Table
```sql
CREATE TABLE products (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255),
  price INT,
  category VARCHAR(255),
  description TEXT,
  image VARCHAR(255),
  stock INT DEFAULT 2,
  discount_price INT DEFAULT 100
);
```

### 3. Orders Table
```sql
CREATE TABLE orders (
  id INT PRIMARY KEY AUTO_INCREMENT,
  userid INT,
  productid INT,
  quantity INT,
  purchaseprice DECIMAL(10,2),
  createdat TIMESTAMP,
  updatedat TIMESTAMP,
  orderstatus VARCHAR(20) DEFAULT 'pending',
  FOREIGN KEY (userid) REFERENCES users(id),
  FOREIGN KEY (productid) REFERENCES products(id)
);
```

### 4. Wishlist Table
```sql
CREATE TABLE wishlist (
  id INT PRIMARY KEY AUTO_INCREMENT,
  user_id INT,
  product_id INT,
  created_at TIMESTAMP,
  UNIQUE KEY unique_wishlist_item (user_id, product_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
);
```

## Authentication System

### Login Process
1. User submits credentials
2. System validates input
3. Passwords are verified using password_verify()
4. Session is created with user data
5. Role-based access control is implemented

```php
function authenticateUser($username, $password) {
    $user = getUser($username);
    if ($user && verifyPassword($password, $user['pwd'])) {
        $_SESSION['user'] = [
            'username' => $user['username'],
            'role' => $user['role']
        ];
        return true;
    }
    return false;
}
```

## Shopping Cart Implementation

### Cart Enhancement (cart-enhance.js)
- Real-time quantity updates
- Price calculations
- Stock validation
- Session management

### Cart Functions
```php
function getUserCartCount() {
    // Returns total items in cart
    $userid = getUser($_SESSION['user']['username'])['id'];
    $sql = "SELECT SUM(quantity) as total_items 
            FROM orders 
            WHERE userid = :userid 
            AND orderstatus = 'pending'";
}

function getCartTotal($userId) {
    // Calculates cart totals including discounts
    // Returns subtotal, item count, shipping, total
}
```

## Checkout Process

### Checkout Flow (checkout-enhance.js)
1. Cart validation
2. Address collection
3. Order creation
4. Payment processing
5. Order confirmation

```javascript
// Example checkout process
async function processCheckout(orderData) {
    // Validate stock
    // Create order
    // Process payment
    // Update inventory
    // Send confirmation
}
```

## Wishlist System

### Implementation (wishlist_handler.php)
- Add/remove items
- Check item existence
- List management
- User-specific lists

```php
function addToWishlist($productId) {
    // Adds product to user's wishlist
    // Handles duplicates
    // Returns status
}

function getWishlist() {
    // Returns user's wishlist items
    // Includes product details
}
```

## Security Features

### 1. SQL Injection Prevention
- Prepared statements
- Parameter binding
- Input validation

### 2. XSS Protection
- Output escaping
- Content Security Policy
- Input sanitization

### 3. CSRF Protection
```php
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
```

### 4. Session Security
- Secure session configuration
- Session timeout
- Session fixation prevention

## Error Handling

### Common Scenarios
1. Database connection failures
2. Invalid input data
3. Authentication errors
4. Stock unavailability
5. Payment processing errors

```php
try {
    // Operation code
} catch (PDOException $e) {
    error_log('Database error: ' . $e->getMessage());
    return ['error' => 'System error occurred'];
}
```

## Performance Optimizations

1. **Database Indexing**
   - Primary keys
   - Foreign keys
   - Composite indexes on frequently queried columns

2. **Query Optimization**
   - Proper JOIN usage
   - Indexed columns in WHERE clauses
   - Limited result sets

3. **Caching**
   - Session-based cart caching
   - Product image caching
   - Database query results caching

## Testing

### Key Test Areas
1. User registration and login
2. Shopping cart operations
3. Checkout process
4. Order management
5. Wishlist functionality
6. Admin operations

## Maintenance

### Regular Tasks
1. Database backup
2. Log rotation
3. Session cleanup
4. Image optimization
5. Security updates
