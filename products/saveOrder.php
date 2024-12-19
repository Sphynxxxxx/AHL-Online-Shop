<?php
session_start();
include '../connections/config.php';

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);

if (!$data || !isset($data['orderDetails']) || empty($data['orderDetails'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid order data']);
    exit();
}

// Start a database transaction
$conn->begin_transaction();

try {
    $email = $_SESSION['email'];
    $customerQuery = "SELECT id FROM customers WHERE email = ?";
    $stmt = $conn->prepare($customerQuery);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Customer not found');
    }
    
    $customer = $result->fetch_assoc();
    $customerId = $customer['id'];

    // Generate unique reference number
    $referenceNumber = 'AHL-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 4));

    $totalPrice = 0;
    foreach ($data['orderDetails'] as $item) {
        $totalPrice += $item['price'] * $item['quantity'];
    }

    // Insert into orders table
    $orderQuery = "INSERT INTO orders (customer_id, delivery_method, reference_number, total_price, order_date) 
                   VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($orderQuery);
    $deliveryMethod = $data['deliveryMethod'] ?? 'pickup';
    $stmt->bind_param("issd", $customerId, $deliveryMethod, $referenceNumber, $totalPrice);
    $stmt->execute();
    $orderId = $conn->insert_id;

    // Insert order details and remove from cart
    foreach ($data['orderDetails'] as $item) {
        // Insert order detail
        $detailQuery = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                    VALUES (?, ?, ?, ?)";
        $detailStmt = $conn->prepare($detailQuery);
        $detailStmt->bind_param("iiid", $orderId, $item['id'], $item['quantity'], $item['price']);
        $detailStmt->execute();

        // Remove item from cart
        $deleteCartQuery = "DELETE FROM carts WHERE customer_id = ? AND product_id = ?";
        $deleteCartStmt = $conn->prepare($deleteCartQuery);
        $deleteCartStmt->bind_param("ii", $customerId, $item['id']);
        $deleteCartStmt->execute();

        // Update product quantity in the products table
        $productId = $item['id'];
        $quantityOrdered = $item['quantity'];

        $stockQuery = "SELECT quantity FROM products WHERE product_id = ?";
        $stockStmt = $conn->prepare($stockQuery);
        $stockStmt->bind_param("i", $productId);
        $stockStmt->execute();
        $stockResult = $stockStmt->get_result();

        if ($stockResult->num_rows > 0) {
            $product = $stockResult->fetch_assoc();
            $currentQuantity = $product['quantity'];

            // Check if enough stock is available
            if ($currentQuantity >= $quantityOrdered) {
                // Update the product quantity
                $updateStockQuery = "UPDATE products SET quantity = quantity - ? WHERE product_id = ?";
                $updateStockStmt = $conn->prepare($updateStockQuery);
                $updateStockStmt->bind_param("ii", $quantityOrdered, $productId);
                $updateStockStmt->execute();
            } else {
                // Not enough stock available
                throw new Exception("Insufficient stock for product: " . $item['product_name']);
            }
        } else {
            throw new Exception("Product not found in stock: " . $item['product_name']);
        }
    }


    // Create the notification message
    $notification_message = "You have successfully placed your order.";
    $notification_title = "Order Status Update";
    $notification_date = date('Y-m-d H:i:s');

    // Insert the notification into the `notifications` table
    $stmtNotification = $conn->prepare("INSERT INTO notifications (title, message, date_created, order_id) 
                                    VALUES (?, ?, ?, ?)");
    $stmtNotification->bind_param("sssi", $notification_title, $notification_message, $notification_date, $orderId);

    if (!$stmtNotification->execute()) {
        throw new Exception("Failed to insert notification.");
    }


    // Commit transaction
    $conn->commit();

    $_SESSION['last_order_reference'] = $referenceNumber;

    // Success response
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully!',
        'referenceNumber' => $referenceNumber,
        'deliveryMethod' => $deliveryMethod,
        'totalPrice' => $totalPrice
    ]);

} catch (Exception $e) {
    // Rollback in case of error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>