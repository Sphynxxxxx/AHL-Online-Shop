<?php
session_start();
include '../connections/config.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Get the raw POST data
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

// Validate input
if (!$data || !isset($data['orderDetails']) || empty($data['orderDetails'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid order data']);
    exit();
}

// Start a database transaction
$conn->begin_transaction();

try {
    // Get customer ID from session
    $email = $_SESSION['email'];
    $customerQuery = "SELECT id FROM customers WHERE email = ?";
    $stmt = $conn->prepare($customerQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result->fetch_assoc();
    $customerId = $customer['id'];

    // Generate unique reference number
    $referenceNumber = 'AHL-' . strtoupper(substr(md5(uniqid()), 0, 8));

    // Calculate total price
    $totalPrice = 0;
    foreach ($data['orderDetails'] as $item) {
        $totalPrice += $item['price'] * $item['quantity'];
    }

    // Insert into orders table
    $orderQuery = "INSERT INTO orders (customer_id, delivery_method, reference_number, total_price) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($orderQuery);
    $deliveryMethod = $data['deliveryMethod'] ?? 'pickup';
    $stmt->bind_param("issd", $customerId, $deliveryMethod, $referenceNumber, $totalPrice);
    $stmt->execute();
    $orderId = $conn->insert_id;

    // Insert order details
    $detailQuery = "INSERT INTO order_details (order_id, product_id, quantity, price, shippingfee) VALUES (?, ?, ?, ?, ?)";
    $detailStmt = $conn->prepare($detailQuery);

    // Update product quantities and insert order details
    foreach ($data['orderDetails'] as $item) {
        // Update product quantity
        $updateProductQuery = "UPDATE products SET quantity = quantity - ? WHERE product_id = ?";
        $updateStmt = $conn->prepare($updateProductQuery);
        $updateStmt->bind_param("ii", $item['quantity'], $item['id']);
        $updateStmt->execute();

        // Insert order detail
        $shippingFee = 0; // Set to 0 for now, adjust as needed
        $detailStmt->bind_param("iiddd", $orderId, $item['id'], $item['quantity'], $item['price'], $shippingFee);
        $detailStmt->execute();
    }

    // Commit transaction
    $conn->commit();

    // Store reference number in session for order details page
    $_SESSION['last_order_reference'] = $referenceNumber;

    echo json_encode([
        'success' => true, 
        'message' => 'Order placed successfully', 
        'referenceNumber' => $referenceNumber
    ]);

} catch (Exception $e) {
    // Rollback transaction in case of error
    $conn->rollback();
    echo json_encode([
        'success' => false, 
        'message' => 'Error processing order: ' . $e->getMessage()
    ]);
}

// Close connections
$stmt->close();
$conn->close();
exit();
?>