<?php
session_start();
include '../connections/config.php';

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Check if any order IDs were selected
if (isset($_POST['order_ids']) && !empty($_POST['order_ids'])) {
    // Start a transaction
    $conn->begin_transaction();

    try {
        // Get selected order IDs
        $orderIds = $_POST['order_ids'];

        // Prepare the DELETE statement for order_details
        $orderIdsList = implode(",", array_map('intval', $orderIds));
        $deleteOrderDetails = "DELETE FROM order_details WHERE order_id IN ($orderIdsList)";
        $conn->query($deleteOrderDetails);

        // Prepare the DELETE statement for orders
        $deleteOrders = "DELETE FROM orders WHERE reference_number IN ($orderIdsList)";
        $conn->query($deleteOrders);

        // Commit the transaction
        $conn->commit();

        // Redirect back to the orders page with a success message
        echo "<script>alert('Selected orders deleted successfully'); window.location.href = 'track_orders.php';</script>";
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $conn->rollback();
        echo "<script>alert('Error deleting orders: " . $e->getMessage() . "'); window.location.href = 'track_orders.php';</script>";
    }

} else {
    echo "<script>alert('No orders selected'); window.location.href = 'track_orders.php';</script>";
}

$conn->close();
?>
