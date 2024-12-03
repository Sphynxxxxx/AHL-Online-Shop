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
        body {
            font-family: Arial, sans-serif;
            background-color: #F6EEE0;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .order-confirmation {
            max-width: 600px;
            background-color: white;
            border: 2px dashed #ccc;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            font-size: 14px;
            color: #333;
        }

        .order-header {
            text-align: center;
            border-bottom: 2px dashed #ccc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .order-header h1 {
            font-size: 20px;
            margin-bottom: 10px;
            color: #333;
        }

        .customer-info,
        .order-details-section,
        .order-summary {
            margin-bottom: 20px;
        }

        h3 {
            font-size: 16px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-bottom: 10px;
            color: #555;
        }

        p {
            margin: 5px 0;
            font-size: 14px;
            line-height: 1.6;
        }

        .order-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .order-item img {
            width: 80px;
            height: 80px;
            margin-right: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .order-item div {
            flex-grow: 1;
        }

        .order-summary {
            text-align: right;
            border-top: 2px dashed #ccc;
            padding-top: 10px;
        }

        .order-summary p {
            margin: 5px 0;
        }

        .order-actions {
            text-align: center;
            margin-top: 20px;
        }

        .order-actions .btn {
            text-decoration: none;
            background-color: #2d1d1d;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin: 5px;
            display: inline-block;
        }

        .order-actions .btn:hover {
            background-color: #0056b3;
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
