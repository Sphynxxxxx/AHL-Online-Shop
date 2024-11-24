<?php
@include 'config.php';

// Approve lender
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $stmt = $conn->prepare("UPDATE customers SET status = 'approved' WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header('Location: adminApprove.php');
        exit();
    } else {
        error_log("Error updating record: " . $conn->error);
        echo "Something went wrong. Please try again later.";
    }
    $stmt->close();
}

// Decline reg
if (isset($_GET['decline'])) {
    $id = intval($_GET['decline']);
    $stmt = $conn->prepare("UPDATE customers SET status = 'declined' WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header('Location: adminApprove.php');
        exit();
    } else {
        error_log("Error updating record: " . $conn->error);
        echo "Something went wrong. Please try again later.";
    }
    $stmt->close();
}

// Delete reg
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $select_image = $conn->prepare("SELECT images FROM customers WHERE id = ?");
    $select_image->bind_param("i", $id);
    $select_image->execute();
    $result = $select_image->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $image_path = 'uploads_img/' . $row['images'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    $delete_stmt = $conn->prepare("DELETE FROM customers WHERE id = ?");
    $delete_stmt->bind_param("i", $id);
    if ($delete_stmt->execute()) {
        header('Location: adminApprove.php');
        exit();
    } else {
        error_log("Error deleting record: " . $conn->error);
        echo "Something went wrong. Please try again later.";
    }
    $delete_stmt->close();
}

// Fetch pending user
$pending_result = $conn->query("SELECT * FROM customers WHERE status = 'pending'");

// Fetch approved user
$approved_result = $conn->query("SELECT * FROM customers WHERE status = 'approved'");

// Fetch declined user
$declined_result = $conn->query("SELECT * FROM customers WHERE status = 'declined'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Approval</title>
    <link rel="stylesheet" href="Assets/adminApproval.css?v=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>

    <a href="../admin.php" class="sidebar-link">
        <i class="fa-solid fa-house icon"></i>
    </a>

    <h2>Admin Approval</h2>
    <div class="container">

        <!-- Pending Users -->
        <div class="table">
            <h2>Pending Registrations</h2>
            <?php if ($pending_result && $pending_result->num_rows > 0): ?>
                <ul>
                    <?php while ($row = $pending_result->fetch_assoc()): ?>
                        <li>
                            <?php echo htmlspecialchars($row['customer_name']); ?> - 
                            <?php echo htmlspecialchars($row['contact_number']); ?> - 
                            <?php echo htmlspecialchars($row['address']); ?> - 
                            <?php echo htmlspecialchars($row['email']); ?>
                            <img src="<?php echo htmlspecialchars($row['images']); ?>" alt="Image" style="width:200px;height:200px;">
                            <a href="?approve=<?php echo $row['id']; ?>">Approve</a> | 
                            <a href="?decline=<?php echo $row['id']; ?>">Decline</a> |
                            <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this lender?');">Delete</a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No pending registrations.</p>
            <?php endif; ?>
        </div>

        <!-- Approved Users -->
        <div class="table">
            <h2>Verified Registrations</h2>
            <?php if ($approved_result && $approved_result->num_rows > 0): ?>
                <ul>
                    <?php while ($row = $approved_result->fetch_assoc()): ?>
                        <li>
                            <?php echo htmlspecialchars($row['customer_name']); ?> - 
                            <?php echo htmlspecialchars($row['contact_number']); ?> - 
                            <?php echo htmlspecialchars($row['address']); ?> - 
                            <?php echo htmlspecialchars($row['email']); ?>
                            <img src="<?php echo htmlspecialchars($row['images']); ?>" alt="Image" style="width:200px;height:200px;">
                            <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this lender?');">Delete</a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No approved registrations.</p>
            <?php endif; ?>
        </div>

        <!-- Declined Users -->
        <div class="table">
            <h2>Declined Registrations</h2>
            <?php if ($declined_result && $declined_result->num_rows > 0): ?>
                <ul>
                    <?php while ($row = $declined_result->fetch_assoc()): ?>
                        <li>
                            <?php echo htmlspecialchars($row['customer_name']); ?> - 
                            <?php echo htmlspecialchars($row['contact_number']); ?> - 
                            <?php echo htmlspecialchars($row['address']); ?> - 
                            <?php echo htmlspecialchars($row['email']); ?>
                            <img src="<?php echo htmlspecialchars($row['images']); ?>" alt="Image" style="width:200px;height:200px;">
                            <a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this lender?');">Delete</a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            <?php else: ?>
                <p>No declined registrations.</p>
            <?php endif; ?>
        </div>

    </div>

    <?php $conn->close(); ?>

</body>
</html>
