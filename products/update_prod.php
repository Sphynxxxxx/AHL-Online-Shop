<?php

@include '../connections/config.php';

if (!isset($_GET['edit'])) {
    die('No product selected for editing.');
}


$product_id = $_GET['edit'];

if (isset($_POST['update_product'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_quantity = $_POST['product_quantity'];
    $product_category = $_POST['product_category'];
    $product_image = $_FILES['product_image']['name'];
    $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
    $product_image_folder = 'product_pics/' . $product_image;

    if (empty($product_name) || empty($product_price) || empty($product_quantity) || empty($product_category)) {
        $message[] = 'Please fill out all fields!';
    } else {
        if (empty($product_image)) {
            $update_query = "UPDATE products SET product_name='$product_name', price='$product_price', quantity='$product_quantity', category='$product_category' WHERE product_id = '$product_id'";
        } else {
            $update_query = "UPDATE products SET product_name='$product_name', price='$product_price', quantity='$product_quantity', category='$product_category', image='$product_image' WHERE product_id = '$product_id'";
        }

        $upload = mysqli_query($conn, $update_query);

        if ($upload) {
            if (!empty($product_image)) {
                move_uploaded_file($product_image_tmp_name, $product_image_folder);
            }
            header('location: products.php');
        } else {
            $message[] = 'Failed to update the product!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/product.css">
    <title>Update Product</title>
</head>
<body>

<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '<span class="message">' . $msg . '</span>';
    }
}
?>

<div class="container">
    <div class="admin-product-form-container centered">

        <?php
        $select_query = "SELECT * FROM products WHERE product_id = '$product_id'";
        $select = mysqli_query($conn, $select_query);

        if (mysqli_num_rows($select) > 0) {
            while ($row = mysqli_fetch_assoc($select)) {
        ?>

        <form action="" method="post" enctype="multipart/form-data">
            <h3 class="title">Update Product</h3>
            <input type="text" class="box" name="product_name" value="<?php echo htmlspecialchars($row['product_name']); ?>" placeholder="Enter Product Name">
            <input type="number" min="0" class="box" name="product_price" value="<?php echo htmlspecialchars($row['price']); ?>" placeholder="Enter Product Price">
            <input type="number" min="0" class="box" name="product_quantity" value="<?php echo htmlspecialchars($row['quantity']); ?>" placeholder="Enter Quantity">
            
            <select name="product_category" class="box">
                <option value="" disabled>Select Category</option>
                <option value="Notebooks" <?php echo $row['category'] == 'Notebooks' ? 'selected' : ''; ?>>Notebooks</option>
                <option value="Papers" <?php echo $row['category'] == 'Papers' ? 'selected' : ''; ?>>Papers</option>
                <option value="Scissors, Glue" <?php echo $row['category'] == 'Scissors, Glue' ? 'selected' : ''; ?>>Scissors, Glue</option>
                <option value="Markers" <?php echo $row['category'] == 'Markers' ? 'selected' : ''; ?>>Markers</option>
                <option value="Pencils, Sharpeners, Erasers" <?php echo $row['category'] == 'Pencils, Sharpeners, Erasers' ? 'selected' : ''; ?>>Pencils, Sharpeners, Erasers</option>
                <option value="Art Supplies" <?php echo $row['category'] == 'Art Supplies' ? 'selected' : ''; ?>>Art Supplies</option>
                <option value="Ruler, Calculator" <?php echo $row['category'] == 'Ruler, Calculator' ? 'selected' : ''; ?>>Ruler, Calculator</option>
                <option value="Backpacks" <?php echo $row['category'] == 'Backpacks' ? 'selected' : ''; ?>>Backpacks</option>
                <option value="Water Bottles" <?php echo $row['category'] == 'Water Bottles' ? 'selected' : ''; ?>>Water Bottles</option>
                <option value="Lunchbox" <?php echo $row['category'] == 'Lunchbox' ? 'selected' : ''; ?>>Lunchbox</option>
            </select>

            <input type="file" accept="image/png, image/jpeg, image/jpg" name="product_image" class="box">
            
            <input type="submit" value="Update Product" name="update_product" class="btn">
            <a href="products.php" class="btn">Go Back</a>
        </form>

        <?php
            }
        } else {
            echo '<p class="message">No product found for the given ID.</p>';
        }
        ?>

    </div>
</div>

</body>
</html>
