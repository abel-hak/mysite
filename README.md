# MYSite E-Commerce Platform

A full-featured e-commerce website built with PHP, MySQL, and JavaScript, designed to run on XAMPP.

## Quick Start Guide

1. **Prerequisites**
   - XAMPP (Apache + MySQL)
   - Web browser
   - Git (optional, for cloning)

2. **Installation**
   ```bash
   # Clone or download the project to your XAMPP htdocs folder
   C:\xampp\htdocs\MYSite

   # Start XAMPP services
   1. Open XAMPP Control Panel
   2. Start Apache and MySQL services
   ```

3. **Database Setup**
   ```bash
   # 1. Open phpMyAdmin
   http://localhost/phpmyadmin

   # 2. Create database
   - Create new database named 'ecommerce_db'
   - Import ecommerce_db.sql file

   # 3. Configure database connection
   - Open config.php
   - Update credentials if needed:
     DB_HOST = 'localhost'
     DB_USER = 'root'
     DB_PASS = 'your_password'
     DB_NAME = 'ecommerce_db'
   ```

4. **Access the Website**
   ```
   http://localhost/MYSite
   ```

## Features

### Customer Features
- Product browsing and search
- Shopping cart functionality
- Wishlist management
- User registration and authentication
- Order tracking
- Responsive design for all devices

### Admin Features
- Product management (CRUD operations)
- Order management
- User management
- Stock tracking

## Project Structure
```
MYSite/
├── includes/           # Core PHP functions
├── assets/            # Static assets
│   ├── images/        # Product images
│   └── css/          # Stylesheets
├── config.php         # Database configuration
├── index.php         # Homepage
├── product.php       # Product details
├── cart.php         # Shopping cart
├── checkout.php     # Checkout process
├── login.php        # User authentication
└── admin.php        # Admin dashboard
```

## Database Schema

The database consists of four main tables:
- `users`: User accounts and roles
- `products`: Product information
- `orders`: Order tracking
- `wishlist`: User wishlists

## Security Features
- Password hashing
- SQL injection prevention
- CSRF protection
- Input validation
- Secure session management

## Troubleshooting

1. **Database Connection Issues**
   - Verify XAMPP services are running
   - Check database credentials in config.php
   - Ensure database 'ecommerce_db' exists

2. **Image Loading Issues**
   - Check file permissions in images directory
   - Verify image paths in database

3. **Access Issues**
   - Make sure Apache is running
   - Check file permissions
   - Verify URL path is correct

## Contributing

This is a practice project. Feel free to fork and modify for learning purposes.

## License

This project is for educational purposes only.

## Support

For issues and questions, please check:
1. Code comments
2. Database schema in ecommerce_db.sql
3. Configuration in config.php
