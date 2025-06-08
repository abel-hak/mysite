$(document).ready(function() {
    $('.add-to-cart').on('click', function() {
        const productId = $(this).data('id');
        const productName = $(this).data('name');
        const productPrice = $(this).data('price');

        console.log('Sending:', {
            id: productId,
            name: productName,
            price: productPrice
        });

        $.ajax({
            url: '/MYSite/cart.php', 
            type: 'POST',
            data: {
                id: productId,
                name: productName,
                price: productPrice
            },
            success: function(response) {
                console.log('Success:', response);
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
            }
        });

    });
});
