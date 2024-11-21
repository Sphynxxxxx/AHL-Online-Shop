let cart = [];

// Update Cart Display
function updateCart() {
  let orderList = document.getElementById('order-list');
  let subtotal = 0;
  orderList.innerHTML = '';

  cart.forEach(item => {
    let itemTotal = item.price * item.quantity;
    subtotal += itemTotal;
    orderList.innerHTML += `
      <div class="order-item">
        <span>${item.name} (x${item.quantity})</span>
        <span>PHP ${itemTotal.toFixed(2)}</span>
      </div>
    `;
  });

  // Update Subtotal
  document.getElementById('subtotal').textContent = `PHP ${subtotal.toFixed(2)}`;
}

// Add to Cart Function
document.querySelectorAll('.add-to-cart').forEach(button => {
  button.addEventListener('click', function() {
    let productId = this.getAttribute('data-id');
    let productName = this.getAttribute('data-name');
    let productPrice = parseFloat(this.getAttribute('data-price'));
    let selectedQuantity = parseInt(this.closest('.menu-item').querySelector('.quantity').value);
    const maxQuantity = parseInt(this.closest('.menu-item').querySelector('.quantity').getAttribute('data-quantity')); // Max stock quantity

    // Check if quantity is greater than zero and doesn't exceed available stock
    if (selectedQuantity > 0 && selectedQuantity <= maxQuantity) {
      let productInCart = cart.find(item => item.id == productId);

      // If the product is already in the cart, update its quantity
      if (productInCart) {
        productInCart.quantity += selectedQuantity;
      } else {
        cart.push({
          id: productId,
          name: productName,
          price: productPrice,
          quantity: selectedQuantity
        });
      }

      // Update the stock in the database
      updateStockInDatabase(productId, selectedQuantity);

      updateCart();
    } else {
      alert('Selected quantity exceeds available stock.');
    }
  });
});

// Function to update the stock in the database
function updateStockInDatabase(productId, quantity) {
  let xhr = new XMLHttpRequest();
  xhr.open('POST', 'products/update_stock.php', true);
  xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
  xhr.onreadystatechange = function () {
    if (xhr.readyState == 4 && xhr.status == 200) {
      let response = JSON.parse(xhr.responseText);
      if (response.status === 'error') {
        alert('Error updating stock: ' + response.message);
      } else {
        // Update UI to reflect the new stock after update
        const productElement = document.querySelector(`#quantity-${productId}`);
        const newQuantity = parseInt(productElement.getAttribute('data-quantity')) - quantity;
        productElement.setAttribute('data-quantity', newQuantity); // Update the available stock data attribute
        productElement.value = 0; // Reset the quantity input after adding to cart
        
      }
    }
  };
  xhr.send('addToCart=true&productId=' + productId + '&quantity=' + quantity);
}

// Quantity Button Functionality (Increase/Decrease)
document.addEventListener("DOMContentLoaded", function() {
  // Increase quantity
  const increaseButtons = document.querySelectorAll('.increase-btn');
  increaseButtons.forEach(button => {
    button.addEventListener('click', function() {
      const productId = this.getAttribute('data-id');
      const quantityInput = document.getElementById('quantity-' + productId);
      let quantity = parseInt(quantityInput.value);
      const maxQuantity = parseInt(quantityInput.getAttribute('data-quantity')); // Maximum available quantity from the database

      // Increase only if below the available stock
      if (quantity < maxQuantity) {
        quantityInput.value = quantity + 1;
      }
    });
  });

  // Decrease quantity
  const decreaseButtons = document.querySelectorAll('.decrease-btn');
  decreaseButtons.forEach(button => {
    button.addEventListener('click', function() {
      const productId = this.getAttribute('data-id');
      const quantityInput = document.getElementById('quantity-' + productId);
      let quantity = parseInt(quantityInput.value);
      if (quantity > 1) {
        quantityInput.value = quantity - 1;
      }
    });
  });
});

// Update Cart when Quantity Changes
document.querySelectorAll('.quantity').forEach(input => {
  input.addEventListener('change', function() {
    let productId = this.getAttribute('data-id');
    let newQuantity = parseInt(this.value);

    if (newQuantity > 0) {
      let productInCart = cart.find(item => item.id == productId);
      if (productInCart) {
        productInCart.quantity = newQuantity;
        updateCart();
      }
    }
  });
});

// Filter by category
function filterCategory(category) {
  window.location.href = `customer.php?category=${category}`;
}

// Search functionality (Update the products based on search)
document.getElementById("search-bar").addEventListener("input", function() {
  let query = this.value.toLowerCase(); 
  filterProductsByName(query);
});

// Function to filter products by name and update the display
function filterProductsByName(query) {
  const products = document.querySelectorAll('.menu-item');
  products.forEach(product => {
    const productName = product.querySelector('h3').textContent.toLowerCase(); 
    if (productName.includes(query)) {
      product.style.display = ''; // Show product if query matches
    } else {
      product.style.display = 'none'; // Hide product if query doesn't match
    }
  });
}
