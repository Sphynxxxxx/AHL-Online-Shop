<?php
session_start();


error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['email'])) {
    header("Location: customer.php");
    exit();
}

@include '../connections/config.php'; 


if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}


$userEmail = mysqli_real_escape_string($conn, $_SESSION['email']);
$sql = "SELECT customer_name FROM customers WHERE email = '$userEmail'";

// Execute query and check for errors
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Query failed: " . mysqli_error($conn)); 
}

$userName = 'User';

// Fetch user data
if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $userName = $user['customer_name'];
}

// Fetch products from the database
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : 'all';
$searchQuery = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

$sql = "SELECT * FROM products WHERE 1";
if ($category !== 'all') {
    $sql .= " AND category = '$category'";
}
if ($searchQuery !== '') {
    $sql .= " AND name LIKE '%$searchQuery%'";
}

// Execute the product query
$result = mysqli_query($conn, $sql);
if (!$result) {
    die("Product query failed: " . mysqli_error($conn));
}

// Initialize orderSummary in session if not set
if (!isset($_SESSION['orderSummary'])) {
    $_SESSION['orderSummary'] = [];
}

// Handle product addition to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
  $productId = intval($_POST['product_id']);
  $selectedQuantity = intval($_POST['quantity']);

  // Validate input values
  if ($selectedQuantity <= 0) {
      die("Invalid quantity selected.");
  }

  // Fetch product details from the database
  $orderQuery = "SELECT * FROM products WHERE product_id = $productId";
  $orderResult = mysqli_query($conn, $orderQuery);
  if (!$orderResult) {
      die("Order query failed: " . mysqli_error($conn)); 
  }

  $product = mysqli_fetch_assoc($orderResult);

  // Check if product is available
  if (!$product || $selectedQuantity > $product['quantity']) {
      die("Invalid product or quantity exceeds stock.");
  }

  // Check if product already exists in the cart
  $exists = false;
  foreach ($_SESSION['orderSummary'] as &$item) {
      if ($item['product_id'] === $productId) {
          // Update the quantity if it already exists
          $item['selected_quantity'] += $selectedQuantity;
          $exists = true;
          break;
      }
  }

  // If product doesn't exist, add it to the cart
  if (!$exists) {
      $product['selected_quantity'] = $selectedQuantity;
      $product['status'] = 'Pending'; // Add pickup status
      $_SESSION['orderSummary'][] = $product;
  }
}


