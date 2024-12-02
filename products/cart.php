<?php
session_start();

// Include database connection
include('../connections/config.php'); 

// Check if customer is logged in
if (!isset($_SESSION['email'])) {
    echo "<p>You need to log in to view your cart. <a href='login.php'>Login</a></p>";
    exit;
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

// Fetch the cart items for the logged-in customer
$sql = "
    SELECT c.id, p.product_name, p.price, p.image, c.quantity, (p.price * c.quantity) AS total_price
    FROM carts c
    JOIN products p ON c.product_id = p.product_id
    WHERE c.customer_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id); 
$stmt->execute();
$result = $stmt->get_result();

// Handle removal of product from cart
if (isset($_GET['remove'])) {
    $cart_id_to_remove = $_GET['remove'];
    $sqlRemove = "DELETE FROM carts WHERE id = ?";
    $stmt = $conn->prepare($sqlRemove);
    $stmt->bind_param("i", $cart_id_to_remove);
    $stmt->execute();
    header("Location: cart.php");
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Cart</title>
    <link rel="stylesheet" href="assets/cart.css">
</head>
<body>
    <h1>Your Cart</h1>

    <ul>
        <li>
            <a href="cart.php" class="icon-link" aria-label="Cart">
                <i class="fa-solid fa-cart-shopping"></i>
                <span id="cart-count" class="cart-count"><?php echo $cartCount; ?></span> 
            </a>
        </li>
    </ul>

    <?php if ($result->num_rows > 0): ?>
        <form method="POST" action="saveOrder.php">
            <table>
                <thead>
                    <tr>
                        <th>Product Image</th>
                        <th>Product Name</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Remove</th>
                        <th>Select for Checkout</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalAmount = 0;
                    while ($row = $result->fetch_assoc()): 
                        $totalAmount += $row['total_price'];
                    ?>
                        <tr>
                            <td>
                                <img class="product-image" 
                                    src="product_pics/<?php echo htmlspecialchars($row['image']); ?>" 
                                    alt="Product Name: <?php echo htmlspecialchars($row['product_name']); ?>" 
                                    style="width:50px;height:50px;">
                            </td>
                            <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                            <td>₱<?php echo number_format($row['price'], 2); ?></td>
                            <td><?php echo $row['quantity']; ?></td>
                            <td>₱<?php echo number_format($row['total_price'], 2); ?></td>
                            <td>
                                <a href="cart.php?remove=<?php echo $row['id']; ?>" class="remove-btn">Remove</a>
                            </td>
                            <td>
                                <input type="checkbox" name="checkout_items[]" value="<?php echo $row['id']; ?>"> 
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </form>

        <div class="order-summary" id="order-summary" style="display: none;">
            <h3>Order Summary</h3>
            <p id="summary-items">Total Items: 0</p>
            <p id="summary-price" class="total-price">Total Price: ₱0.00</p>
            <button class="place-order">Place Order</button>
        </div>



    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>

    <p><a href="customer.php">Continue Shopping</a></p>

    <?php
    // Close the statement and connection
    $stmt->close();
    $conn->close();
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('input[name="checkout_items[]"]');
            const summaryDiv = document.getElementById('order-summary');
            const summaryItems = document.getElementById('summary-items');
            const summaryPrice = document.getElementById('summary-price');
            const placeOrderButton = document.querySelector('.place-order');

            function updateOrderSummary() {
                let totalItems = 0;
                let totalPrice = 0;
                let hasSelection = false;

                checkboxes.forEach((checkbox) => {
                    if (checkbox.checked) {
                        hasSelection = true;
                        const row = checkbox.closest('tr');
                        const quantity = parseInt(row.querySelector('td:nth-child(4)').textContent, 10);
                        const price = parseFloat(
                            row.querySelector('td:nth-child(5)').textContent.replace('₱', '').replace(',', '')
                        );

                        totalItems += quantity;
                        totalPrice += price;
                    }
                });

                if (totalItems > 0) {
                    summaryDiv.style.display = 'block';
                    summaryItems.textContent = `Total Items: ${totalItems}`;
                    summaryPrice.textContent = `Total Price: ₱${totalPrice.toFixed(2)}`;
                } else {
                    summaryDiv.style.display = 'none';
                }

                placeOrderButton.disabled = !hasSelection; // Enable/disable button based on selection
            }

            placeOrderButton.addEventListener('click', function () {
                const selectedProducts = [];
                checkboxes.forEach((checkbox) => {
                    if (checkbox.checked) {
                        const row = checkbox.closest('tr');
                        const productId = parseInt(checkbox.value, 10);
                        const quantity = parseInt(row.querySelector('td:nth-child(4)').textContent, 10);
                        const price = parseFloat(
                            row.querySelector('td:nth-child(3)').textContent.replace('₱', '').replace(',', '')
                        );

                        selectedProducts.push({ id: productId, quantity, price });
                    }
                });

                if (selectedProducts.length > 0) {
                    // Send AJAX request to saveOrder.php
                    fetch('saveOrder2.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            orderDetails: selectedProducts,
                            deliveryMethod: 'pickup', // Example, can be changed dynamically
                        }),
                    })
                        .then((response) => response.json())
                        .then((data) => {
                            if (data.success) {
                                alert(data.message);
                                window.location.href = `order_details.php?reference=${data.referenceNumber}`;
                            } else {
                                alert(data.message);
                            }
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                            alert('Failed to place order. Please try again.');
                        });
                } else {
                    alert('No products selected for order.');
                }
            });

            // Add event listeners to checkboxes
            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', updateOrderSummary);
            });

            // Initialize the summary
            updateOrderSummary();
        });
    </script>

</body>
</html>
