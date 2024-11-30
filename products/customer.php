<?php
session_start();
include '../connections/config.php';

if (!isset($_SESSION['email'])) {
    header("Location: customer.php"); 
    exit();
}

// Fetch products
// Fetch products
$sql = "SELECT 
            product_id, 
            category, 
            product_name, 
            description, 
            price, 
            quantity, 
            image, 
            created_at 
        FROM products";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>AHL Online Store</title>
  <link rel="stylesheet" href="../connections/Assets/customer.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">


  <style>
    
  </style>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
      <nav>
        <ul>
          <li> <a href="CusProfile.php" class="icon-link" aria-label="Profile"><i class="fa-solid fa-user"></i></a>
          <li> <a href="History.php" class="icon-link" aria-label="Profile"><i class="fa-solid fa-clipboard-list"></i></a>
          <li> <a href="cart.php" class="icon-link" aria-label="Profile"><i class="fa-solid fa-cart-shopping"></i></i></a>
          <li> <a href="cart.php" class="icon-link" aria-label="Profile"><i class="fa-solid fa-right-from-bracket"></i></i></i></a>
        </ul>
      </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
      <header>
        <div class="logo">
          <img src="../connections/Assets/images/logo.png" alt="Farming Tool and Rental System Logo">
          <div class="logo-text">
            <h1>AHL Online Store</h1>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['customer_name']); ?>!</p>
        </div>
        <input id="search-box" type="text" placeholder="Search Product here...">
        <div class="table-info"></div>
      </header>

      <!-- Categories Buttons -->
      <div class="menu-categories">
          <button data-category="all">All</button>
          <button data-category="Notebooks">Notebooks</button>
          <button data-category="Papers">Papers</button>
          <button data-category="Scissors, Glue">Scissors, Glue</button>
          <button data-category="Markers">Markers</button>
          <button data-category="Pencils, Sharpeners, Erasers">Pencils, Sharpeners, Erasers</button>
          <button data-category="Art Supplies">Art Supplies</button>
          <button data-category="Ruler, Calculator">Ruler, Calculator</button>
          <button data-category="Backpacks">Backpacks</button>
          <button data-category="Water Bottles">Water Bottles</button>
          <button data-category="Lunchbox">Lunchbox</button>
      </div>

      <!-- Menu Items -->
      <div class="menu-items" id="menu-items">
        <?php
        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              $category = htmlspecialchars($row['category']);
              $productName = htmlspecialchars($row['product_name']);
              $description = htmlspecialchars($row['description']);
              $price = number_format($row['price'], 2);
              $image = htmlspecialchars($row['image']);
              $availableQuantity = $row['quantity'];
              $outOfStockClass = $availableQuantity <= 0 ? 'out-of-stock' : '';
              $outOfStockLabel = $availableQuantity <= 0 ? '<div class="out-of-stock-label">Out of Stock</div>' : '';
              ?>
              <div class="item <?php echo $outOfStockClass; ?>" 
                  data-id="<?php echo $row['product_id']; ?>" 
                  data-category="<?php echo $row['category']; ?>" 
                  data-name="<?php echo $productName; ?>" 
                  data-price="<?php echo $price; ?>" 
                  data-quantity="<?php echo $availableQuantity; ?>">
                  <?php echo $outOfStockLabel; ?>
                  <p><strong>Category:</strong> <?php echo $category; ?></p>
                  <p><strong>Product Name:</strong> <?php echo $productName; ?></p>
                  <!--<p><strong>Description:</strong> <?php echo $description; ?></p> -->
                  <img src="product_pics/<?php echo $image; ?>" alt="<?php echo $productName; ?>" onerror="this.src='product_pics/default_image.jpg';">
                  <h3 class="item-price" style="color: red;">₱<?php echo $price; ?></h3>
                  <p><strong>Available:</strong> <?php echo $availableQuantity; ?></p>
                  <div class="quantity-control">
                      <button class="minus-btn" <?php echo $availableQuantity <= 0 ? 'disabled' : ''; ?>>-</button>
                      <span class="quantity">0</span>
                      <button class="plus-btn" <?php echo $availableQuantity <= 0 ? 'disabled' : ''; ?>>+</button>
                      <button class="rent-btn" <?php echo $availableQuantity <= 0 ? 'disabled' : ''; ?>>Rent</button>
                  </div>
              </div>
              <?php
          }
      } else {
          echo "<p>No products available</p>";
      }
        $conn->close();
        ?>
      </div>
    </div>

    <!-- Order Summary -->
    <div class="order-summary">
        <h3>Order Summary</h3>

        <!-- Order List Section -->
        <div id="order-list"></div>

        <!-- Delivery Method Section -->
        <div class="delivery-method">
            <h4>Delivery Method</h4>
            <label>
            <input type="radio" name="delivery-method" value="pickup" checked> 
            Pick Up
            </label>
        </div>

        <!-- Total Calculation Section -->
        <div class="total">
            <p>Subtotal</p>
            <p id="subtotal">₱0.00</p>
        </div>
        <div class="total" id="shipping-fee-container">
            <p>Shipping Fee</p>
            <p id="shippingfee">₱0.00</p> 
        </div>
        <div class="total">
            <p><strong>Total</strong></p>
            <p id="total-amount"><strong>₱0.00</strong></p>
        </div>

        <!-- Place Order Button -->
        <button class="place-order">Place Order</button>
        </div>

