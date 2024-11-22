<?php
session_start();
include '../connections/config.php';

if (isset($_POST['action']) && $_POST['action'] == 'addToCart') {
    $productId = intval($_POST['productId']);
    $productName = htmlspecialchars($_POST['productName']);
    $productPrice = floatval($_POST['productPrice']);
    $quantity = intval($_POST['quantity']);

    // Check if the cart session exists
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // Check if the product is already in the cart
    $productIndex = -1;
    foreach ($_SESSION['cart'] as $index => $item) {
        if ($item['id'] == $productId) {
            $productIndex = $index;
            break;
        }
    }

    // If product is already in the cart, update the quantity
    if ($productIndex != -1) {
        $_SESSION['cart'][$productIndex]['quantity'] += $quantity;
    } else {
        // If product is not in the cart, add it
        $_SESSION['cart'][] = [
            'id' => $productId,
            'name' => $productName,
            'price' => $productPrice,
            'quantity' => $quantity
        ];
    }

    // Return success response
    echo json_encode(['status' => 'success', 'message' => 'Product added to cart!']);
} else {
    // Invalid action or missing parameters
    echo json_encode(['status' => 'error', 'message' => 'Invalid request.']);
}
?>
