<?php
session_start();
include '../connections/config.php';

if (!isset($_SESSION['email'])) {
    echo "<p>You need to log in to proceed with checkout. <a href='login.php'>Login</a></p>";
    exit();
}

$email = $_SESSION['email'];
$customerQuery = "SELECT id FROM customers WHERE email = ?";
$stmt = $conn->prepare($customerQuery);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();
$customerId = $customer['id'];

// Check if items were submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout_items'])) {
    $selectedItems = $_POST['checkout_items'];

    if (empty($selectedItems)) {
        echo "<p>No items selected for checkout. <a href='cart.php'>Go back to cart</a></p>";
        exit();
    }

    $conn->begin_transaction();

    try {
        $referenceNumber = 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8));

        // Calculate total price
        $totalPrice = 0;
        foreach ($selectedItems as $cartId) {
            $cartQuery = "
                SELECT p.price, c.quantity 
                FROM carts c
                JOIN products p ON c.product_id = p.product_id
                WHERE c.id = ? AND c.customer_id = ?
            ";
            $stmt = $conn->prepare($cartQuery);
            $stmt->bind_param("ii", $cartId, $customerId);
            $stmt->execute();
            $result = $stmt->get_result();
            $cartItem = $result->fetch_assoc();

            $totalPrice += $cartItem['price'] * $cartItem['quantity'];
        }

        // Insert into orders table
        $orderQuery = "INSERT INTO orders (customer_id, reference_number, total_price) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($orderQuery);
        $stmt->bind_param("isd", $customerId, $referenceNumber, $totalPrice);
        $stmt->execute();
        $orderId = $conn->insert_id;

        // Insert into order details table and delete items from the cart
        $orderDetailsQuery = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $deleteCartQuery = "DELETE FROM carts WHERE id = ?";
        foreach ($selectedItems as $cartId) {
            $cartQuery = "
                SELECT c.product_id, c.quantity, p.price
                FROM carts c
                JOIN products p ON c.product_id = p.product_id
                WHERE c.id = ?
            ";
            $stmt = $conn->prepare($cartQuery);
            $stmt->bind_param("i", $cartId);
            $stmt->execute();
            $result = $stmt->get_result();
            $cartItem = $result->fetch_assoc();

            // Insert order details
            $stmt = $conn->prepare($orderDetailsQuery);
            $stmt->bind_param(
                "iiid",
                $orderId,
                $cartItem['product_id'],
                $cartItem['quantity'],
                $cartItem['price']
            );
            $stmt->execute();

            // Remove from cart
            $stmt = $conn->prepare($deleteCartQuery);
            $stmt->bind_param("i", $cartId);
            $stmt->execute();
        }

        $conn->commit();

        $_SESSION['last_order_reference'] = $referenceNumber;
        header("Location: order_confirmation.php");
        exit();

    } catch (Exception $e) {
        $conn->rollback();
        echo "<p>Error processing checkout: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p>No items selected for checkout. <a href='cart.php'>Go back to cart</a></p>";
}

$stmt->close();
$conn->close();
?>
