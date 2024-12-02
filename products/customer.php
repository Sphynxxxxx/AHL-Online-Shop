<?php
session_start();
include '../connections/config.php';

if (!isset($_SESSION['email'])) {
    header("Location: customer.php"); 
    exit();
}

$customer_email = $_SESSION['email'];

// Fetch the customer_id based on the email
$sqlCustomer = "SELECT id FROM customers WHERE email = ?";
$stmt = $conn->prepare($sqlCustomer);
$stmt->bind_param("s", $customer_email);
$stmt->execute();
$resultCustomer = $stmt->get_result();

if ($resultCustomer->num_rows === 0) {
    echo "<p>Customer not found. Please log in again. <a href='login.php'>Login</a></p>";
    exit;
}

$rowCustomer = $resultCustomer->fetch_assoc();
$customer_id = $rowCustomer['id'];

// Fetch the total quantity of items in the cart
$sqlCartCount = "
    SELECT SUM(quantity) AS total_quantity
    FROM carts
    WHERE customer_id = ?
";
$stmt = $conn->prepare($sqlCartCount);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$resultCartCount = $stmt->get_result();
$rowCartCount = $resultCartCount->fetch_assoc();
$cartCount = $rowCartCount['total_quantity'] ? $rowCartCount['total_quantity'] : 0;

// Fetch all products
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
  <link rel="stylesheet" href="../connections/Assets/customer.css?v=1.0">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    /* Add your custom styles here */
  </style>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <div class="sidebar">
      <nav>
        <ul>
          <li><a href="profile.php" class="icon-link" aria-label="Profile"><i class="fa-solid fa-user"></i></a></li>
          <li><a href="History.php" class="icon-link" aria-label="History"><i class="fa-solid fa-clipboard-list"></i></a></li>
          <li>
            <a href="cart.php" class="icon-link" aria-label="Cart">
              <i class="fa-solid fa-cart-shopping"></i>
              <span id="cart-count" class="cart-count"><?php echo $cartCount; ?></span>
            </a>
          </li>
          <li><a href="../connections/logout.php" class="icon-link" aria-label="Logout"><i class="fa-solid fa-right-from-bracket"></i></a></li>
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
        </div>
        <input id="search-box" type="text" placeholder="Search Product here...">
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
              $image = htmlspecialchars($row['image']);
              $category = htmlspecialchars($row['category']);
              $productName = htmlspecialchars($row['product_name']);
              $description = htmlspecialchars($row['description']);
              $price = number_format($row['price'], 2);
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
                  <img src="product_pics/<?php echo $image; ?>" alt="<?php echo $productName; ?>" onerror="this.src='product_pics/default_image.jpg';">
                  <p><strong>Category:</strong> <?php echo $category; ?></p>
                  <p><strong>Product Name:</strong> <?php echo $productName; ?></p>
                  <h3 class="item-price" style="color: red;">₱<?php echo $price; ?></h3>
                  <p><strong>Available:</strong> <?php echo $availableQuantity; ?></p>
                  <div class="quantity-control">
                      <button class="minus-btn" <?php echo $availableQuantity <= 0 ? 'disabled' : ''; ?>>-</button>
                      <span class="quantity">0</span>
                      <button class="plus-btn" <?php echo $availableQuantity <= 0 ? 'disabled' : ''; ?>>+</button>
                      <button class="rent-btn" <?php echo $availableQuantity <= 0 ? 'disabled' : ''; ?>>Place Order</button>
                      <button class="add-to-cart-btn" <?php echo $availableQuantity <= 0 ? 'disabled' : ''; ?>>Add to Cart</button>
                  </div>
              </div>
              <?php
          }
      } else {
          echo "<p>No products available</p>";
      }
      ?>
    </div>
  </div>

  <!-- Order Summary -->
  <div class="order-summary">
      <h3>Order Summary</h3>
      <div id="order-list"></div>
      <div class="delivery-method">
          <h4>Delivery Method</h4>
          <label>
          <input type="radio" name="delivery-method" value="pickup" checked> 
          Pick Up
          </label>
      </div>
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
      <button class="place-order">Check Out</button>
  </div>
</div>
    <script src="customer.js"></script>
  <script>
      document.addEventListener('DOMContentLoaded', () => {
            const orderList = document.getElementById('order-list');
            const subtotalElement = document.getElementById('subtotal');
            const totalAmountElement = document.getElementById('total-amount');
            const placeOrderButton = document.querySelector('.place-order');

            let orderItems = [];

            // Update the order summary dynamically
            function updateOrderSummary() {
                orderList.innerHTML = '';
                let subtotal = 0;

                orderItems.forEach((item, index) => {
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
                        <div class="order-item-actions">
                            <button class="remove-item-btn" data-index="${index}" data-id="${id}" data-quantity="${quantity}">Remove</button>
                        </div>
                    `;
                    orderList.appendChild(orderItem);
                });

                // Update totals
                subtotalElement.textContent = `₱${subtotal.toFixed(2)}`;
                totalAmountElement.textContent = `₱${subtotal.toFixed(2)}`;

                // Add remove functionality
                document.querySelectorAll('.remove-item-btn').forEach(button => {
                    button.addEventListener('click', (event) => {
                        const index = parseInt(event.target.dataset.index);
                        const productId = event.target.dataset.id;
                        const removedQuantity = parseInt(event.target.dataset.quantity);

                        // Remove item from the array
                        orderItems.splice(index, 1);

                        // Reset product quantity in the main list
                        const productElement = document.querySelector(`.item[data-id="${productId}"]`);
                        if (productElement) {
                            const availableQuantity = parseInt(productElement.dataset.quantity);
                            const currentQuantityElement = productElement.querySelector('.quantity');
                            currentQuantityElement.textContent = '0';

                            // Enable the plus button if it was disabled
                            const plusButton = productElement.querySelector('.plus-btn');
                            plusButton.removeAttribute('disabled');
                        }

                        // Refresh the summary
                        updateOrderSummary();
                    });
                });
            }

            // Search functionality
            const searchBox = document.getElementById('search-box');
            const menuItems = document.querySelectorAll('.item');

            searchBox.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();

                menuItems.forEach(item => {
                    const productName = item.dataset.name.toLowerCase();
                    const category = item.dataset.category.toLowerCase();

                    if (productName.includes(searchTerm) || category.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });

            // Category filtering
            const categoryButtons = document.querySelectorAll('.menu-categories button');

            categoryButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const selectedCategory = this.dataset.category;

                    // Remove active class from all buttons
                    categoryButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');

                    menuItems.forEach(item => {
                        const itemCategory = item.dataset.category;

                        if (selectedCategory === 'all' || itemCategory === selectedCategory) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                });
            });

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