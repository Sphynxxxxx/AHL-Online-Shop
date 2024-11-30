<?php
session_start();
include '../connections/config.php';

// Check if user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: customer.php");
    exit();
}

// Check if there's a recent order reference
if (!isset($_SESSION['last_order_reference'])) {
    header("Location: customer.php");
    exit();
}

$referenceNumber = $_SESSION['last_order_reference'];

// Fetch order details
$query = "
    SELECT 
        o.id AS order_id,
        o.reference_number,
        o.order_date,
        o.delivery_method,
        c.customer_name,
        c.email,
        c.contact_number,
        c.address,
        p.product_name,
        p.image AS product_image,
        od.quantity,
        od.price,
        o.total_price
    FROM 
        orders o
    JOIN 
        customers c ON o.customer_id = c.id
    JOIN 
        order_details od ON o.id = od.order_id
    JOIN 
        products p ON od.product_id = p.product_id
    WHERE 
        o.reference_number = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $referenceNumber);
$stmt->execute();
$result = $stmt->get_result();

// Check if order exists
if ($result->num_rows == 0) {
    header("Location: customer.php");
    exit();
}

// Fetch order details
$orderDetails = [];
$orderInfo = null;
while ($row = $result->fetch_assoc()) {
    if ($orderInfo === null) {
        $orderInfo = [
            'reference_number' => $row['reference_number'],
            'customer_name' => $row['customer_name'],
            'email' => $row['email'],
            'contact_number' => $row['contact_number'],
            'address' => $row['address'],
            'order_date' => $row['order_date'],
            'delivery_method' => $row['delivery_method'],
            'total_price' => $row['total_price']
        ];
    }
    
    $orderDetails[] = [
        'product_name' => $row['product_name'],
        'product_image' => $row['product_image'],
        'quantity' => $row['quantity'],
        'price' => $row['price']
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - AHL Online Store</title>
    <link rel="stylesheet" href="../connections/Assets/customer.css">
    <style>
        .order-confirmation {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .order-details-section {
            margin-top: 20px;
        }
        .order-item {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
            padding: 10px;
            background-color: #fff;
            border-radius: 5px;
        }
        .order-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            margin-right: 20px;
        }
        .order-summary {
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="order-confirmation">
        <div class="order-header">
            <div>
                <h1>Order Confirmation</h1>
                <p><strong>Reference Number:</strong> <?php echo htmlspecialchars($orderInfo['reference_number']); ?></p>
            </div>
            <div>
                <p><strong>Order Date:</strong> <?php echo htmlspecialchars($orderInfo['order_date']); ?></p>
            </div>
        </div>

        <div class="customer-info">
            <h3>Customer Information</h3>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($orderInfo['customer_name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($orderInfo['email']); ?></p>
            <p><strong>Contact Number:</strong> <?php echo htmlspecialchars($orderInfo['contact_number']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($orderInfo['address']); ?></p>
            <p><strong>Delivery Method:</strong> <?php echo htmlspecialchars(ucfirst($orderInfo['delivery_method'])); ?></p>
        </div>

        <div class="order-details-section">
            <h3>Order Items</h3>
            <?php foreach ($orderDetails as $item): ?>
                <div class="order-item">
                    <img src="product_pics/<?php echo htmlspecialchars($item['product_image']); ?>" 
                         alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                         onerror="this.src='product_pics/default_image.jpg';">
                    <div>
                        <h4><?php echo htmlspecialchars($item['product_name']); ?></h4>
                        <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                        <p>Price: ₱<?php echo number_format($item['price'], 2); ?></p>
                        <p>Subtotal: ₱<?php echo number_format($item['quantity'] * $item['price'], 2); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="order-summary">
            <h3>Order Total</h3>
            <p><strong>Total Price:</strong> ₱<?php echo number_format($orderInfo['total_price'], 2); ?></p>
        </div>

        <div class="order-actions">
            <a href="customer.php" class="btn">Back to Dashboard</a>
            <a href="download_pdf.php?order_reference=<?php echo urlencode($orderInfo['reference_number']); ?>" class="btn">Download PDF</a>
        </div>
    </div>
</body>
</html>
<?php 
// Clear the last order reference
unset($_SESSION['last_order_reference']);
$stmt->close();
$conn->close();
?>