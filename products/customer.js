
    // Category filter functionality
    const categoryButtons = document.querySelectorAll('.menu-categories button');
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = this.dataset.category;  
            document.querySelectorAll('.item').forEach(item => {
                const itemCategory = item.dataset.categories;  
                if (category === 'all' || itemCategory === category) {
                    item.style.display = 'block'; 
                } else {
                    item.style.display = 'none'; 
                }
            });
        });
    });

    // Search functionality
    const searchBox = document.getElementById('search-box');
    searchBox.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll('.item').forEach(item => {
            const productName = item.dataset.name.toLowerCase();
            if (productName.includes(searchTerm)) {
                item.style.display = 'block';
            } else {
                item.style.display = 'none';
            }
        });
    });

    
    // Attach event listener to the "Add to Cart" buttons
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', function() {
            var productId = this.getAttribute('data-product-id');
            var quantity = document.getElementById('quantity').value;

            // Send the data to the server using AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'add_to_cart.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        alert('Product added to cart');
                    } else {
                        alert('Failed to add product to cart');
                    }
                }
            };
            xhr.send('product_id=' + productId + '&quantity=' + quantity);
        });
    });


    // Add to Cart functionality
    document.querySelectorAll('.add-to-cart-btn').forEach(button => {
        button.addEventListener('click', (event) => {
            const itemElement = event.target.closest('.item');
            const productId = itemElement.dataset.id;
            const quantity = parseInt(itemElement.querySelector('.quantity').textContent);

            if (quantity > 0) {
                fetch('add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&quantity=${quantity}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Success: show a message or update the cart count
                        alert('Product added to cart!');
                    } else {
                        // Failure: show an error message
                        alert('Failed to add product to cart: ' + data.message);
                    }
                })
                .catch(error => {
                    // Handle any network or server errors
                    console.error('Error adding product to cart:', error);
                    alert('An error occurred. Please try again later.');
                });
            } else {
                alert('Please select a valid quantity.');
            }
        });
    });

    function updateCartCount(cartCount) {
        const cartIcon = document.querySelector('.cart-icon');
        if (cartIcon) {
            cartIcon.textContent = `Cart (${cartCount})`;  
        }
    }

    // Display the cart items in a section of the page
    function displayCartItems(cartItems) {
        const cartContainer = document.querySelector('.cart-items-container');
        cartContainer.innerHTML = '';  // Clear current cart items

        if (cartItems.length > 0) {
            let cartHTML = '<table><thead><tr><th>Product</th><th>Quantity</th><th>Price</th><th>Total</th></tr></thead><tbody>';
            
            cartItems.forEach(item => {
                cartHTML += `
                    <tr>
                        <td>${item.product_name}</td>
                        <td>${item.quantity}</td>
                        <td>$${item.price}</td>
                        <td>$${item.total_price}</td>Z
                    </tr>
                `;
            });

            cartHTML += '</tbody></table>';
            cartContainer.innerHTML = cartHTML;
        } else {
            cartContainer.innerHTML = '<p>Your cart is empty.</p>';
        }
    }


    