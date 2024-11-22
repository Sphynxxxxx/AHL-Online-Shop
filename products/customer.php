<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: customer.php");
    exit();
}

@include '../connections/config.php';

$userEmail = htmlspecialchars($_SESSION['email']);
$sql = "SELECT name FROM users WHERE email = '$userEmail'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
$userName = $user ? $user['name'] : 'User';

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
$result = mysqli_query($conn, $sql);

// Initialize orderSummary in session
if (!isset($_SESSION['orderSummary'])) {
    $_SESSION['orderSummary'] = [];
}

// Handle product addition to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $productId = intval($_POST['product_id']);
    $selectedQuantity = intval($_POST['quantity']);
    
    // Fetch product details from the database
    $orderQuery = "SELECT * FROM products WHERE id = $productId";
    $orderResult = mysqli_query($conn, $orderQuery);
    $product = mysqli_fetch_assoc($orderResult);

    if ($product && $selectedQuantity > 0 && $selectedQuantity <= $product['quantity']) {
        // Check if product already exists in cart
        $exists = false;
        foreach ($_SESSION['orderSummary'] as &$item) {
            if ($item['id'] === $product['id']) {
                $item['selected_quantity'] += $selectedQuantity; // Update quantity
                $exists = true;
                break;
            }
        }

        if (!$exists) {
            // Add new product to order summary
            $product['selected_quantity'] = $selectedQuantity;
            $product['status'] = 'Pending'; // Add pickup status
            $_SESSION['orderSummary'][] = $product;
        }
    } else {
        echo "<script>alert('Invalid quantity selected or product not available.');</script>";
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
          <li><a href="javascript:void(0);" onclick="openPickupStatusModal()"><i class="fa-solid fa-truck"></i></a></li>
          <div class="modal-overlay" id="modal-overlay" style="display: none;"></div>
          <div class="pickup-status-modal" id="pickup-status-modal" style="display: none;">
            <div class="modal-content">
              <h3>Status</h3>
              <p>Your pickup status is: <strong>Pending</strong></p> 
              <button onclick="closePickupStatusModal()">Close</button>
            </div>
          </div>
          <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i></a></li>
        </ul>
      </nav>
    </div>

    <div class="main-content">
      <header>
        <div class="user-welcome">
          <h1>AHL Online Store</h1>
          <p>Welcome, <?php echo $userName; ?>!</p>
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
            <div class="menu-item">
              <div class="product-category"><?php echo htmlspecialchars($row['category']); ?></div>
              <img src="product_pics/<?php echo htmlspecialchars($row['image']); ?>" alt="<?php echo htmlspecialchars($row['name']); ?>">
              <h3><?php echo htmlspecialchars($row['name']); ?></h3>
              <p>PHP <?php echo htmlspecialchars($row['price']); ?></p>
              <p>Available: <?php echo htmlspecialchars($row['quantity']); ?> in stock</p>
              <form method="POST">
                <div class="quantity-control">
                  <button type="button" onclick="decreaseQuantity(<?php echo htmlspecialchars($row['id']); ?>)">-</button>
                  <input type="number" name="quantity" value="0" min="0" max="<?php echo htmlspecialchars($row['quantity']); ?>" id="quantity-<?php echo htmlspecialchars($row['id']); ?>" onchange="updateQuantity(<?php echo htmlspecialchars($row['id']); ?>)">
                  <button type="button" onclick="increaseQuantity(<?php echo htmlspecialchars($row['id']); ?>)">+</button>
                </div>
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                <button type="submit" name="place_order">Buy Now</button>
              </form>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <p>No products found for your search or category.</p>
        <?php endif; ?>
      </div>
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
              <img src="product_pics/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 50px; height: auto;">
              <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo htmlspecialchars($item['selected_quantity']); ?>)</span>
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

  <div class="pickup-status-modal" id="pickup-status-modal" style="display: none;">
    <div class="modal-content">
      <h3>Pick Up Status</h3>
      <div id="pickup-status-list">
        <?php if (!empty($_SESSION['orderSummary'])): ?>
          <?php foreach ($_SESSION['orderSummary'] as $order): ?>
            <div class="status-item">
              <p><strong><?php echo htmlspecialchars($order['name']); ?></strong></p>
              <p>Quantity: <?php echo htmlspecialchars($order['selected_quantity']); ?></p>
              <p>Status: <strong><?php echo htmlspecialchars($order['status']); ?></strong></p>
            </div>
            <hr>
          <?php endforeach; ?>
        <?php else: ?>
          <p>No orders available.</p>
        <?php endif; ?>
      </div>
      <button onclick="closePickupStatusModal()">Close</button>
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
      <div id="modal-order-list">
        <?php if (!empty($orderSummary)): ?>
          <?php 
          $subtotal = 0;
          foreach ($orderSummary as $item): 
            $totalPrice = $item['price'] * $item['selected_quantity'];
            $subtotal += $totalPrice;
          ?>
            <div class="order-item">
              <img src="product_pics/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 50px; height: auto;">
              <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo htmlspecialchars($item['selected_quantity']); ?>)</span>
              <span>PHP <?php echo number_format($totalPrice, 2); ?></span>
            </div>
          <?php endforeach; ?>
          <div class="total">
            <p>Subtotal</p>
            <p id="modal-subtotal">PHP <?php echo number_format($subtotal, 2); ?></p>
          </div>
        <?php endif; ?>
      </div>
      <button onclick="placeOrder()">Place Order</button>
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
        window.location.href = "customer.php";  
      }, 500);
    }
  </script>
</body>
</html>
