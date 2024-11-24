<?php
session_start();
include '../connections/config.php';

// Ensure the customer is logged in
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['email'])) {
        header("Location: customer.php");
        exit();
    }

    // Get the customer email and order details
    $userEmail = mysqli_real_escape_string($conn, $_SESSION['email']);
    $product_names = $_POST['product_names'];
    $quantities = $_POST['quantities'];
    $prices = $_POST['prices'];
    $product_images = $_POST['product_images'];
    $subtotal = $_POST['subtotal'];
    
    // Start a transaction to ensure data integrity
    mysqli_begin_transaction($conn);

    try {
        // Insert the order into the `orders` table
        $sql = "INSERT INTO orders (customer_email, subtotal) VALUES (?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'sd', $userEmail, $subtotal);
        mysqli_stmt_execute($stmt);

        // Get the last inserted order ID
        $order_id = mysqli_insert_id($conn);

        // Insert each item into the `order_items` table
        foreach ($product_names as $index => $product_name) {
            $quantity = $quantities[$index];
            $price = $prices[$index];
            $product_image = $product_images[$index];

            // Insert the order item into `order_items` table
            $sql = "INSERT INTO order_items (order_id, product_name, quantity, price, product_image) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'isids', $order_id, $product_name, $quantity, $price, $product_image);
            mysqli_stmt_execute($stmt);

            // Update the product quantity in the `products` table
            $sql = "UPDATE products SET quantity = quantity - ? WHERE product_name = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, 'is', $quantity, $product_name);
            $updateResult = mysqli_stmt_execute($stmt);

            // Check if the product quantity update was successful
            if (!$updateResult) {
                throw new Exception("Error updating product stock for $product_name.");
            }
        }

        // Commit the transaction if everything is successful
        mysqli_commit($conn);

        // Clear the session order summary after successful order placement
        unset($_SESSION['orderSummary']);

        // Redirect the user to the customer page (or success page)
        header("Location: customer.php");
        exit();
    } catch (Exception $e) {
        // Rollback the transaction if something goes wrong
        mysqli_roll_back($conn);
        echo "Error: " . $e->getMessage();
    }
}
?>
