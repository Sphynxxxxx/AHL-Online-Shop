<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: customer.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get product details from the POST request
    $productId = intval($_POST['productId']);
    $productName = htmlspecialchars($_POST['productName']);
    $productPrice = floatval($_POST['productPrice']);
    $quantity = intval($_POST['quantity']); // Quantity is obtained from the form submission
    $productImage = htmlspecialchars($_POST['productImage']);

    // Calculate total price
    $totalPrice = $productPrice * $quantity;

    // Display order summary
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Order Summary</title>
        <link rel="stylesheet" href="../connections/Assets/customer.css">
    </head>
    <body>
        <div class="order-summary">
            <h3>Order Summary</h3>
            <div class="order-item">
                <img src="product_pics/<?php echo $productImage; ?>" alt="<?php echo $productName; ?>" style="width: 50px; height: auto;" onerror="this.onerror=null; this.src='product_pics/default.png';">
                <span><?php echo $productName; ?> (x<?php echo $quantity; ?>)</span>
                <span>PHP <?php echo number_format($totalPrice, 2); ?></span>
            </div>
            <div class="total">
                <p>Subtotal</p>
                <p id="subtotal">PHP <?php echo number_format($totalPrice, 2); ?></p>
            </div>
            <div class="payment">
                <button id="cash-btn">Pick up</button>
            </div>
            <button class="place-order">Place Order</button>
        </div>
    </body>
    </html>
    <?php
}
?>