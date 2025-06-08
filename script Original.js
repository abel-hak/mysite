$(document).ready(function() {
    // Initialize application
    initializeApp();
    
    // Mobile navigation toggle
    $('.nav-toggle').click(function() {
        $('.nav-menu').toggleClass('active');
    });
    
    // Cart functionality
    initializeCart();
    
    // Wishlist functionality
    initializeWishlist();
    
    // Admin functionality
    if (window.location.pathname.includes('admin.php')) {
        initializeAdmin();
    }
    
    // Checkout functionality
    if (window.location.pathname.includes('checkout.php')) {
        initializeCheckout();
    }
    
    // Product page functionality
    if (window.location.pathname.includes('product.php')) {
        initializeProductPage();
    }
});

// Initialize application
function initializeApp() {
    updateCartCount();
    updateWishlistCount();
    
    // Add smooth scrolling to anchor links
    $('a[href^="#"]').click(function(e) {
        e.preventDefault();
        const target = $(this.hash);
        if (target.length) {
            $('html, body').animate({
                scrollTop: target.offset().top - 80
            }, 500);
        }
    });
    
    // Add loading animation to forms
    $('form').submit(function() {
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.prop('disabled', true).text('Loading...');
        
        setTimeout(() => {
            submitBtn.prop('disabled', false).text(submitBtn.data('original-text') || 'Submit');
        }, 2000);
    });
}

// Cart Management
function initializeCart() {
    // Load cart from localStorage
    loadCart();
    
    // Add to cart button clicks
    $(document).on('click', '.add-to-cart', function(e) {
        e.preventDefault();
        
        const productId = $(this).data('id');
        const productName = $(this).data('name');
        const productPrice = parseFloat($(this).data('price'));
        
        // Get variants if on product page
        let variants = {};
        if ($('#product-form').length) {
            const colorSelect = $('select[name="color"]');
            const sizeSelect = $('select[name="size"]');
            const quantityInput = $('input[name="quantity"]');
            
            // Validate required variants
            if (colorSelect.length && colorSelect.attr('required') && !colorSelect.val()) {
                alert('Please select a color');
                return;
            }
            
            if (sizeSelect.length && sizeSelect.attr('required') && !sizeSelect.val()) {
                alert('Please select a size');
                return;
            }
            
            variants.color = colorSelect.val() || '';
            variants.size = sizeSelect.val() || '';
            variants.quantity = parseInt(quantityInput.val()) || 1;
        } else {
            variants.quantity = 1;
        }
        
        addToCart(productId, productName, productPrice, variants);
        
        // Show success animation
        showCartSuccess($(this));
    });
    
    // Remove from cart
    $(document).on('click', '.remove-item', function() {
        const productId = $(this).data('id');
        removeFromCart(productId);
    });
    
    // Update quantity
    $(document).on('change', '.quantity-input', function() {
        const productId = $(this).data('id');
        const newQuantity = parseInt($(this).val());
        updateCartQuantity(productId, newQuantity);
    });
    
    // Apply discount code
    $('#apply-discount').click(function() {
        const discountCode = $('#discount-input').val().trim();
        applyDiscountCode(discountCode);
    });
}

function addToCart(productId, productName, productPrice, variants = {}) {
    let cart = getCart();
    
    // Create unique cart item ID including variants
    const cartItemId = productId + '_' + (variants.color || '') + '_' + (variants.size || '');
    
    const existingItem = cart.find(item => item.cartItemId === cartItemId);
    
    if (existingItem) {
        existingItem.quantity += variants.quantity || 1;
    } else {
        cart.push({
            id: productId,
            cartItemId: cartItemId,
            name: productName,
            price: productPrice,
            quantity: variants.quantity || 1,
            color: variants.color || '',
            size: variants.size || ''
        });
    }
    
    saveCart(cart);
    updateCartCount();
    updateCartDisplay();
    
    // Show notification
    showNotification('Product added to cart!', 'success');
}

function removeFromCart(cartItemId) {
    let cart = getCart();
    cart = cart.filter(item => item.cartItemId !== cartItemId);
    saveCart(cart);
    updateCartCount();
    updateCartDisplay();
    showNotification('Product removed from cart', 'info');
}

function updateCartQuantity(cartItemId, newQuantity) {
    if (newQuantity <= 0) {
        removeFromCart(cartItemId);
        return;
    }
    
    let cart = getCart();
    const item = cart.find(item => item.cartItemId === cartItemId);
    
    if (item) {
        item.quantity = newQuantity;
        saveCart(cart);
        updateCartDisplay();
    }
}

