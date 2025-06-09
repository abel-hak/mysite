$(document).ready(function() {
    // Hide payment method section initially
    $('#payment-method-section').hide();
    $('#final-confirm-section').hide();

    // Place Order button: Validate stock first
    $('#place-order-btn').on('click', function(e) {
        e.preventDefault();
        $("#checkout-error").remove();
        $.ajax({
            url: 'checkout.php',
            type: 'POST',
            data: {action: 'validate_stock'},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show payment method section
                    $('#payment-method-section').css('display', 'flex');
                    $('#checkout-form').hide();
                } else {
                    $('<div id="checkout-error" style="color:red; margin:10px 0;">'+response.message+'</div>').insertBefore('#checkout-form');
                }
            },
            error: function() {
                alert('There was an error validating stock.');
            }
        });
    });

    // Payment method selection: enable Finish button
    $('input[name="payment_method"]').on('change', function() {
        $('#finish-order-btn').prop('disabled', false);
    });

    // Finish button: Finalize order
    $('#finish-order-btn').on('click', function(e) {
        e.preventDefault();
        var payment_method = $('input[name="payment_method"]:checked').val();
        if (!payment_method) {
            alert('Please select a payment method.');
            return;
        }
        $.ajax({
            url: 'checkout.php',
            type: 'POST',
            data: {action: 'finalize_order', payment_method: payment_method},
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#order-number').text(response.order_id);
                    $('#order-modal').addClass('active');
                } else {
                    $('<div id="checkout-error" style="color:red; margin:10px 0;">'+response.message+'</div>').insertBefore('#payment-method-section');
                }
            },
            error: function() {
                alert('There was an error processing your order.');
            }
        });
    });
});
