<?php
// remove_from_cart.php
header('Content-Type: application/json');
require_once '../connections/config.php';

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    // Validate input
    if (!isset($data['cart_id'])) {
        echo json_encode([
            'success' => false, 
            'message' => 'Invalid input'
        ]);
        exit;
    }

    $cart_id = $data['cart_id'];

    try {
        // Remove item from cart
        $stmt = $pdo->prepare("DELETE FROM cart WHERE id = ?");
        $stmt->execute([$cart_id]);

        echo json_encode([
            'success' => true, 
            'message' => 'Item removed from cart successfully!'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false, 
            'message' => 'Error removing from cart: ' . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid request method'
    ]);
}