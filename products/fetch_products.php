<?php
session_start();
include '../connections/config.php';

$customer_id = isset($_SESSION['customer_id']) ? $_SESSION['customer_id'] : null;
$cartItems = [];

if ($customer_id) {
    $stmt = $conn->prepare("
        SELECT c.id, p.name, p.price, c.quantity 
        FROM carts c
        JOIN products p ON c.product_id = p.product_id
        WHERE c.customer_id = ?
    ");
    $stmt->execute([$customer_id]);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif (isset($_SESSION['cart'])) {
    $cartItems = [];
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $stmt = $conn->prepare("SELECT name, price FROM products WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($product) {
            $product['quantity'] = $quantity;
            $cartItems[] = $product;
        }
    }
}

echo json_encode($cartItems);
