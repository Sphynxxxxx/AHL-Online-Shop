<?php

@include '../connections/config.php';

$id = $_GET['edit'];

if (isset($_POST['update_product'])) {

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_quantity = $_POST['product_quantity'];  // New field for quantity
   $product_category = $_POST['product_category'];  // New field for category
   $product_image = $_FILES['product_image']['name'];
   $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
   $product_image_folder = 'uploaded_img/' . $product_image;

   if (empty($product_name) || empty($product_price) || empty($product_quantity) || empty($product_category)) {
      $message[] = 'Please fill out all fields!';
   } else {
      // Update data query with all fields
      if (empty($product_image)) {
         $update_data = "UPDATE products SET name='$product_name', price='$product_price', quantity='$product_quantity', category='$product_category' WHERE id = '$id'";
      } else {
         $update_data = "UPDATE products SET name='$product_name', price='$product_price', quantity='$product_quantity', category='$product_category', image='$product_image' WHERE id = '$id'";
      }
      
      $upload = mysqli_query($conn, $update_data);

      if ($upload) {
         // If there's a new image, upload it to the server
         if (!empty($product_image)) {
            move_uploaded_file($product_image_tmp_name, $product_image_folder);
         }
         header('location: products.php');
      } else {
         $message[] = 'Failed to update the product!'; 
      }
   }
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="assets/product.css">
</head>
<body>

<?php
   if (isset($message)) {
      foreach ($message as $message) {
         echo '<span class="message">' . $message . '</span>';
      }
   }
?>

<div class="container">
   <div class="admin-product-form-container centered">

      <?php
         $select = mysqli_query($conn, "SELECT * FROM products WHERE id = '$id'");
         while ($row = mysqli_fetch_assoc($select)) {
      ?>

      <form action="" method="post" enctype="multipart/form-data">
         <h3 class="title">Update Product</h3>
         <input type="text" class="box" name="product_name" value="<?php echo $row['name']; ?>" placeholder="Enter Product Name">
         <input type="number" min="0" class="box" name="product_price" value="<?php echo $row['price']; ?>" placeholder="Enter Product Price">
         <input type="number" min="0" class="box" name="product_quantity" value="<?php echo $row['quantity']; ?>" placeholder="Enter Quantity">
         
         <select name="product_category" class="box">
            <option value="" disabled>Select Category</option>
            <option value="notebooks" <?php echo $row['category'] == 'notebooks' ? 'selected' : ''; ?>>Notebooks</option>
            <option value="paper" <?php echo $row['category'] == 'paper' ? 'selected' : ''; ?>>Papers</option>
            <option value="scissors-glue" <?php echo $row['category'] == 'scissors-glue' ? 'selected' : ''; ?>>Scissors, Glue</option>
            <option value="markers" <?php echo $row['category'] == 'markers' ? 'selected' : ''; ?>>Markers</option>
            <option value="pencil" <?php echo $row['category'] == 'pencil' ? 'selected' : ''; ?>>Pencils, Sharpeners, Erasers</option>
            <option value="art-supplies" <?php echo $row['category'] == 'art-supplies' ? 'selected' : ''; ?>>Art Supplies</option>
            <option value="ruler-calculator" <?php echo $row['category'] == 'ruler-calculator' ? 'selected' : ''; ?>>Ruler, Calculator</option>
            <option value="backpack" <?php echo $row['category'] == 'backpack' ? 'selected' : ''; ?>>Backpacks</option>
            <option value="waterbottle" <?php echo $row['category'] == 'waterbottle' ? 'selected' : ''; ?>>Water Bottles</option>
            <option value="lunchbox" <?php echo $row['category'] == 'lunchbox' ? 'selected' : ''; ?>>Lunchbox</option>
         </select>

         <input type="file" accept="image/png, image/jpeg, image/jpg" name="product_image" class="box">
         
         <input type="submit" value="Update Product" name="update_product" class="btn">
         <a href="products.php" class="btn">Go Back</a>
      </form>

      <?php }; ?>

   </div>
</div>

</body>
</html>