function getCart() {
    return JSON.parse(localStorage.getItem('cart') || '[]');
}

function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
}

function updateCartCount() {
    const cart = getCart();
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    $('#cart-count').text(totalItems);
}

function loadCart() {
    updateCartDisplay();
}   

function updateCartDisplay() {
    if (!$('#cart-content').length) return;
    
    const cart = getCart();
    const cartContent = $('#cart-content');
    const checkoutBtn = $('#checkout-btn');
    
    if (cart.length === 0) {
        cartContent.html(`
            <div class="empty-cart">
                <p>Your cart is empty</p>
                <a href="index.php" class="btn btn-primary">Continue Shopping</a>
            </div>
        `);
        checkoutBtn.hide();
        updateCartSummary([], 0, 0);
        return;
    }
    
    let cartHtml = '';
    cart.forEach(item => {
        const itemTotal = item.price * item.quantity;
        const variantText = (item.color || item.size) ? 
            `<small>${item.color ? 'Color: ' + item.color : ''} ${item.size ? 'Size: ' + item.size : ''}</small>` : '';
        
        cartHtml += `
            <div class="cart-item">
                <div class="cart-item-image">
                    <img src="https://images.pexels.com/photos/607812/pexels-photo-607812.jpeg?auto=compress&cs=tinysrgb&w=80&h=80&fit=crop" alt="${item.name}">
                </div>
                <div class="cart-item-info">
                    <div class="cart-item-name">${item.name}</div>
                    ${variantText}
                    <div class="cart-item-price">$${item.price.toFixed(2)}</div>
                    <div class="cart-item-controls">
                        <input type="number" class="quantity-input" value="${item.quantity}" min="1" data-id="${item.cartItemId}">
                        <button class="remove-item" data-id="${item.cartItemId}">Remove</button>
                    </div>
                </div>
                <div class="cart-item-total">
                    $${itemTotal.toFixed(2)}
                </div>
            </div>
        `;
    });
    
    cartContent.html(cartHtml);
    checkoutBtn.show();
    
    // Calculate totals
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const shipping = subtotal > 50 ? 0 : 5.99; // Free shipping over $50
    
    updateCartSummary(cart, subtotal, shipping);
}

function updateCartSummary(cart, subtotal, shipping) {
    const discountAmount = parseFloat($('#discount').text().replace(/[$-]/g, '')) || 0;
    const total = subtotal + shipping - discountAmount;
    
    $('#subtotal').text('$' + subtotal.toFixed(2));
    $('#shipping').text(shipping === 0 ? 'Free' : '$' + shipping.toFixed(2));
    $('#total').text('$' + total.toFixed(2));
    
    // Update checkout totals if on checkout page
    if ($('#checkout-subtotal').length) {
        updateCheckoutSummary(cart, subtotal, shipping, discountAmount);
    }
}

function applyDiscountCode(code) {
    const validCodes = {
        'SAVE10': 10,
        'WELCOME': 5,
        'STUDENT': 15
    };
    
    if (validCodes[code.toUpperCase()]) {
        const discountAmount = validCodes[code.toUpperCase()];
        $('#discount').text('-$' + discountAmount.toFixed(2));
        $('.discount-section').show();
        $('#discount-input').val('');
        showNotification(`Discount code applied! You saved $${discountAmount}`, 'success');
        
        // Recalculate totals
        const subtotal = parseFloat($('#subtotal').text().replace('$', ''));
        const shipping = $('#shipping').text() === 'Free' ? 0 : parseFloat($('#shipping').text().replace('$', ''));
        updateCartSummary(getCart(), subtotal, shipping);
    } else {
        showNotification('Invalid discount code', 'error');
    }
}

// Wishlist Management
function initializeWishlist() {
    updateWishlistDisplay();
    
    $(document).on('click', '.wishlist-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const productId = $(this).data('id');
        toggleWishlist(productId);
        $(this).toggleClass('active');
    });
    
    $('#wishlist-link').click(function(e) {
        e.preventDefault();
        showWishlistModal();
    });
}

function toggleWishlist(productId) {
    let wishlist = getWishlist();
    
    if (wishlist.includes(productId)) {
        wishlist = wishlist.filter(id => id !== productId);
        showNotification('Removed from wishlist', 'info');
    } else {
        wishlist.push(productId);
        showNotification('Added to wishlist!', 'success');
    }
    
    saveWishlist(wishlist);
    updateWishlistCount();
    updateWishlistDisplay();
}

function getWishlist() {
    return JSON.parse(localStorage.getItem('wishlist') || '[]');
}

