<?php
session_start();
include '../connections/config.php';

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

$customer_email = $_SESSION['email'];
$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];

// Get customer id from email
$sqlCustomer = "SELECT id FROM customers WHERE email = ?";
$stmt = $conn->prepare($sqlCustomer);
$stmt->bind_param("s", $customer_email);
$stmt->execute();
$resultCustomer = $stmt->get_result();
$rowCustomer = $resultCustomer->fetch_assoc();
$customer_id = $rowCustomer['id'];

// Check if the product is already in the cart
$sqlCheckCart = "SELECT quantity FROM carts WHERE customer_id = ? AND product_id = ?";
$stmt = $conn->prepare($sqlCheckCart);
$stmt->bind_param("ii", $customer_id, $product_id);
$stmt->execute();
$resultCheckCart = $stmt->get_result();

if ($resultCheckCart->num_rows > 0) {
    // If product is in cart, update the quantity
    $row = $resultCheckCart->fetch_assoc();
    $newQuantity = $row['quantity'] + $quantity;
    $sqlUpdateCart = "UPDATE carts SET quantity = ? WHERE customer_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sqlUpdateCart);
    $stmt->bind_param("iii", $newQuantity, $customer_id, $product_id);
    $stmt->execute();
} else {
    // If product is not in cart, insert it
    $sqlInsertCart = "INSERT INTO carts (customer_id, product_id, quantity) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sqlInsertCart);
    $stmt->bind_param("iii", $customer_id, $product_id, $quantity);
    $stmt->execute();
}

// Fetch the updated cart count
$sqlCartCount = "
    SELECT SUM(quantity) AS total_quantity
    FROM carts
    WHERE customer_id = ?
";
$stmt = $conn->prepare($sqlCartCount);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$resultCartCount = $stmt->get_result();
$rowCartCount = $resultCartCount->fetch_assoc();
$cartCount = $rowCartCount['total_quantity'] ? $rowCartCount['total_quantity'] : 0;

echo json_encode(['success' => true, 'cartCount' => $cartCount]);
?>
