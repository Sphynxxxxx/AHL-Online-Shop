<?php
session_start();
include '../connections/config.php';

if (!isset($_SESSION['email'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

if (isset($_POST['order_id'])) {
    $orderReferenceNumber = $_POST['order_id'];

    $conn->begin_transaction();

    try {
        $deleteOrderDetails = "DELETE FROM order_details WHERE order_id = (SELECT id FROM orders WHERE reference_number = ?)";
        $stmt = $conn->prepare($deleteOrderDetails);
        $stmt->bind_param("s", $orderReferenceNumber);
        $stmt->execute();

        $deleteOrder = "DELETE FROM orders WHERE reference_number = ?";
        $stmt = $conn->prepare($deleteOrder);
        $stmt->bind_param("s", $orderReferenceNumber);
        $stmt->execute();

        $conn->commit();

        echo "<script>alert('Order deleted successfully'); window.location.href = 'track_orders.php';</script>";
    } catch (Exception $e) {
        $conn->rollback();
        echo "<script>alert('Error deleting order: " . $e->getMessage() . "'); window.location.href = 'track_orders.php';</script>";
    }

    $conn->close();
} else {
    echo "<script>alert('Invalid request'); window.location.href = 'track_orders.php';</script>";
}
?>
