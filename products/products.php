<?php
@include '../connections/config.php'; 

if(isset($_POST['add_product'])){

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_quantity = $_POST['product_quantity']; 
   $product_category = $_POST['product_category']; 
   $product_image = $_FILES['product_image']['name'];
   $product_image_tmp_name = $_FILES['product_image']['tmp_name'];
   $product_image_folder = 'product_pics/'.$product_image;

   if(empty($product_name) || empty($product_price) || empty($product_image) || empty($product_quantity) || empty($product_category)){
      $message[] = 'Please fill out all fields';
   }else{
      // Insert product into the database
      $insert = "INSERT INTO products(name, price, quantity, category, image) 
                 VALUES('$product_name', '$product_price', '$product_quantity', '$product_category', '$product_image')";
      $upload = mysqli_query($conn, $insert);
      
      if($upload){
         // Move the uploaded image to the target folder
         move_uploaded_file($product_image_tmp_name, $product_image_folder);
         $message[] = 'New product added successfully';
         
         // Redirect to prevent re-submission on page refresh
         header('Location: ' . $_SERVER['PHP_SELF']);
         exit(); // Make sure to exit after redirect
      }else{
         $message[] = 'Could not add the product';
      }
   }

};

if(isset($_GET['delete'])){
   $id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM products WHERE id = $id");
   header('location: products.php');
};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Page</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
   <link rel="stylesheet" href="assets/product.css">

</head>
<body>

<?php
if(isset($message)){
   foreach($message as $message){
      echo '<span class="message">'.$message.'</span>';
   }
}
?>
   
<div class="container">
   <!-- Add Product Form -->
   <div class="admin-product-form-container">
      <form action="<?php $_SERVER['PHP_SELF'] ?>" method="post" enctype="multipart/form-data">
         <h3>Add a New Product</h3>
         <input type="text" placeholder="Enter Product name" name="product_name" class="box">
         <input type="number" placeholder="Enter Rent price" name="product_price" class="box">
         <input type="number" placeholder="Enter Quantity" name="product_quantity" class="box"> <!-- New input for quantity -->
         <select name="product_category" class="box">
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
         </select> <!-- New dropdown for category -->
         <input type="file" accept="image/png, image/jpeg, image/jpg" name="product_image" class="box">
         <input type="submit" class="btn" name="add_product" value="Add Product">
      </form>
   </div>

   <?php
   // Fetch products from the database
   $select = mysqli_query($conn, "SELECT * FROM products");
   ?>

   <!-- Display Products -->
   <div class="product-display">
      <table class="product-display-table">
         <thead>
            <tr>
               <th>Product Image</th>
               <th>Product Name</th>
               <th>Rent Price</th>
               <th>Quantity</th> <!-- New column for quantity -->
               <th>Category</th> <!-- New column for category -->
               <th>Action</th>
            </tr>
         </thead>
         <?php while($row = mysqli_fetch_assoc($select)){ ?>
         <tr>
            <td><img src="product_pics/<?php echo $row['image']; ?>" height="100" alt=""></td>
            <td><?php echo $row['name']; ?></td>
            <td>₱<?php echo $row['price']; ?></td>
            <td><?php echo $row['quantity']; ?></td> <!-- Show the quantity -->
            <td><?php echo $row['category']; ?></td> 
            <td>
               <a href="update_prod.php?edit=<?php echo $row['id']; ?>" class="btn"> <i class="fas fa-edit"></i> Edit </a>
               <a href="products.php?delete=<?php echo $row['id']; ?>" class="btn"> <i class="fas fa-trash"></i> Delete </a>
            </td>
         </tr>
      <?php } ?>
      </table>
   </div>
</div>

</body>
</html>
