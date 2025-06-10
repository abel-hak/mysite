function showPopup(message, type = 'success') {
    // Remove existing popup if any
    $('.custom-popup').remove();

    // Create new popup
    const popup = $(`
        <div class="custom-popup ${type}">
            <span class="icon">${type === 'success' ? '✓' : '✕'}</span>
            <p class="message">${message}</p>
        </div>
    `);

    // Add to body
    $('body').append(popup);

    // Show popup
    setTimeout(() => popup.addClass('show'), 100);

    // Hide and remove after 3 seconds
    setTimeout(() => {
        popup.removeClass('show');
        setTimeout(() => popup.remove(), 300);
    }, 3000);
}

$(document).ready(function() {
    // Initialize wishlist buttons
    function updateWishlistButton(btn, isInWishlist) {
        if (isInWishlist) {
            btn.html('♥').addClass('active');
        } else {
            btn.html('♡').removeClass('active');
        }
        // Ensure button is visible
        btn.css('display', 'flex');
    }

    // Check initial wishlist status for all products
    $('.wishlist-btn').each(function() {
        const btn = $(this);
        const productId = btn.data('id');
        
        $.ajax({
            url: 'includes/wishlist_handler.php',
            type: 'GET',
            dataType: 'json',
            data: { 
                action: 'check', 
                product_id: productId 
            },
            success: function(response) {
                console.log('Check response:', response);
                updateWishlistButton(btn, response.inWishlist);
            },
            error: function(xhr, status, error) {
                console.error('Check error:', error);
                console.log('Response:', xhr.responseText);
            }
        });
    });

    // Handle wishlist button clicks
    $('.wishlist-btn').on('click', function(e) {
        e.preventDefault();
        const btn = $(this);
        const productId = btn.data('id');
        const action = btn.hasClass('active') ? 'remove' : 'add';

        console.log('Wishlist action:', { action, productId });

        $.ajax({
            url: 'includes/wishlist_handler.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: action,
                product_id: productId
            },
            success: function(response) {
                console.log('Success response:', response);
                if (response.success) {
                    updateWishlistButton(btn, action === 'add');
                    // Update wishlist count in header
                    $.ajax({
                        url: 'includes/wishlist_handler.php',
                        type: 'GET',
                        dataType: 'json',
                        data: { action: 'count' },
                        success: function(countResponse) {
                            console.log('Count response:', countResponse);
                            $('#wishlist-count').text(countResponse.count || 0);
                        }
                    });
                } else {
                    if (response.error === 'Please login to manage wishlist') {
                        window.location.href = 'login.php';
                    } else {
                        alert(response.error || 'Error updating wishlist');
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                console.log('Response:', xhr.responseText);
                alert('Error updating wishlist. Please try again.');
            }
        });
    });

    // Add to cart functionality
    $('.add-to-cart').on('click', function(e) {
        e.preventDefault();
        const button = $(this);
        const productId = button.data('id');
        const productName = button.data('name');
        const productPrice = button.data('price');
        
        // Get quantity from the input field if it exists, otherwise use 1
        const quantity = parseInt($('input[name="quantity"]').val()) || 1;
        
        if (isNaN(quantity) || quantity < 1) {
            showPopup('Please enter a valid quantity', 'error');
            return;
        }
        
        $.ajax({
            url: 'cart.php',
            type: 'POST',
            dataType: 'json',
            data: {
                id: productId,
                name: productName,
                price: productPrice,
                quantity: quantity
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                try {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }
                    
                    if (response.success) {
                        $('#cart-count').text(response.cartCount);
                        showPopup(response.message || 'Item added to cart successfully!', 'success');
                    } else {
                        showPopup(response.error || 'Error adding item to cart', 'error');
                    }
                } catch (e) {
                    showPopup('Error processing server response', 'error');
                    console.error('Error:', e);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                showPopup('Error adding item to cart', 'error');
            }
        });
    });

    // Remove from cart functionality
    $('.remove-from-cart').on('click', function(e) {
        e.preventDefault();
        const button = $(this);
        const orderId = button.data('order-id');
        const orderCard = button.closest('.order-card');

        if (confirm('Are you sure you want to remove this item from your cart?')) {
            $.ajax({
                url: 'cart.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'remove',
                    order_id: orderId
                },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    if (response.success) {
                        // Remove the item card
                        orderCard.fadeOut(300, function() {
                            $(this).remove();
                            // If no items left, show empty cart message
                            if ($('.order-card').length === 0) {
                                $('#cart-content').html(
                                    '<div class="empty-cart">\n' +
                                    '    <p>Your cart is empty</p>\n' +
                                    '    <a href="index.php" class="btn btn-primary">Continue Shopping</a>\n' +
                                    '</div>'
                                );
                                // Hide checkout button
                                $('#checkout-btn').hide();
                            }
                            // Reload the page to update totals
                            location.reload();
                        });
                    } else {
                        alert(response.error || 'Error removing item from cart');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Error removing item from cart');
                }
            });
        }
    });

    // Initialize wishlist count
    $.get('includes/wishlist_handler.php', { action: 'count' }, function(response) {
        $('#wishlist-count').text(response.count || 0);
    });
});
