<?php

@include 'connections/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="connections\Assets\adminDashboard.css?v=1.0">
</head>
<body>


    <!-- Main Contentqwq -->
    <div class="main-content">
        <h1>Welcome to Admin Dashboard</h1>
        <p>Here you can manage products, customers, and approvals.</p>

        
        <div class="quick-stats">
            <div class="card">
                <h3>Products</h3>
                <p>Manage and Update product listings.</p>
                <a href="products/products.php" class="btn">View Products</a>
            </div>
            <div class="card">
                <h3>Orders</h3>
                <p>Track the orders.</p>
                <a href="products/delivery.php" class="btn">View Customers</a>
            </div>
            <div class="card">
                <h3>Approvals</h3>
                <p>Approve or decline pending applications.</p>
                <a href="connections\adminApprove.php" class="btn">Manage Approvals</a>
            </div>
        </div>

        
        <div class="help-tips">
            <h2>Quick Tips to Get Started</h2>
            <ul>
                <li>Make sure to review all pending approvals in the "Approvals" section.</li>
                <li>Click on the "Products" section to start managing your product listings.</li>
                <li>For customer support, visit the "Settings" page for contact information.</li>
                <li>Review User information and approve or decline registrations promptly.</li>
            </ul>
        </div>
    </div>

    
    

</body>
</html>
