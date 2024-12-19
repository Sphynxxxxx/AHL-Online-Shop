<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ahl_user'; 

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    // Validate status input
    if (!in_array($status, ['ready_to_pick_up', 'canceled'])) {
        die("Invalid status.");
    }

    // Update the status of the order
    $sql = "UPDATE orders SET status = ? WHERE reference_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $status, $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Order status updated successfully.'); window.location.href='track_orders.php';</script>";
    } else {
        echo "<script>alert('Error updating order status.'); window.location.href='track_orders.php';</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
