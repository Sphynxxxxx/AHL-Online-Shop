<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login/Register</title>
    <link rel="stylesheet" href="connections/Assets/styles.css?v1.0">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <!-- Login Form -->
            <form id="login-form" class="form">
                <img src="connections\Assets\images\logo.png" alt="" width = "300" height = "300">
                <br>
                <h1>AHL Online Shop</h1>
                <h2>Login</h2>
                <input type="text" id="login-email" placeholder="Email" required>
                <input type="password" id="login-password" placeholder="Password" required>
                <button type="button" id="login-button">Login</button>
                <p id="show-register">——— New User? Register Here ———</p>
            </form>

            <!-- Register Form -->
            <form id="register-form" class="form hidden" enctype="multipart/form-data">
                <h2>Register New Account</h2>
                <img src="connections\Assets\images\logo.png" alt="" width = "200" height = "200">
                <br>
                <input type="text" id="register-name" placeholder="Name" required>
                <input type="number" id="register-contact" placeholder="Contact Number" required>
                <input type="text" id="register-address" placeholder="Address" required>
                <input type="text" id="register-email" placeholder="Email" required>
                <input type="password" id="register-password" placeholder="Password" required>
                <input type="password" id="register-confirm-password" placeholder="Confirm Password" required>
                <input type="file" id="register-image" accept="image/*" required>
                <button type="button" id="register-button">Register</button>
                <p id="registration-message" class="error-message"></p>
                <button type="button" id="back-to-login-button">Back to Login</button>
            </form>

            <!-- Verification Code Form -->
            <form id="verification-form" class="form hidden">
                <h2>Enter Verification Code</h2>
                <input type="text" id="verification-code" placeholder="Enter the code sent to your email" required>
                <button type="button" id="verify-button">Verify Code</button>
                <p id="verification-message" class="error-message"></p>
                <p id="back-register">——— Back to Register ———</p>
            </form>
        </div>
    </div>

    <script>
        // Global variables
        let verificationCode = '';

        // Toggle between forms
        const toggleForms = (formToShow, formToHide) => {
            document.getElementById(formToShow).classList.remove('hidden');
            document.getElementById(formToHide).classList.add('hidden');
        };

        // Show the registration form
        document.getElementById('show-register').onclick = () => toggleForms('register-form', 'login-form');

        // Back to login form
        document.getElementById('back-to-login-button').onclick = () => toggleForms('login-form', 'register-form');

        // Back to register form from verification
        document.getElementById('back-register').onclick = () => toggleForms('register-form', 'verification-form');

        // Generate a 6-digit verification code
        const generateVerificationCode = () => Math.floor(100000 + Math.random() * 900000).toString();

        // Send verification email
        const sendVerificationEmail = async (email, code) => {
            try {
                const response = await fetch('connections/send_email_verification.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email, code }),
                });
                const data = await response.json();
                if (data.success) {
                    console.log("Verification email sent!");
                } else {
                    throw new Error("Failed to send verification email.");
                }
            } catch (error) {
                alert(error.message);
            }
        };

        // Handle registration form submission
        document.getElementById('register-button').onclick = async () => {
            const name = document.getElementById('register-name').value.trim();
            const contact = document.getElementById('register-contact').value.trim();
            const address = document.getElementById('register-address').value.trim();
            const email = document.getElementById('register-email').value.trim();
            const password = document.getElementById('register-password').value;
            const confirmPassword = document.getElementById('register-confirm-password').value;
            const image = document.getElementById('register-image').files[0];
            const errorMessage = document.getElementById('registration-message');

            // Clear previous error messages
            errorMessage.innerText = '';

            // Validate inputs
            if (!name || !contact || !address || !email || !password || !confirmPassword || !image) {
                errorMessage.innerText = "All fields are required!";
                return;
            }
            if (password !== confirmPassword) {
                errorMessage.innerText = "Passwords do not match!";
                return;
            }
            if (!/^\d{11}$/.test(contact)) {
                errorMessage.innerText = "Contact number must be 11 digits!";
                return;
            }
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                errorMessage.innerText = "Invalid email address!";
                return;
            }

            // Generate and send verification code
            verificationCode = generateVerificationCode();
            await sendVerificationEmail(email, verificationCode);

            // Show verification form
            toggleForms('verification-form', 'register-form');
        };

        // Verify the entered code
        document.getElementById('verify-button').onclick = () => {
            const enteredCode = document.getElementById('verification-code').value;
            if (enteredCode === verificationCode) {
                alert("Verification successful!");
                completeRegistration();
            } else {
                document.getElementById('verification-message').innerText = "Invalid verification code!";
            }
        };

        // Complete the registration process
        const completeRegistration = async () => {
            const name = document.getElementById('register-name').value;
            const contact = document.getElementById('register-contact').value;
            const address = document.getElementById('register-address').value;
            const email = document.getElementById('register-email').value;
            const password = document.getElementById('register-password').value;
            const image = document.getElementById('register-image').files[0];

            const formData = new FormData();
            formData.append('name', name);
            formData.append('contact', contact);
            formData.append('address', address);
            formData.append('email', email);
            formData.append('password', password);
            formData.append('images', image);

            try {
                const response = await fetch('connections/register.php', {
                    method: 'POST',
                    body: formData,
                });
                const data = await response.json();
                if (data.success) {
                    alert("Registration successful!");
                    window.location.href = 'index.php';
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                alert(`Registration failed: ${error.message}`);
            }
        };

        // Handle login
        document.getElementById('login-button').onclick = async () => {
            const email = document.getElementById('login-email').value;
            const password = document.getElementById('login-password').value;

            try {
                const response = await fetch('connections/login.php', {
                    method: 'POST',
                    body: new URLSearchParams({ email, password }),
                });
                const data = await response.json();
                if (data.success) {
                    alert(data.message);
                    window.location.href = 'products/customer.php';
                } else {
                    throw new Error(data.message);
                }
            } catch (error) {
                alert(`Login failed: ${error.message}`);
            }
        };
    </script>
</body>
</html>
