$(document).ready(function() {
    // Add to cart functionality
    $('.add-to-cart').click(function(e) {
        e.preventDefault();
        const productId = $(this).data('productid');
        const quantity = $(this).data('quantity');
        
        $.ajax({
            url: 'cart-process.php',
            method: 'POST',
            data: {
                action: 'add',
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                const data = JSON.parse(response);
                if(data.success) {
                    alert('Product added to cart!');
                    // Update cart count in navbar
                    $('.badge').text(data.cartCount);
                } else {
                    alert(data.message || 'Error adding product to cart');
                }
            },
            error: function() {
                alert('Error adding product to cart');
            }
        });
    });

    // Quantity update in cart
    $('.quantity-input').change(function() {
        const productId = $(this).data('productid');
        const quantity = $(this).val();
        
        $.ajax({
            url: 'cart-process.php',
            method: 'POST',
            data: {
                action: 'update',
                product_id: productId,
                quantity: quantity
            },
            success: function(response) {
                const data = JSON.parse(response);
                if(data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error updating cart');
                }
            },
            error: function() {
                alert('Error updating cart');
            }
        });
    });

    // Search functionality
    $('#search-form').submit(function(e) {
        const searchTerm = $('#search-input').val().trim();
        if(searchTerm === '') {
            e.preventDefault();
            alert('Please enter a search term');
        }
    });
}); 