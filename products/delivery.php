<?php

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ahl_user'; 

$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to retrieve data from the database
$sql = "
    SELECT 
        p.image AS product_image,
        p.product_name,
        c.customer_name AS customer_name,
        od.price,  
        od.shippingfee,
        c.address,
        c.contact_number,
        o.order_date,
        o.delivery_method,
        o.reference_number
    FROM 
        order_details od
    JOIN 
        orders o ON od.order_id = o.id
    JOIN 
        customers c ON o.customer_id = c.id
    JOIN 
        products p ON od.product_id = p.product_id
    ORDER BY 
        o.order_date DESC
";

$result = $conn->query($sql);

// Check if query executed successfully
if (!$result) {
    die("Error executing query: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 90%;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        header {
            margin-bottom: 20px;
        }

        header a {
            text-decoration: none;
        }

        button {
            padding: 10px 15px;
            font-size: 16px;
            background-color: black;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #007bff;
        }

        .order-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .order-table th, .order-table td {
            padding: 15px;
            text-align: left;
            font-size: 14px;
            border-bottom: 1px solid #ddd;
        }

        .order-table th {
            background-color: black;
            color: #fff;
            text-transform: uppercase;
        }

        .order-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .order-table tr:hover {
            background-color: #f1f1f1;
        }

        .order-table .product-img {
            max-width: 100px;
            height: auto;
            border-radius: 8px;
        }

        .order-table td {
            vertical-align: middle;
        }

        /* Responsive Styling */
        @media (max-width: 768px) {
            .order-table th, .order-table td {
                font-size: 12px;
                padding: 10px;
            }

            .order-table .product-img {
                width: 80px;
                height: auto;
            }

            button {
                font-size: 14px;
                padding: 8px 12px;
            }
        }

        @media (max-width: 480px) {
            .order-table th, .order-table td {
                font-size: 10px;
                padding: 8px;
            }

            button {
                font-size: 12px;
                padding: 6px 10px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="main-content">
        <header>
            <!-- Back button -->
            <a href="../admin.php">
                <button type="button">Back</button>
            </a>
        </header>

        <?php
        if ($result->num_rows > 0) {
            // Table header
            echo "
            <table class='order-table'>
                <thead>
                    <tr>
                        <th>Product Image</th>
                        <th>Product Name</th>
                        <th>Customer Name</th>
                        <th>Price</th>
                        <th>Shipping Fee</th>
                        <th>Address</th>
                        <th>Contact Number</th>
                        <th>Order Date</th>
                        <th>Delivery Method</th>
                        <th>Reference Number</th>
                    </tr>
                </thead>
                <tbody>
            ";

            // Fetch and display rows
            while ($row = $result->fetch_assoc()) {
                echo "
                <tr>
                    <td><img src='product_pics/{$row['product_image']}' alt='{$row['product_name']}' class='product-img' onerror=\"this.src='uploaded_img/default_image.jpg';\"></td>
                    <td>{$row['product_name']}</td>
                    <td>{$row['customer_name']}</td>
                    <td>₱" . number_format($row['price'], 2) . "</td>
                    <td>₱" . number_format($row['shippingfee'], 2) . "</td>
                    <td>{$row['address']}</td>
                    <td>{$row['contact_number']}</td>
                    <td>{$row['order_date']}</td>
                    <td>" . ucfirst($row['delivery_method']) . "</td>
                    <td>{$row['reference_number']}</td>
                </tr>
                ";
            }

            echo "</tbody></table>";
        } else {
            echo "<p>No orders found.</p>";
        }

        $conn->close();
        ?>

    </div>
</div>

</body>
</html>
