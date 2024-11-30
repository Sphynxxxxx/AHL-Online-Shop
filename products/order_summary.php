<?php
session_start();
include '../connections/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the order details from the POST request
    $product_names = isset($_POST['product_names']) ? $_POST['product_names'] : [];
    $quantities = isset($_POST['quantities']) ? $_POST['quantities'] : [];
    $prices = isset($_POST['prices']) ? $_POST['prices'] : [];
    $subtotal = isset($_POST['subtotal']) ? $_POST['subtotal'] : 0;
    $product_images = isset($_POST['product_images']) ? $_POST['product_images'] : [];
    $customer_email = isset($_SESSION['email']) ? $_SESSION['email'] : ''; // Assuming the customer is logged in

    // Check if all arrays are correctly initialized and not empty
    if (empty($product_names) || empty($quantities) || empty($prices) || empty($product_images)) {
        echo "Error: Missing product details.";
        exit;
    }

    // Calculate the total price
    $total = 0;
    foreach ($prices as $index => $price) {
        $total += $price * $quantities[$index];
    }

    // Start a transaction to ensure data integrity
    mysqli_begin_transaction($conn);

    try {
        // Insert order into the orders table
        $sql = "INSERT INTO orders (customer_email, subtotal, total) VALUES (?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'sdd', $customer_email, $subtotal, $total);
        mysqli_stmt_execute($stmt);

        // Get the order ID of the inserted order
        $order_id = mysqli_insert_id($conn);

        // Insert each order item into the order_items table
        foreach ($product_names as $index => $product_name) {
            $quantity = $quantities[$index];
            $price = $prices[$index];
            $product_image = $product_images[$index];

            $sql = "INSERT INTO order_items (order_id, product_name, quantity, price, product_image) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'isids', $order_id, $product_name, $quantity, $price, $product_image);
            mysqli_stmt_execute($stmt);
        }

        // Commit the transaction
        mysqli_commit($conn);

        // After successful order placement, clear the session order summary
        unset($_SESSION['orderSummary']);

        // Redirect to the customer page after the order has been processed
        header("Location: customer.php");
        exit();
    } catch (Exception $e) {
        
        mysqli_rollback($conn);
        echo "Error: " . $e->getMessage();
    }
}
?>