</div>
    <script src="customer.js"></script>
  <script>
      document.addEventListener('DOMContentLoaded', () => {
            // Add event listener for order summary and item rent
            const orderList = document.getElementById('order-list');
            const subtotalElement = document.getElementById('subtotal');
            const totalAmountElement = document.getElementById('total-amount');
            const placeOrderButton = document.querySelector('.place-order');
            
            let orderItems = []; 

            // Update the order summary dynamically
            function updateOrderSummary() {
                orderList.innerHTML = '';
                let subtotal = 0;

                orderItems.forEach(item => {
                    const { id, name, price, quantity, image } = item;
                    subtotal += price * quantity;

                    const orderItem = document.createElement('div');
                    orderItem.className = 'order-item';
                    orderItem.innerHTML = `
                        <div class="order-item-image">
                            <img src="product_pics/${image}" alt="${name}" onerror="this.src='uploaded_img/default_image.jpg';">
                        </div>
                        <div class="order-item-details">
                            <p><strong>${name}</strong></p>
                            <p>₱${price.toFixed(2)} x ${quantity} = ₱${(price * quantity).toFixed(2)}</p>
                        </div>
                    `;
                    orderList.appendChild(orderItem);
                });

                // Update totals
                subtotalElement.textContent = `₱${subtotal.toFixed(2)}`;
                totalAmountElement.textContent = `₱${subtotal.toFixed(2)}`;
            }

            // Add item to the order summary when Rent button is clicked
            document.querySelectorAll('.rent-btn').forEach(button => {
                button.addEventListener('click', (event) => {
                    const itemElement = event.target.closest('.item');
                    const itemId = itemElement.dataset.id;
                    const itemName = itemElement.dataset.name;
                    const itemPrice = parseFloat(itemElement.dataset.price);
                    const itemImage = itemElement.querySelector('img').src.split('/').pop(); 

                    const quantityElement = itemElement.querySelector('.quantity');
                    const quantity = parseInt(quantityElement.textContent);

                    if (quantity <= 0) {
                        alert('Please select a quantity greater than 0.');
                        return;
                    }

                    // Check if item already exists in the order list
                    const existingItem = orderItems.find(item => item.id === itemId);

                    if (existingItem) {
                        // Update quantity if already in the list
                        existingItem.quantity += quantity;
                    } else {
                        // Add new item to the list
                        orderItems.push({
                            id: itemId,
                            name: itemName,
                            price: itemPrice,
                            quantity,
                            image: itemImage,
                        });
                    }

                    // Reset quantity in the product listing
                    quantityElement.textContent = '0';

                    // Update the order summary display
                    updateOrderSummary();
                });
            });

            // Handle quantity adjustment buttons
            document.querySelectorAll('.minus-btn').forEach(button => {
                button.addEventListener('click', (event) => {
                    const quantityElement = event.target.closest('.quantity-control').querySelector('.quantity');
                    const currentQuantity = parseInt(quantityElement.textContent);

                    if (currentQuantity > 0) {
                        quantityElement.textContent = currentQuantity - 1;
                    }
                });
            });

            document.querySelectorAll('.plus-btn').forEach(button => {
                button.addEventListener('click', (event) => {
                    const itemElement = event.target.closest('.item');
                    const quantityElement = itemElement.querySelector('.quantity');
                    const currentQuantity = parseInt(quantityElement.textContent);
                    const availableQuantity = parseInt(itemElement.dataset.quantity);

                    if (currentQuantity < availableQuantity) {
                        quantityElement.textContent = currentQuantity + 1;
                    }

                    // Disable the plus button if the quantity exceeds the available stock
                    if (currentQuantity + 1 >= availableQuantity) {
                        event.target.setAttribute('disabled', 'true');
                    }
                });
            });

            // Re-enable the plus button if the quantity is reduced below the available stock
            document.querySelectorAll('.quantity').forEach(quantityElement => {
                quantityElement.addEventListener('DOMSubtreeModified', () => {
                    const itemElement = quantityElement.closest('.item');
                    const currentQuantity = parseInt(quantityElement.textContent);
                    const availableQuantity = parseInt(itemElement.dataset.quantity);
                    const plusButton = itemElement.querySelector('.plus-btn');

                    if (currentQuantity < availableQuantity) {
                        plusButton.removeAttribute('disabled');
                    }
                });
            });

            // Handle order placement
            placeOrderButton.addEventListener('click', () => {
                if (orderItems.length === 0) {
                    alert('Please add items to your order.');
                    return;
                }

                // Send order data to the server
                fetch('saveOrder.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        orderDetails: orderItems
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.href = 'order_details.php'; 
                    } else {
                        alert(data.message);  
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            });
        });



  </script>
</body>
</html>