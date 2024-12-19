<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "ahl_user"; 

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    
    $image = $_FILES['images']['name'];
    $target_dir = "uploads/"; 
    $target_file = $target_dir . basename($image);
    move_uploaded_file($_FILES['images']['tmp_name'], $target_file);

    if (empty($customer_name) || empty($email) || empty($password) || empty($address) || empty($contact_number)) {
        echo "All fields are required!";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("INSERT INTO customers (customer_name, email, password, address, contact_number, images) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $customer_name, $email, $hashed_password, $address, $contact_number, $target_file);

        if ($stmt->execute()) {
            echo "Registration successful!";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }
}

$conn->close();
?>

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