function saveWishlist(wishlist) {
    localStorage.setItem('wishlist', JSON.stringify(wishlist));
}

function updateWishlistCount() {
    const wishlist = getWishlist();
    $('#wishlist-count').text(wishlist.length);
}

function updateWishlistDisplay() {
    const wishlist = getWishlist();
    $('.wishlist-btn').each(function() {
        const productId = $(this).data('id');
        if (wishlist.includes(productId.toString())) {
            $(this).addClass('active');
        } else {
            $(this).removeClass('active');
        }
    });
}

function showWishlistModal() {
    const wishlist = getWishlist();
    
    if (wishlist.length === 0) {
        showNotification('Your wishlist is empty', 'info');
        return;
    }
    
    // In a real app, you'd fetch product details and show them
    alert(`You have ${wishlist.length} items in your wishlist:\n` + wishlist.join(', '));
}

// Checkout Management
function initializeCheckout() {
    let currentStep = 1;
    const totalSteps = 4;
    
    // Load cart items in checkout
    updateCheckoutItems();
    
    // Step navigation
    $('#next-btn').click(function() {
        if (validateCurrentStep(currentStep)) {
            if (currentStep < totalSteps) {
                currentStep++;
                showStep(currentStep);
            }
        }
    });
    
    $('#prev-btn').click(function() {
        if (currentStep > 1) {
            currentStep--;
            showStep(currentStep);
        }
    });
    
    // Shipping method change
    $('input[name="shipping"]').change(function() {
        updateShippingCost();
    });
    
    // Form submission
    $('#checkout-form').submit(function(e) {
        e.preventDefault();
        processOrder();
    });
    
    function showStep(step) {
        // Update step indicators
        $('.step').removeClass('active');
        $(`.step[data-step="${step}"]`).addClass('active');
        
        // Update step content
        $('.step-content').removeClass('active');
        $(`.step-content[data-step="${step}"]`).addClass('active');
        
        // Update navigation buttons
        $('#prev-btn').toggle(step > 1);
        $('#next-btn').toggle(step < totalSteps);
        $('#place-order-btn').toggle(step === totalSteps);
        
        // Update review section on last step
        if (step === totalSteps) {
            updateOrderReview();
        }
    }
    
    function validateCurrentStep(step) {
        const currentStepContent = $(`.step-content[data-step="${step}"]`);
        const requiredFields = currentStepContent.find('input[required], select[required], textarea[required]');
        
        let isValid = true;
        requiredFields.each(function() {
            if (!$(this).val()) {
                $(this).focus();
                showNotification('Please fill in all required fields', 'error');
                isValid = false;
                return false;
            }
        });
        
        return isValid;
    }
    
    function updateCheckoutItems() {
        const cart = getCart();
        const checkoutItems = $('#checkout-items');
        
        if (cart.length === 0) {
            window.location.href = 'cart.php';
            return;
        }
        
        let itemsHtml = '';
        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            itemsHtml += `
                <div class="checkout-item">
                    <span class="checkout-item-name">${item.name} (${item.quantity}x)</span>
                    <span class="checkout-item-price">$${itemTotal.toFixed(2)}</span>
                </div>
            `;
        });
        
        checkoutItems.html(itemsHtml);
        
        // Calculate totals
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        updateCheckoutSummary(cart, subtotal, 0, 0);
    }
    
    function updateCheckoutSummary(cart, subtotal, shipping, discount) {
        const total = subtotal + shipping - discount;
        
        $('#checkout-subtotal').text('$' + subtotal.toFixed(2));
        $('#checkout-shipping').text(shipping === 0 ? 'Free' : '$' + shipping.toFixed(2));
        $('#checkout-total').text('$' + total.toFixed(2));
    }
    
    function updateShippingCost() {
        const selectedShipping = $('input[name="shipping"]:checked').val();
        let shippingCost = 0;
        
        switch (selectedShipping) {
            case 'express':
                shippingCost = 9.99;
                break;
            case 'overnight':
                shippingCost = 19.99;
                break;
            default:
                shippingCost = 0;
        }
        
        const cart = getCart();
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        updateCheckoutSummary(cart, subtotal, shippingCost, 0);
    }
    
    function updateOrderReview() {
        const formData = new FormData(document.getElementById('checkout-form'));
        
        // Review items
        const cart = getCart();
        let reviewItemsHtml = '<ul>';
        cart.forEach(item => {
            reviewItemsHtml += `<li>${item.name} (${item.quantity}x) - $${(item.price * item.quantity).toFixed(2)}</li>`;
        });
        reviewItemsHtml += '</ul>';
        $('#review-items').html(reviewItemsHtml);
        
        // Review shipping info
        const shippingHtml = `
            <p><strong>Name:</strong> ${formData.get('name')}</p>
            <p><strong>Email:</strong> ${formData.get('email')}</p>
            <p><strong>Phone:</strong> ${formData.get('phone') || 'Not provided'}</p>
            <p><strong>Address:</strong> ${formData.get('address')}</p>
            <p><strong>Shipping Method:</strong> ${formData.get('shipping')}</p>
            <p><strong>Payment Method:</strong> ${formData.get('payment')}</p>
        `;
        $('#review-shipping').html(shippingHtml);
        
        // Review summary
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const shippingCost = $('input[name="shipping"]:checked').val() === 'express' ? 9.99 : 
                            $('input[name="shipping"]:checked').val() === 'overnight' ? 19.99 : 0;
        const total = subtotal + shippingCost;
        
        const summaryHtml = `
            <p>Subtotal: $${subtotal.toFixed(2)}</p>
            <p>Shipping: $${shippingCost.toFixed(2)}</p>
            <p><strong>Total: $${total.toFixed(2)}</strong></p>
        `;
        $('#review-summary').html(summaryHtml);
    }
    
    function processOrder() {
        const formData = new FormData(document.getElementById('checkout-form'));
        const cart = getCart();
        
        // Add cart items to form data
        formData.append('items', JSON.stringify(cart));
        
        // Calculate total
        const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
        const shippingCost = $('input[name="shipping"]:checked').val() === 'express' ? 9.99 : 
                            $('input[name="shipping"]:checked').val() === 'overnight' ? 19.99 : 0;
        const total = subtotal + shippingCost;
        formData.append('total', total.toFixed(2));
        
        // Simulate order processing
        $.ajax({
            url: 'checkout.php',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.success) {
                        // Clear cart
                        localStorage.removeItem('cart');
                        updateCartCount();
                        
                        // Show success modal
                        $('#order-number').text(result.order_id);
                        $('#order-modal').addClass('active');
                    }
                } catch (e) {
                    showNotification('Order placed successfully! (Demo mode)', 'success');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                }
            },
            error: function() {
                showNotification('Error processing order. Please try again.', 'error');
            }
        });
    }
}

