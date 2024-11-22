// Function to increase the quantity
function increaseQuantity(productId) {
  let quantityInput = document.getElementById('quantity-' + productId);
  let currentValue = parseInt(quantityInput.value);
  let maxQuantity = parseInt(quantityInput.getAttribute('max')); // Get the max quantity (stock) available

  // Only increase if the current value is less than the max quantity
  if (currentValue < maxQuantity) {
    quantityInput.value = currentValue + 1;
  } else {
    alert("You cannot exceed the available stock.");
  }
}

// Function to decrease the quantity
function decreaseQuantity(productId) {
  let quantityInput = document.getElementById('quantity-' + productId);
  let currentValue = parseInt(quantityInput.value);
  if (currentValue > 0) {
    quantityInput.value = currentValue - 1;
  }
}

// Function to update the quantity manually (if the user types directly)
function updateQuantity(productId) {
  let quantityInput = document.getElementById('quantity-' + productId);
  let maxQuantity = parseInt(quantityInput.max); 
  let inputValue = parseInt(quantityInput.value);

  // If the entered quantity is greater than available stock, set it to the max
  if (inputValue > maxQuantity) {
    quantityInput.value = maxQuantity;
  }
  // If the entered quantity is less than 1, set it to 1
  else if (inputValue < 1) {
    quantityInput.value = 1;
  }
}

// Search function for filtering products by name
document.getElementById("search-bar").addEventListener("input", function() {
  let query = this.value.toLowerCase(); 
  filterProductsByName(query);
});

// Function to filter products by name
function filterProductsByName(query) {
  const products = document.querySelectorAll('.menu-item');
  products.forEach(product => {
    const productName = product.querySelector('h3').textContent.toLowerCase(); 
    if (productName.includes(query)) {
      product.style.display = ''; // Show the product if it matches the search query
    } else {
      product.style.display = 'none'; // Hide the product if it doesn't match the query
    }
  });
}

// Open the pickup status modal
function openPickupStatusModal() {
    document.getElementById("modal-overlay").style.display = "block";
    document.getElementById("pickup-status-modal").style.display = "block";
  }
  
  // Close the pickup status modal
  function closePickupStatusModal() {
    document.getElementById("modal-overlay").style.display = "none";
    document.getElementById("pickup-status-modal").style.display = "none";
  }
  
// Place the order and update the status
function placeOrder() {
    setTimeout(() => {
      alert("Order placed successfully!");
  
      // Reload page to reflect new status
      window.location.href = "customer.php";
    }, 500);
  }
  
  // Open the Pick Up Status modal
  function openPickupStatusModal() {
    document.getElementById("modal-overlay").style.display = "block";
    document.getElementById("pickup-status-modal").style.display = "block";
  }
  
  // Close the Pick Up Status modal
  function closePickupStatusModal() {
    document.getElementById("modal-overlay").style.display = "none";
    document.getElementById("pickup-status-modal").style.display = "none";
  }
  
