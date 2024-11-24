<?php
// Database connection details
$servername = "localhost"; // or your database server address
$username = "root"; // your MySQL username
$password = ""; // your MySQL password
$dbname = "ahl_user"; // the name of your database

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $customer_name = $_POST['customer_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    
    // Image upload (optional)
    $image = $_FILES['images']['name'];
    $target_dir = "uploads/"; // Make sure this folder exists or create it
    $target_file = $target_dir . basename($image);
    move_uploaded_file($_FILES['images']['tmp_name'], $target_file);

    // Basic validation
    if (empty($customer_name) || empty($email) || empty($password) || empty($address) || empty($contact_number)) {
        echo "All fields are required!";
    } else {
        // Hash the password before saving
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare and bind the SQL query to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO customers (customer_name, email, password, address, contact_number, images) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $customer_name, $email, $hashed_password, $address, $contact_number, $target_file);

        // Execute the query
        if ($stmt->execute()) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement and connection
        $stmt->close();
    }
}

// Close the connection
$conn->close();
?>

<!-- HTML Form for Registration -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>

    <h2>Register New Account</h2>
    
    <form method="POST" enctype="multipart/form-data">
        <label for="customer_name">Name:</label>
        <input type="text" id="customer_name" name="customer_name" required><br><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required><br><br>

        <label for="address">Address:</label>
        <input type="text" id="address" name="address" required><br><br>

        <label for="contact_number">Contact Number:</label>
        <input type="text" id="contact_number" name="contact_number" required><br><br>

        <label for="images">Profile Image:</label>
        <input type="file" id="images" name="images" accept="image/*"><br><br>

        <button type="submit">Register</button>
    </form>

</body>
</html>