// Product Page Management
function initializeProductPage() {
    // Quantity controls
    $('.quantity-input').each(function() {
        const input = $(this);
        const max = parseInt(input.attr('max')) || 999;
        
        input.on('change', function() {
            let value = parseInt($(this).val());
            if (isNaN(value) || value < 1) value = 1;
            if (value > max) value = max;
            $(this).val(value);
        });
    });
}

// Admin Management
function initializeAdmin() {
    // Tab functionality
    $('.tab-btn').click(function() {
        const targetTab = $(this).data('tab');
        
        $('.tab-btn').removeClass('active');
        $(this).addClass('active');
        
        $('.tab-content').removeClass('active');
        $(`#${targetTab}-tab`).addClass('active');
    });
    
    // Product form handling
    $('#product-category').change(function() {
        if ($(this).val() === 'new') {
            const newCategory = prompt('Enter new category name:');
            if (newCategory) {
                $(this).append(`<option value="${newCategory}" selected>${newCategory}</option>`);
            } else {
                $(this).val('');
            }
        }
    });
    
    // Edit product functionality (placeholder)
    $('.edit-product').click(function() {
        const productId = $(this).data('id');
        showNotification('Edit functionality would open here (demo mode)', 'info');
    });
}

// Utility Functions
function showNotification(message, type = 'info') {
    const notification = $(`
        <div class="notification notification-${type}">
            ${message}
        </div>
    `);
    
    $('body').append(notification);
    
    // Trigger animation
    setTimeout(() => {
        notification.addClass('show');
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.removeClass('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

function showCartSuccess(button) {
    const originalText = button.text();
    button.text('Added!').addClass('btn-success');
    
    setTimeout(() => {
        button.text(originalText).removeClass('btn-success');
    }, 1500);
}

// Add notification styles dynamically
$('<style>').text(`
    .notification {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 16px 24px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        transform: translateX(100%);
        transition: transform 0.3s ease-in-out;
        max-width: 300px;
    }
    
    .notification.show {
        transform: translateX(0);
    }
    
    .notification-success {
        background-color: #059669;
    }
    
    .notification-error {
        background-color: #dc2626;
    }
    
    .notification-info {
        background-color: #2563eb;
    }
    
    .btn-success {
        background-color: #059669 !important;
    }
`).appendTo('head');