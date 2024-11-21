<?php

@include '../connections/config.php';


if (isset($_POST['addToCart'])) {
   
    $productId = $_POST['productId'];
    $quantity = $_POST['quantity'];

    
    $productId = mysqli_real_escape_string($conn, $productId);
    $quantity = mysqli_real_escape_string($conn, $quantity);

    // Update product stock in the database
    $sql = "UPDATE products SET quantity = quantity - $quantity WHERE id = $productId";
    $result = mysqli_query($conn, $sql);
    
    // Return success or error response
    if ($result) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update stock']);
    }

    exit; 
} else {
    
    echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
}
