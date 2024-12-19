<?php
session_start();
require_once 'cart.php';

$product_id = $_GET['product_id'] ?? null;
$product = [
    'product_id' => $product_id,
    'name' => 'Sample Product',
    'price' => 19.99,
    'stock' => 10
];
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo htmlspecialchars($product['name']); ?></title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1><?php echo htmlspecialchars($product['name']); ?></h1>
    <p>Price: $<?php echo number_format($product['price'], 2); ?></p>
    <p>Available Stock: <?php echo $product['stock']; ?></p>

    <form id="add-to-cart-form">
        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
        <label for="quantity">Quantity:</label>
        <input type="number" name="quantity" id="quantity" 
               min="1" 
               max="<?php echo $product['stock']; ?>" 
               value="1">
        <button type="submit" 
                <?php echo $product['stock'] <= 0 ? 'disabled' : ''; ?>>
            Add to Cart
        </button>
    </form>

    <script>
    $(document).ready(function() {
        $('#add-to-cart-form').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: 'add_to_cart.php',
                method: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        alert(response.message);
                    } else {
                        alert(response.message);
                    }
                },
                error: function() {
                    alert('Error adding product to cart');
                }
            });
        });
    });
    </script>
</body>
</html>