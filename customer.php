<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: customer.php");
    exit();
}

@include 'connections/config.php';

$userEmail = htmlspecialchars($_SESSION['email']);

$sql = "SELECT name FROM users WHERE email = '$userEmail'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);
$userName = $user ? $user['name'] : 'User'; 

// Fetch products from the database
$category = isset($_GET['category']) ? mysqli_real_escape_string($conn, $_GET['category']) : 'all'; // Sanitize category
$searchQuery = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : ''; // Sanitize search query

// Build the SQL query based on category and search query
$sql = "SELECT * FROM products WHERE 1";

if ($category !== 'all') {
    $sql .= " AND category = '$category'";
}

if ($searchQuery !== '') {
    $sql .= " AND name LIKE '%$searchQuery%'";
}

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AHL Online Store</title>
  <link rel="stylesheet" href="connections/Assets/customer.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <nav>
        <ul>
          <li><a href="profile.php"><i class="fa-solid fa-user"></i></a></li>
          <li><a href="delivery.php"><i class="fa-solid fa-cart-shopping"></i></a></li>
          <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i></a></li>
        </ul>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <header>
        <div class="user-welcome">
          <h1>AHL Online Store</h1>
          <p>Welcome, <?php echo $userName; ?>!</p> 
        </div>
        <!-- Search bar -->
        <input type="text" placeholder="Search Product here..." id="search-bar">
        <div class="table-info"></div>
      </header>

      <!-- Categories Buttons -->
      <div class="menu-categories">
        <button onclick="filterCategory('all')">All</button>
        <button onclick="filterCategory('notebooks')">Notebooks</button>
        <button onclick="filterCategory('paper')">Papers</button>
        <button onclick="filterCategory('scissors-glue')">Scissors, Glue</button>
        <button onclick="filterCategory('markers')">Markers</button>
        <button onclick="filterCategory('pencil')">Pencils, Sharpeners, Erasers</button>
        <button onclick="filterCategory('art-supplies')">Art Supplies</button>
        <button onclick="filterCategory('ruler-calculator')">Ruler, Calculator</button>
        <button onclick="filterCategory('backpack')">Backpacks</button>
        <button onclick="filterCategory('waterbottle')">Water Bottles</button>
        <button onclick="filterCategory('lunchbox')">Lunchbox</button>
      </div>

      <!-- Menu Items (Products) -->
      <div class="menu-items" id="menu-items">
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
              echo '<div class="menu-item">
              <div class="product-category"><strong>Category: ' . htmlspecialchars($row['category']) . '</strong></div>
              <img src="products/product_pics/' . htmlspecialchars($row['image']) . '" alt="' . htmlspecialchars($row['name']) . '">
              <h3>' . htmlspecialchars($row['name']) . '</h3>
              <p>PHP ' . htmlspecialchars($row['price']) . '</p>
              <p>Available: ' . htmlspecialchars($row['quantity']) . ' in stock</p>
              <div class="quantity-control">
                  <button class="decrease-btn" data-id="' . $row['id'] . '">-</button>
                  <input type="number" class="quantity" value="0" id="quantity-' . $row['id'] . '" data-price="' . $row['price'] . '" data-quantity="' . $row['quantity'] . '" readonly>
                  <button class="increase-btn" data-id="' . $row['id'] . '">+</button>
              </div>
              <button class="add-to-cart" data-id="' . $row['id'] . '" data-name="' . $row['name'] . '" data-price="' . $row['price'] . '" data-quantity="1">Add to Cart</button>
          </div>';

            
            }
        } else {
            echo '<p>No products found for your search or category.</p>';
        }
        ?>
      </div>

    </div>

    <div class="order-summary">
      <h3>Order Summary</h3>
      <div id="order-list"></div>
      <div class="total">
        <p>Subtotal</p>
        <p id="subtotal">PHP0.00</p>
      </div>
      <div class="payment">
        <button id="cash-btn">Cash</button>
        <button id="online-payment-btn">Online Payment</button>
        <button id="qr-code-btn">QR Code</button>
      </div>
      <button class="place-order">Place Order</button>
    </div>
  </div>

  <script src="connections\scripts.js"></script>
  <script>
    
  </script>
</body>
</html>
