$(document).ready(function() {
    // Handle "Proceed to Checkout" button click
    $('#proceed-checkout').on('click', function() {
        // First, validate stock
        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: { action: 'validate_stock' },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Hide cart actions and show payment methods
                    $('.cart-actions').hide();
                    $('#payment-methods').css('display', 'flex');
                } else {
                    $('#stock-error').text(response.message).show();
                }
            },
            error: function() {
                $('#stock-error').text('Error checking stock availability.').show();
            }
        });
    });

    // Enable finish button when payment method is selected
    $('input[name="payment_method"]').on('change', function() {
        $('#finish-order').prop('disabled', false);
    });

    // Handle order completion
    $('#finish-order').on('click', function() {
        const paymentMethod = $('input[name="payment_method"]:checked').val();
        if (!paymentMethod) {
            $('#stock-error').text('Please select a payment method.').show();
            return;
        }

        $(this).prop('disabled', true).text('Processing...');
        
        $.ajax({
            url: 'cart.php',
            type: 'POST',
            data: { 
                action: 'finalize',
                payment_method: paymentMethod
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#stock-error').hide();
                    $('#order-success').show();
                    // Redirect to cart page with success message
                    setTimeout(function() {
                        window.location.href = 'cart.php?success=1';
                    }, 1500);
                } else {
                    $('#stock-error').text(response.message || 'Error processing order.').show();
                    $('#finish-order').prop('disabled', false).text('Complete Order');
                }
            },
            error: function() {
                $('#stock-error').text('Error processing order.').show();
                $('#finish-order').prop('disabled', false).text('Complete Order');
            }
        });
    });
});
