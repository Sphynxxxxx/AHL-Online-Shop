
/*
// Initializes an empty cart array to store the items added by the user
let cart = [];

// Function to update the cart display
function updateCart() {
  let orderList = document.getElementById('order-list');
  let subtotal = 0;
  orderList.innerHTML = '';  // Clears the current order list

  // Loop through each item in the cart and display it
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

  // Update the subtotal display
  document.getElementById('subtotal').textContent = `PHP ${subtotal.toFixed(2)}`;
}




// Function to update the stock in the database when a product is added to the cart
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
        const productElement = document.querySelector(`#quantity-${productId}`);
        const newQuantity = parseInt(productElement.getAttribute('data-quantity')) - quantity;
        productElement.setAttribute('data-quantity', newQuantity);
        productElement.value = 0;
      }
    }
  };
  xhr.send('addToCart=true&productId=' + productId + '&quantity=' + quantity);
}

// Event listeners for increasing and decreasing product quantities
document.addEventListener("DOMContentLoaded", function() {
  const increaseButtons = document.querySelectorAll('.increase-btn');
  increaseButtons.forEach(button => {
    button.addEventListener('click', function() {
      const productId = this.getAttribute('data-id');
      const quantityInput = document.getElementById('quantity-' + productId);
      let quantity = parseInt(quantityInput.value);
      const maxQuantity = parseInt(quantityInput.getAttribute('data-quantity'));

      // Increase the quantity if it is less than the max available
      if (quantity < maxQuantity) {
        quantityInput.value = quantity + 1;
      }
    });
  });

  const decreaseButtons = document.querySelectorAll('.decrease-btn');
  decreaseButtons.forEach(button => {
    button.addEventListener('click', function() {
      const productId = this.getAttribute('data-id');
      const quantityInput = document.getElementById('quantity-' + productId);
      let quantity = parseInt(quantityInput.value);

      // Decrease the quantity if it is greater than 1
      if (quantity > 1) {
        quantityInput.value = quantity - 1;
      }
    });
  });
});

// Event listener for when the quantity in the cart changes manually
document.querySelectorAll('.quantity').forEach(input => {
  input.addEventListener('change', function() {
    let productId = this.getAttribute('data-id');
    let newQuantity = parseInt(this.value);

    // If the quantity is greater than 0, update the cart
    if (newQuantity > 0) {
      let productInCart = cart.find(item => item.id == productId);
      if (productInCart) {
        productInCart.quantity = newQuantity;
        updateCart();
      }
    }
  });
});

// Function to filter products by category (redirects to filtered page)
function filterCategory(category) {
  window.location.href = `customer.php?category=${category}`;
}

// Event listener for the search bar to filter products by name
document.getElementById("search-bar").addEventListener("input", function() {
  let query = this.value.toLowerCase(); 
  filterProductsByName(query);
});

// Function to filter products by name based on user input in the search bar
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

// Event listener for all add to cart buttons
document.querySelectorAll('.add-to-cart-btn').forEach(button => {
  button.addEventListener('click', function() {
      let productId = this.getAttribute('data-id');
      let productName = this.getAttribute('data-name');
      let productPrice = parseFloat(this.getAttribute('data-price'));
      let productImage = this.getAttribute('data-image');
      let quantity = 1;  // Default quantity is 1, can be modified if needed
      
      // Send the product details to the server to add to the cart
      let xhr = new XMLHttpRequest();
      xhr.open('POST', 'products/cart_action.php', true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
      xhr.onreadystatechange = function() {
          if (xhr.readyState == 4 && xhr.status == 200) {
              let response = JSON.parse(xhr.responseText);
              if (response.status === 'success') {
                  alert('Product added to cart!');

                  addToCart(productId, productName, productPrice, quantity, productImage);
                  updateCart(); // Optionally, update the cart UI
              } else {
                  alert('Error adding product to cart: ' + response.message);
              }
          }
      };
      xhr.send('action=addToCart&productId=' + productId + '&productName=' + productName + '&productPrice=' + productPrice + '&quantity=' + quantity);
  });
});
*/
