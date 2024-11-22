<?php
session_start();

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['email'])) {
    header("Location: customer.php");
    exit();
}

// Check if the cart session is set, otherwise initialize it
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle cart removal if needed
if (isset($_GET['remove'])) {
    $productId = $_GET['remove'];
    unset($_SESSION['cart'][$productId]);
    header("Location: cart.php");
    exit();
}

// Handle quantity update if needed
if (isset($_POST['update_cart'])) {
    $productId = $_POST['product_id'];
    $newQuantity = $_POST['quantity'];
    
    if ($newQuantity > 0) {
        $_SESSION['cart'][$productId]['quantity'] = $newQuantity;
    }
    header("Location: cart.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Cart</title>
  <link rel="stylesheet" href="connections/Assets/customer.css">
  <style>
    table {
      width: 100%;
      border-collapse: collapse;
      margin: 20px 0;
    }

    th, td {
      padding: 10px;
      text-align: left;
      border: 1px solid #ddd;
    }

    th {
      background-color: #f4f4f4;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    tr:hover {
      background-color: #f1f1f1;
    }

    .container {
      padding: 20px;
    }

    .actions button {
      padding: 5px 10px;
      margin: 5px;
      background-color: #f44336;
      color: white;
      border: none;
      cursor: pointer;
    }

    .actions button:hover {
      background-color: #d32f2f;
    }

    .update-quantity input {
      width: 50px;
      text-align: center;
    }

    .product-image img {
      width: 100px; /* Set the size for the image */
      height: auto;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>My Cart</h1>
    <?php if (!empty($_SESSION['cart'])): ?>
      <form method="POST" action="cart.php">
        <table>
          <thead>
            <tr>
              <th>Image</th>
              <th>Product</th>
              <th>Price</th>
              <th>Quantity</th>
              <th>Total</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php 
            $grandTotal = 0;
            foreach ($_SESSION['cart'] as $productId => $product): 
                $totalPrice = $product['price'] * $product['quantity'];
                $grandTotal += $totalPrice;
                
                // Check for both JPG and PNG file extensions
                $imagePathJpg = 'product_pics/' . $productId . '.jpg'; 
                $imagePathPng = 'product_pics/' . $productId . '.png';
                
                // If .jpg file exists, use it; otherwise, check for .png
                if (file_exists($imagePathJpg)) {
                    $imagePath = $imagePathJpg;
                } elseif (file_exists($imagePathPng)) {
                    $imagePath = $imagePathPng;
                } else {
                    // Fallback image if neither exists
                    $imagePath = 'product_pics/default.png';
                }
            ?>
              <tr>
                <td class="product-image">
                  <img src="<?php echo $imagePath; ?>" alt="Product Image">
                </td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td>PHP <?php echo number_format($product['price'], 2); ?></td>
                <td class="update-quantity">
                  <input type="number" name="quantity" value="<?php echo $product['quantity']; ?>" min="1" max="<?php echo $product['quantity']; ?>" required>
                  <input type="hidden" name="product_id" value="<?php echo $productId; ?>">
                </td>
                <td>PHP <?php echo number_format($totalPrice, 2); ?></td>
                <td class="actions">
                  <a href="cart.php?remove=<?php echo $productId; ?>">
                    <button type="button">Remove</button>
                  </a>
                  <button type="submit" name="update_cart">Update Quantity</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr>
              <td colspan="4"><strong>Grand Total</strong></td>
              <td colspan="2">PHP <?php echo number_format($grandTotal, 2); ?></td>
            </tr>
          </tfoot>
        </table>
      </form>
    <?php else: ?>
      <p>Your cart is empty.</p>
    <?php endif; ?>
  </div>
</body>
</html>
