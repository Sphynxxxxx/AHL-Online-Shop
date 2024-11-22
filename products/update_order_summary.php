<?php
session_start();

// Capture the order item from the request
$inputData = json_decode(file_get_contents('php://input'), true);

// Check if we already have an order summary in the session
if (!isset($_SESSION['order_summary'])) {
    $_SESSION['order_summary'] = [];
}

// Add the new order item to the summary
$productId = $inputData['productId'];
$quantity = $inputData['quantity'];
$productName = $inputData['productName'];
$price = $inputData['price'];

// Check if the product already exists in the order summary
$productExists = false;
foreach ($_SESSION['order_summary'] as &$item) {
    if ($item['productId'] == $productId) {
        $item['quantity'] += $quantity;  // Increase quantity if already in the cart
        $productExists = true;
        break;
    }
}

if (!$productExists) {
    // If the product is not in the summary, add it
    $_SESSION['order_summary'][] = [
        'productId' => $productId,
        'productName' => $productName,
        'quantity' => $quantity,
        'price' => $price,
    ];
}

echo json_encode(['status' => 'success']);
?>
