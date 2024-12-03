<?php
// saveOrder2.php

// Include your database connection
include('db_connection.php');

// Retrieve the POST data
$data = json_decode(file_get_contents('php://input'), true);

// Get customer ID (for example, from session)
$customerId = $_SESSION['customer_id'];  // Assuming customer ID is stored in session

// Get order details and delivery method
$orderDetails = $data['orderDetails'];
$deliveryMethod = $data['deliveryMethod'];
$subtotal = $data['subtotal'];

// Begin a database transaction
$conn->begin_transaction();

try {
    // Insert the order into the 'orders2' table
    $stmt = $conn->prepare("INSERT INTO orders2 (customer_id, delivery_method) VALUES (?, ?)");
    $stmt->bind_param("is", $customerId, $deliveryMethod);
    $stmt->execute();
    $orderId = $stmt->insert_id; // Get the inserted order's ID
    $stmt->close();

    // Insert the order items into the 'order_items2' table
    foreach ($orderDetails as $item) {
        $stmt = $conn->prepare("INSERT INTO order_items2 (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiii", $orderId, $item['product_id'], $item['quantity'], $item['price']);
        $stmt->execute();
        $stmt->close();
    }

    // Commit the transaction
    $conn->commit();

    // Respond with success and the order ID
    echo json_encode(['success' => true, 'orderId' => $orderId]);

} catch (Exception $e) {
    // Rollback the transaction in case of error
    $conn->rollback();

    // Respond with error message
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
}
?>
