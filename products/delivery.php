<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: customer.php");
    exit();
}

@include '../connections/config.php';

// Fetch user email and name
$userEmail = htmlspecialchars($_SESSION['email']);
$sql = "SELECT name FROM users WHERE email = '$userEmail'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
$userName = $user ? $user['name'] : 'User';

// Check if order_id is passed via GET request
if (isset($_GET['order_id']) && is_numeric($_GET['order_id'])) {
    $orderId = $_GET['order_id'];

    // Fetch the order details from the orders table
    $orderQuery = "SELECT * FROM orders WHERE id = $orderId AND user_email = '$userEmail'";
    $orderResult = mysqli_query($conn, $orderQuery);

    if ($orderResult && mysqli_num_rows($orderResult) > 0) {
        $order = mysqli_fetch_assoc($orderResult);
        
        // Fetch the order items from the order_items table
        $orderItemsQuery = "SELECT oi.*, p.product_name, p.image FROM order_items oi
                             JOIN products p ON oi.product_id = p.id
                             WHERE oi.order_id = $orderId";
        $orderItemsResult = mysqli_query($conn, $orderItemsQuery);
    } else {
        echo "Order not found or you are not authorized to view this order.";
        exit();
    }
} else {
    echo "No valid order ID provided.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Details</title>
    <link rel="stylesheet" href="assets/cart.css">
</head>
<body>
    <div class="container">
        <h1>Delivery Details</h1>

        <?php if (isset($order)): ?>
            <h2>Order #<?php echo $order['id']; ?></h2>
            <p>Status: <?php echo $order['order_status']; ?></p>
            <p>Order Date: <?php echo $order['created_at']; ?></p>

            <h3>Order Summary</h3>
            <div class="order-items">
                <?php 
                $totalPrice = 0;
                while ($item = mysqli_fetch_assoc($orderItemsResult)): 
                    $totalPrice += $item['price'] * $item['quantity'];
                ?>
                    <div class="order-item">
                        <img src="product_pics/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width: 50px; height: auto;">
                        <span><?php echo htmlspecialchars($item['product_name']); ?> (x<?php echo htmlspecialchars($item['quantity']); ?>)</span>
                        <span>PHP <?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                    </div>
                <?php endwhile; ?>
            </div>

            <div class="total">
                <p>Subtotal</p>
                <p>PHP <?php echo number_format($totalPrice, 2); ?></p>
            </div>

            <!-- Delivery Information Section -->
            <h3>Delivery Information</h3>
            <p>Delivery Method: <?php echo $order['delivery_method']; ?></p>
            <p>Delivery Address: <?php echo $order['delivery_address']; ?></p>
            <p>Estimated Delivery Date: <?php echo $order['estimated_delivery_date']; ?></p>
            
            <h3>Contact Information</h3>
            <p>Name: <?php echo $userName; ?></p>
            <p>Email: <?php echo $userEmail; ?></p>
        <?php else: ?>
            <p>No order found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
