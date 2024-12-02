<?php
session_start();
include('../connections/config.php'); 

header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit;
}

$customer_email = $_SESSION['email'];

// Fetch customer details
$sqlCustomer = "SELECT id, customer_name, email, contact_number, address FROM customers WHERE email = ?";
$stmt = $conn->prepare($sqlCustomer);
$stmt->bind_param("s", $customer_email);
$stmt->execute();
$resultCustomer = $stmt->get_result();

if ($resultCustomer->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Customer not found.']);
    exit;
}

$customer = $resultCustomer->fetch_assoc();
$customer_id = $customer['id'];

// Get order details from POST data
$input = json_decode(file_get_contents('php://input'), true);
$orderDetails = $input['orderDetails'] ?? [];
$deliveryMethod = $input['deliveryMethod'] ?? 'pickup';

if (empty($orderDetails)) {
    echo json_encode(['success' => false, 'message' => 'No items selected for checkout.']);
    exit;
}

// Generate a unique reference number
$referenceNumber = uniqid('ORDER_');

// Calculate total price
$totalPrice = 0;
foreach ($orderDetails as $item) {
    $totalPrice += $item['price'] * $item['quantity'];
}

// Insert into `orders` table
$sqlOrder = "
    INSERT INTO orders (customer_id, delivery_method, reference_number, total_price) 
    VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sqlOrder);
$stmt->bind_param("issd", $customer_id, $deliveryMethod, $referenceNumber, $totalPrice);
$stmt->execute();

$order_id = $stmt->insert_id;

// Insert into `order_details` table
$sqlOrderDetails = "
    INSERT INTO order_details (order_id, product_id, quantity, price) 
    VALUES (?, ?, ?, ?, ?)";

$stmtDetails = $conn->prepare($sqlOrderDetails);

foreach ($orderDetails as $item) {
    $product_id = $item['id'];
    $quantity = $item['quantity'];
    $price = $item['price'];

    $stmtDetails->bind_param("iiidd", $order_id, $product_id, $quantity, $price,);
    $stmtDetails->execute();
}

// Clear selected items from the cart
$cartItemIds = array_map(fn($item) => $item['id'], $orderDetails);
$cartItemIdsPlaceholder = implode(',', array_fill(0, count($cartItemIds), '?'));

$sqlClearCart = "DELETE FROM carts WHERE id IN ($cartItemIdsPlaceholder)";
$stmtClearCart = $conn->prepare($sqlClearCart);
$stmtClearCart->bind_param(str_repeat('i', count($cartItemIds)), ...$cartItemIds);
$stmtClearCart->execute();

echo json_encode([
    'success' => true,
    'message' => 'Order placed successfully!',
    'referenceNumber' => $referenceNumber,
    'customerInfo' => [
        'name' => $customer['customer_name'],
        'email' => $customer['email'],
        'contact_number' => $customer['contact_number'],
        'address' => $customer['address'],
    ],
    'orderSummary' => [
        'deliveryMethod' => $deliveryMethod,
        'totalPrice' => $totalPrice,
        'items' => $orderDetails,
    ],
]);
exit;
?>
