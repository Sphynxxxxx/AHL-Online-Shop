<?php
@include '../connections/config.php'; 

// Handle Add Product Form
if (isset($_POST['add_product'])) {
    $product_name = $_POST['product_name'] ?? '';
    $product_price = $_POST['product_price'] ?? '';
    $product_quantity = $_POST['product_quantity'] ?? '';
    $product_category = $_POST['product_category'] ?? '';
    $product_image = $_FILES['product_image']['name'] ?? '';
    $product_image_tmp_name = $_FILES['product_image']['tmp_name'] ?? '';
    $product_image_folder = 'product_pics/' . $product_image;

    if (empty($product_name) || empty($product_price) || empty($product_image) || empty($product_quantity) || empty($product_category)) {
        $message[] = 'Please fill out all fields';
    } else {
        $insert = "INSERT INTO products (product_name, price, quantity, category, image) 
                   VALUES ('$product_name', '$product_price', '$product_quantity', '$product_category', '$product_image')";

        $upload = mysqli_query($conn, $insert);

        if ($upload) {
            if (move_uploaded_file($product_image_tmp_name, $product_image_folder)) {
                $message[] = 'New product added successfully';
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $message[] = 'Failed to upload product image.';
            }
        } else {
            $message[] = 'Database error: ' . mysqli_error($conn);
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
    <title>Admin Page</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* General Page Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        .container {
            margin: 20px auto;
            max-width: 1200px;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h3 {
            font-size: 24px;
            margin-bottom: 20px;
            text-align: center;
            color: #333;
        }

        .box {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }

        .btn {
            display: inline-block;
            background-color: black;
            color: #fff;
            padding: 10px 20px;
            font-size: 16px;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            margin-top: 10px;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        .message {
            display: block;
            background: #ffefc2;
            color: #555;
            padding: 10px;
            margin: 15px 0;
            border-left: 5px solid #ffc107;
            font-size: 14px;
        }

        /* Back Button */
        .back-button-container {
            margin: 10px 0;
        }

        .back-button {
            text-decoration: none;
            color: black;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 20px;
        }

        .back-button:hover {
            color:  #0056b3;
        }

        .back-button i {
            font-size: 30px;
        }
        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        thead {
            background-color: #007BFF;
            color: #fff;
        }

        th, td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            font-size: 14px;
            text-transform: uppercase;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        td {
            font-size: 16px;
        }

        img {
            max-width: 80px;
            border-radius: 5px;
        }

        .btn-action {
            margin-right: 5px;
            padding: 5px 10px;
            font-size: 14px;
            border-radius: 3px;
        }

        .btn-edit {
            background-color: #ffc107;
            color: white;
        }

        .btn-edit:hover {
            background-color: #e0a800;
        }

        .btn-delete {
            background-color: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>

<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '<span class="message">' . htmlspecialchars($msg) . '</span>';
    }
}
?>

<div class="back-button-container">
    <a href="../admin.php" class="back-button"><i class="fa-solid fa-house"></i></a>
</div>

<div class="container">
    <!-- Add Product Form -->
    <div class="admin-product-form-container">
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
            <h3>Add a New Product</h3>
            <input type="text" placeholder="Enter Product name" name="product_name" class="box" required>
            <input type="number" placeholder="Enter Product price" name="product_price" class="box" required>
            <input type="number" placeholder="Enter Quantity" name="product_quantity" class="box" required>
            <select name="product_category" class="box" required>
                <option value="" disabled selected>Select Category</option>
                <option value="Notebooks">Notebooks</option>
                <option value="Papers">Papers</option>
                <option value="Scissors, Glue">Scissors, Glue</option>
                <option value="Markers">Markers</option>
                <option value="Pencils, Sharpeners, Erasers">Pencils, Sharpeners, Erasers</option>
                <option value="Art Supplies">Art Supplies</option>
                <option value="Ruler, Calculator">Ruler, Calculator</option>
                <option value="Backpacks">Backpacks</option>
                <option value="Water Bottles">Water Bottles</option>
                <option value="Lunchbox">Lunchbox</option>
            </select>
            <input type="file" accept="image/png, image/jpeg, image/jpg" name="product_image" class="box" required>
            <input type="submit" class="btn" name="add_product" value="Add Product">
            <a href="product_list.php" class="btn">View Product List</a>
        </form>
    </div>
</div>

</body>
</html>
