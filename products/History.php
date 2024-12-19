<?php

include '../connections/config.php';
session_start();

if (!isset($_SESSION['email'])) {
    echo "Please log in to view your order history.";
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Order History</title>
    <link rel="stylesheet" href="assets\history.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php

$userEmail = $_SESSION['email'];

$sqlCustomer = "SELECT id FROM customers WHERE email = ?";
$stmt = $conn->prepare($sqlCustomer);
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$resultCustomer = $stmt->get_result();

if ($resultCustomer->num_rows === 0) {
    echo "No user found with this email.";
    exit;
}

$customer = $resultCustomer->fetch_assoc();
$customerId = $customer['id'];

// Query to get orders for this customer
$sqlOrders = "
    SELECT o.id AS order_id, o.order_date, o.delivery_method, o.reference_number, o.total_price
    FROM orders o
    WHERE o.customer_id = ?
    ORDER BY o.order_date DESC
";
$stmt = $conn->prepare($sqlOrders);
$stmt->bind_param("i", $customerId);
$stmt->execute();
$resultOrders = $stmt->get_result();

// Check if orders exist
if ($resultOrders->num_rows === 0) {
    echo "No orders found.";
    exit;
}

echo "<a href='customer.php'><i class='fa-solid fa-house'></i></a>";
echo "<h1>Your Order History</h1>";

while ($order = $resultOrders->fetch_assoc()) {
    echo "<h2>Order #" . htmlspecialchars($order['reference_number']) . "</h2>";
    echo "<p>Order Date: " . htmlspecialchars($order['order_date']) . "</p>";

    // Validate delivery method before displaying
    $validDeliveryMethods = ['pickup', 'cod'];
    if (!in_array($order['delivery_method'], $validDeliveryMethods)) {
        echo "<p>Delivery Method: Invalid</p>";
    } else {
        echo "<p>Delivery Method: " . htmlspecialchars($order['delivery_method']) . "</p>";
    }

    echo "<p>Total Price: ₱" . htmlspecialchars($order['total_price']) . "</p>";

    // Query to get order details
    $sqlOrderDetails = "
        SELECT od.product_id, od.quantity, od.price, od.shippingfee, p.product_name, p.image 
        FROM order_details od
        JOIN products p ON od.product_id = p.product_id
        WHERE od.order_id = ?
    ";

    $stmtDetails = $conn->prepare($sqlOrderDetails);
    $stmtDetails->bind_param("i", $order['order_id']);
    $stmtDetails->execute();
    $resultDetails = $stmtDetails->get_result();

    // Display order details
    echo "<h3>Order Details:</h3>";
    echo "<table border='1'>
        <tr>
            <th>Product ID</th>
            <th>Product Name</th>
            <th>Image</th>
            <th>Quantity</th>
            <th>Price</th>
            <th>Shipping Fee</th>
        </tr>";
    while ($detail = $resultDetails->fetch_assoc()) {
        echo "<tr>
            <td>" . htmlspecialchars($detail['product_id']) . "</td>
            <td>" . htmlspecialchars($detail['product_name']) . "</td>
            <td>
                <img class='product-image' src='product_pics/" . htmlspecialchars($detail['image']) . "' 
                alt='Product Name: " . htmlspecialchars($detail['product_name']) . "' 
                style='width:100px;height:100px;'>
            </td>
            <td>" . htmlspecialchars($detail['quantity']) . "</td>
            <td>₱" . htmlspecialchars($detail['price']) . "</td>
            <td>₱" . htmlspecialchars($detail['shippingfee']) . "</td>
        </tr>";
    }
    echo "</table>";
    echo "<hr>";
}

$stmt->close();
$conn->close();
?>
</body>
</html>
