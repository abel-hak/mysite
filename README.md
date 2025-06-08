# E-Shop - XAMPP E-commerce Practice Project

A complete e-commerce website built for XAMPP local development using vanilla PHP, MySQL, HTML, CSS, jQuery, and JavaScript. This project is designed for practice and learning purposes.

## Features

### Customer Features
- **Product Catalog**: Browse products with search and category filtering
- **Product Details**: View detailed product information with variants and reviews
- **Shopping Cart**: Add/remove items, update quantities, apply discount codes
- **Checkout Process**: Multi-step checkout with shipping and payment options
- **Wishlist**: Save favorite products using localStorage
- **Responsive Design**: Works on mobile, tablet, and desktop

### Admin Features
- **Dashboard**: Manage products and view orders
- **Product Management**: Add, edit, and delete products
- **Order Management**: View and manage customer orders
- **Category Management**: Create and manage product categories

## Installation

### Prerequisites
- XAMPP installed on your computer
- Web browser

### Setup Instructions

1. **Start XAMPP**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

2. **Install the Project**
   - Copy all project files to your XAMPP `htdocs` folder
   - Example: `C:\xampp\htdocs\eshop\`

3. **Create Database**
   - Open phpMyAdmin in your browser: `http://localhost/phpmyadmin`
   - Import the `database.sql` file to create the database and sample data

4. **Access the Website**
   - Open your browser and go to: `http://localhost/eshop/`

## Demo Accounts

### Customer Account
- **Username**: customer
- **Password**: customer123

### Admin Account
- **Username**: admin  
- **Password**: admin123

## Project Structure

```
eshop/
├── index.php              # Homepage with product catalog
├── product.php            # Product detail page
├── cart.php              # Shopping cart page
├── checkout.php          # Multi-step checkout process
├── login.php             # Login/logout functionality
├── admin.php             # Admin dashboard
├── config.php            # Database configuration and helper functions
├── style.css             # Main stylesheet (responsive design)
├── script.js             # JavaScript functionality (jQuery)
├── database.sql          # Database structure and sample data
└── README.md             # Project documentation
```

## Technologies Used

- **Backend**: PHP 7.4+, MySQL
- **Frontend**: HTML5, CSS3, JavaScript (ES6+), jQuery 3.6
- **Database**: MySQL with PDO
- **Design**: Custom CSS with Flexbox/Grid (no frameworks)
- **Local Development**: XAMPP

## Key Features Explained

### Responsive Design
- Mobile-first approach with breakpoints at 768px and 1024px
- CSS Grid and Flexbox for layout
- Optimized for all screen sizes

### Shopping Cart
- LocalStorage-based cart management
- Real-time quantity updates
- Discount code system
- Cart persistence across sessions

### Database Integration
- PDO for secure database connections
- Prepared statements for security
- Sample data included for testing

### Admin Panel
- Product CRUD operations
- Order management system
- Category management
- Form validation

## Discount Codes (Demo)

- **SAVE10**: $10 off
- **WELCOME**: $5 off  
- **STUDENT**: $15 off

## Security Notes

This is a practice project and includes simplified authentication. For production use, implement:
- Password hashing
- CSRF protection
- Input sanitization
- Session security
- File upload validation

## Browser Compatibility

- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Development Notes

- All images use Pexels placeholder URLs
- Cart and wishlist use localStorage
- Order processing is simulated (demo mode)
- Database operations include placeholder comments for expansion

## Troubleshooting

### Common Issues

1. **Database Connection Error**
   - Ensure MySQL is running in XAMPP
   - Check database name and credentials in `config.php`

2. **Images Not Loading**
   - Check internet connection (images load from Pexels)
   - Images are for demonstration only

3. **JavaScript Errors**
   - Ensure jQuery CDN is accessible
   - Check browser console for errors

## Future Enhancements

- User registration system
- Email notifications
- Payment gateway integration
- Inventory management
- Product reviews system
- Advanced search filters

## License

This project is for educational purposes only. Feel free to modify and use for learning.

## Support

This is a practice project. For learning purposes, refer to the code comments and documentation.