// Show the order summary if it's available
$orderSummary = $_SESSION['orderSummary'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AHL Online Store</title>
  <link rel="stylesheet" href="../connections/Assets/customer.css">
  <link rel="stylesheet" href="assets\cart.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <nav>
        <ul>
          <li><a href="profile.php"><i class="fa-solid fa-user"></i></a></li>
          <li><a href="products/cart.php"><i class="fa-solid fa-cart-shopping"></i></a></li>
          <li><a href="view_succesful_orders.php"><i class="fa-solid fa-truck"></i></a></li>
          <li><a href="javascript:void(0)" onclick="logout()"><i class="fa-solid fa-right-from-bracket"></i></a></li>
        </ul>
      </nav>
    </div>

    <div class="main-content">
      <header>
        <div class="user-welcome">
          <h1>AHL Online Store</h1>
          <p>Welcome, <?php echo htmlspecialchars($userName); ?>!</p>
        </div>
        <input type="text" placeholder="Search Product here..." id="search-bar">
      </header>

      <!-- Category Buttons -->
      <div class="menu-categories">
        <button onclick="window.location.href='?category=all'">All</button>
        <button onclick="window.location.href='?category=Notebooks'">Notebooks</button>
        <button onclick="window.location.href='?category=Papers'">Papers</button>
        <button onclick="window.location.href='?category=Scissors, Glue'">Scissors, Glue</button>
        <button onclick="window.location.href='?category=Markers'">Markers</button>
        <button onclick="window.location.href='?category=Pencils, Sharpeners, Erasers'">Pencils, Sharpeners, Erasers</button>
        <button onclick="window.location.href='?category=Art Supplies'">Art Supplies</button>
        <button onclick="window.location.href='?category=Ruler, Calculator'">Ruler, Calculator</button>
        <button onclick="window.location.href='?category=Backpacks'">Backpacks</button>
        <button onclick="window.location.href='?category=Water Bottles'">Water Bottles</button>
        <button onclick="window.location.href='?category=Lunchbox'">Lunchbox</button>
      </div>

      <div class="menu-items" id="menu-items">
          <?php if (mysqli_num_rows($result) > 0): ?>
              <?php while ($row = mysqli_fetch_assoc($result)): ?>
                  <div class="menu-item <?php echo ($row['quantity'] <= 0) ? 'out-of-stock' : ''; ?>">
                      <?php if ($row['quantity'] <= 0): ?>
                          <div class="out-of-stock-label">Out of Stock</div>
                      <?php endif; ?>
                      <div class="product-category"><?php echo htmlspecialchars($row['category']); ?></div>
                      <img 
                          src="product_pics/<?php echo htmlspecialchars($row['image']); ?>" 
                          alt="<?php echo htmlspecialchars($row['product_name']); ?>" 
                          class="expandable-image" 
                          onclick="expandImage(this)">
                      <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
                      <p>PHP <?php echo htmlspecialchars($row['price']); ?></p>
                      <p>Available: <?php echo htmlspecialchars($row['quantity']); ?> in stock</p>
                      <form method="POST">
                          <div class="quantity-control">
                              <button type="button" onclick="decreaseQuantity(<?php echo htmlspecialchars($row['product_id']); ?>)">-</button>
                              <input 
                                  type="number" 
                                  name="quantity" 
                                  value="0" 
                                  min="0" 
                                  max="<?php echo htmlspecialchars($row['quantity']); ?>" 
                                  id="quantity-<?php echo htmlspecialchars($row['product_id']); ?>" 
                                  onchange="updateQuantity(<?php echo htmlspecialchars($row['product_id']); ?>)">
                              <button type="button" onclick="increaseQuantity(<?php echo htmlspecialchars($row['product_id']); ?>)">+</button>
                          </div>
                          <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['product_id']); ?>">
                          <button type="submit" name="place_order" <?php echo ($row['quantity'] <= 0) ? 'disabled' : ''; ?>>Buy Now</button>
                      </form>
                  </div>
              <?php endwhile; ?>
          <?php else: ?>
              <p>No products found for your search or category.</p>
          <?php endif; ?>
      </div>
    </div>

    <div class="image-modal" id="imageModal">
        <button class="close-modal" onclick="closeModal()">×</button>
        <img id="expandedImage" src="" alt="Expanded View">
    </div>

    <!-- Order Summary Section -->
    <div class="order-summary" id="order-summary" style="display: <?php echo !empty($orderSummary) ? 'block' : 'none'; ?>;">
      <h3>Order Summary</h3>
      <div id="order-list">
        <?php if (!empty($orderSummary)): ?>
          <?php 
          $subtotal = 0;
          foreach ($orderSummary as $item): 
            $totalPrice = $item['price'] * $item['selected_quantity'];
            $subtotal += $totalPrice;
          ?>
            <div class="order-item">
              <img src="product_pics/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width: 50px; height: auto;">
              <span><?php echo htmlspecialchars($item['product_name']); ?> (x<?php echo htmlspecialchars($item['selected_quantity']); ?>)</span>
              <span>PHP <?php echo number_format($totalPrice, 2); ?></span>
            </div>
          <?php endforeach; ?>
          <div class="total">
            <p>Subtotal</p>
            <p id="subtotal">PHP <?php echo number_format($subtotal, 2); ?></p>
          </div>
        <?php endif; ?>
      </div>
      <button class="check-out" onclick="openCheckoutModal()">Check Out</button>
    </div>
  </div>

  <!-- Modal for Checkout -->
  
  <div class="modal-overlay" id="modal-overlay"></div>
  <div class="checkout-modal" id="checkout-modal">
    <div class="modal-content">
      <h3>Confirm Your Order</h3>
      <br>
      <p>Pick Up</p>
      <br>
      <form action="process_order.php" method="POST">
        <div id="modal-order-list">
          <?php if (!empty($orderSummary)): ?>
            <?php 
            $subtotal = 0;
            foreach ($orderSummary as $item): 
              $totalPrice = $item['price'] * $item['selected_quantity'];
              $subtotal += $totalPrice;
            ?>
              <div class="order-item">
                <img src="product_pics/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['product_name']); ?>" style="width: 50px; height: auto;">
                <span><?php echo htmlspecialchars($item['product_name']); ?> (x<?php echo htmlspecialchars($item['selected_quantity']); ?>)</span>
                <span>PHP <?php echo number_format($totalPrice, 2); ?></span>
                <!-- Hidden fields to store order data -->
                <input type="hidden" name="product_names[]" value="<?php echo htmlspecialchars($item['product_name']); ?>">
                <input type="hidden" name="quantities[]" value="<?php echo $item['selected_quantity']; ?>">
                <input type="hidden" name="prices[]" value="<?php echo $item['price']; ?>">
                <input type="hidden" name="product_images[]" value="<?php echo htmlspecialchars($item['image']); ?>"> <!-- Store image file name -->
              </div>
            <?php endforeach; ?>
            <div class="total">
              <p>Subtotal</p>
              <p id="modal-subtotal">PHP <?php echo number_format($subtotal, 2); ?></p>
              <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
            </div>
          <?php endif; ?>
        </div>
        <button type="submit">Place Order</button>
      </form>
    </div>

    <div class="image-modal" id="imageModal">
        <button class="close-modal" onclick="closeModal()">×</button>
        <img id="expandedImage" src="" alt="Expanded View">
    </div>
  </div>


  <script src="customer.js"></script>
  <script>
    // Open the checkout modal
    function openCheckoutModal() {
      document.getElementById("modal-overlay").style.display = "block";
      document.getElementById("checkout-modal").style.display = "block";
    }

    // Close the checkout modal
    function closeCheckoutModal() {
      document.getElementById("modal-overlay").style.display = "none";
      document.getElementById("checkout-modal").style.display = "none";
    }

    // Place the order and update the page
    function placeOrder() {
      setTimeout(() => {
        alert("Order placed successfully!");

        // Reload page to reflect new status
        window.location.href = "customer.php";
      }, 500);
    }

  </script>
</body>
</html>

