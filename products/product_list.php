<?php
@include '../connections/config.php'; 

// Handle product deletion
if (isset($_GET['delete'])) {
    $product_id = $_GET['delete'];
    
    // Delete the product from the database
    $delete_query = "DELETE FROM products WHERE product_id = '$product_id'";
    
    if (mysqli_query($conn, $delete_query)) {
        echo '<script>alert("Product deleted successfully!"); window.location.href="product_list.php";</script>';
    } else {
        echo '<script>alert("Error deleting product: ' . mysqli_error($conn) . '");</script>';
    }
}

// Fetch products
$select = mysqli_query($conn, "SELECT * FROM products");

if (!$select) {
    die("Error fetching products: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/product.css">
</head>

<body>
    <div class="back-button-container">
        <a href="products.php" class="back-button"><i class="fa-solid fa-house"></i></a>
    </div>
    <div class="product-display">
        <table class="product-display-table">
            <thead>
                <tr>
                    <th>Product Image</th>
                    <th>Product Name</th>
                    <th>Product Price</th>
                    <th>Quantity</th>
                    <th>Category</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($select)) { ?>
                    <tr>
                        <td><img src="product_pics/<?php echo htmlspecialchars($row['image']); ?>" height="100" alt=""></td>
                        <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                        <td>₱<?php echo htmlspecialchars($row['price']); ?></td>
                        <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($row['category']); ?></td>
                        <td>
                            <a href="update_prod.php?edit=<?php echo $row['product_id']; ?>" class="btn"> 
                                <i class="fas fa-edit"></i> Edit 
                            </a>
                            <a href="product_list.php?delete=<?php echo $row['product_id']; ?>" class="btn btn-danger" 
                               onclick="return confirm('Are you sure you want to delete this product?');"> 
                               <i class="fa-solid fa-trash"></i> Delete 
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</body>
</html>
