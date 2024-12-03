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
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/cart.css?v=1.0">
</head>
<body>
    <h1>Your Cart</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Select</th>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="checkout_items[]" value="<?php echo $row['id']; ?>" class="select-checkbox">
                        </td>
                        <td>
                            <div class="product-details">
                                <img class="product-image" src="product_pics/<?php echo htmlspecialchars($row['image']); ?>" alt="Product">
                                <div class="product-info">
                                    <p class="product-name"><?php echo htmlspecialchars($row['product_name']); ?></p>
                                    <p class="product-price">Price: ₱<?php echo number_format($row['price'], 2); ?></p>
                                    <a href="cart.php?remove=<?php echo $row['id']; ?>" class="remove-btn"><i class="fa-solid fa-trash"></i></a>
                                </div>
                            </div>
                        </td>
                        <td>
                            <input type="number" min="1" value="<?php echo $row['quantity']; ?>" class="quantity-input" readonly>
                        </td>
                        <td>₱<?php echo number_format($row['price'] * $row['quantity'], 2); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="summary">
            <p id="summary-items">Total Items: 0</p>
            <p id="summary-subtotal">Subtotal: ₱0.00</p>
            <p id="summary-total">Total Price: ₱0.00</p>
        </div>

    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>

    <p><a href="customer.php" class="continue-shopping">Continue Shopping</a></p>

    <?php
    // Close the statement and connection
    $stmt->close();
    $conn->close();
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkboxes = document.querySelectorAll('input[name="checkout_items[]"]');
            const summaryItems = document.getElementById('summary-items');
            const summarySubtotal = document.getElementById('summary-subtotal');
            const summaryTotal = document.getElementById('summary-total');

            // Function to update subtotal and total
            function updateOrderSummary() {
                let totalItems = 0;
                let subtotal = 0;

                checkboxes.forEach((checkbox) => {
                    if (checkbox.checked) {
                        const row = checkbox.closest('tr');
                        const quantity = parseInt(row.querySelector('.quantity-input').value, 10);
                        const price = parseFloat(
                            row.querySelector('td:nth-child(4)').textContent.replace('₱', '').replace(',', '')
                        );

                        totalItems += quantity;
                        subtotal += price;
                    }
                });

                // Update summary display
                summaryItems.textContent = `Total Items: ${totalItems}`;
                summarySubtotal.textContent = `Subtotal: ₱${subtotal.toFixed(2)}`;
                summaryTotal.textContent = `Total Price: ₱${subtotal.toFixed(2)}`;
            }

            // Attach event listeners to checkboxes
            checkboxes.forEach((checkbox) => {
                checkbox.addEventListener('change', updateOrderSummary);
            });

            // Initialize the summary
            updateOrderSummary();
        });
    </script>
</body>
</html>
