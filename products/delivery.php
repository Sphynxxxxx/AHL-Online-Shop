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

echo "<div class='container'>";
echo "<div class='main-content'>";
echo "<header>";

// Back button
echo "<a href='../admin.php'>
        <button type='button'>Back</button>
      </a>";

echo "</header>";

if ($result->num_rows > 0) {
    // Table header
    echo "
    <table border='1' cellpadding='10' cellspacing='0'>
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
    ";

    // Fetch and display rows
    while ($row = $result->fetch_assoc()) {
        echo "
        <tr>
            <td><img src='product_pics/{$row['product_image']}' alt='{$row['product_name']}' width='100' height='100' onerror=\"this.src='uploaded_img/default_image.jpg';\"></td>
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

    echo "</table>";
} else {
    echo "<p>No orders found.</p>";
}

echo "</div>";  
echo "</div>";  

$conn->close();
?>
