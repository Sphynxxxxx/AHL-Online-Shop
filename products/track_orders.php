<?php

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'ahl_user'; 

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
    SELECT 
        p.image AS product_image,
        p.product_name,
        c.customer_name AS customer_name,
        od.price,  
        od.quantity,
        c.address,
        c.contact_number,
        o.order_date,
        o.delivery_method,
        o.reference_number,
        o.status
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

if (!$result) {
    die("Error executing query: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <title>Track Orders</title>
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

        .back-button {
            text-decoration: none;
            color: #000000;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 30px;
            transition: background-color 0.3s;
        }

        .back-button i {
            margin-right: 5px;
        }

        .back-button:hover {
            color: #0056b3;
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

        .delete-btn {
            color: red;
            border: none;
            background-color: transparent ;
            font-size: 20px;
        }

        .delete-btn:hover {
            color: darkred;
        }


        .ready-to-pick-up-btn {
            background-color: green;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
            margin-bottom: 10px;
        }

        .ready-to-pick-up-btn:hover {
            background-color: darkgreen;
        }

        .cancel-btn {
            background-color: red;
            color: white;
            padding: 5px 10px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
        }

        .cancel-btn:hover {
            background-color: darkred;
        }

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
            <a href="..\admin.php" class="back-button"><i class="fa-solid fa-house"></i></a>
        </header>
        <h1>Track Orders</h1>

        <?php
        if ($result->num_rows > 0) {
            // Table header
            echo "
            <table class='order-table'>
                <thead>
                    <tr>
                        <th>Actions</th> <!-- Added for the delete icon -->
                        <th>Product Image</th>
                        <th>Product Name</th>
                        <th>Customer Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Address</th>
                        <th>Contact Number</th>
                        <th>Order Date</th>
                        <th>Delivery Method</th>
                        <th>Reference Number</th>
                        <th>Status</th> <!-- Added Status column -->
                        <th>Actions</th> <!-- Updated column for the action buttons -->
                    </tr>
                </thead>
                <tbody>
            ";

            // Fetch and display rows
            while ($row = $result->fetch_assoc()) {
                $status = ucfirst(str_replace('_', ' ', $row['status'])); // Convert status to a readable format
                echo "
                <tr>
                    <td>
                        <!-- Trash icon for deleting an order -->
                        <form action='delete_order.php' method='POST' style='display:inline;'>
                            <input type='hidden' name='order_id' value='{$row['reference_number']}'>
                            <button type='submit' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this order?\")'>
                                <i class='fa-solid fa-trash'></i>
                            </button>
                        </form>
                    </td>
                    <td><img src='product_pics/{$row['product_image']}' alt='{$row['product_name']}' class='product-img' onerror=\"this.src='uploaded_img/default_image.jpg';\"></td>
                    <td>{$row['product_name']}</td>
                    <td>{$row['customer_name']}</td>
                    <td>₱" . number_format($row['price'], 2) . "</td>
                    <td>" . number_format($row['quantity']) . "</td>
                    <td>{$row['address']}</td>
                    <td>{$row['contact_number']}</td>
                    <td>{$row['order_date']}</td>
                    <td>" . ucfirst($row['delivery_method']) . "</td>
                    <td>{$row['reference_number']}</td>
                    <td>{$status}</td> <!-- Display order status -->
                    <td>
                        <form action='update_order_status.php' method='POST'>
                            <input type='hidden' name='order_id' value='{$row['reference_number']}'>
                            <button type='submit' name='status' value='ready_to_pick_up' class='ready-to-pick-up-btn' onclick='return confirm(\"Are you sure you want to mark this order as ready to pick up?\")'>Ready to Pick Up</button>
                            <button type='submit' name='status' value='canceled' class='cancel-btn' onclick='return confirm(\"Are you sure you want to cancel this order?\")'>Cancel</button>
                        </form>
                    </td>
                </tr>
                ";
            }

            echo "</tbody></table>";
        } else {
            echo "<p>No orders found.</p>";
        }

        $conn->close();
        
