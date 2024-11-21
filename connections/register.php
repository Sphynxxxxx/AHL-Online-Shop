<?php
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "ahl_user";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $name = $conn->real_escape_string($_POST['name']);
    $contact_number = $conn->real_escape_string($_POST['contact']);
    $address = $conn->real_escape_string($_POST['address']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400); 
        echo json_encode(['success' => false, 'message' => "Invalid email format."]);
        exit();
    }

    // Check if email already exists
    $email_check_query = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $email_check_query->bind_param("s", $email);
    $email_check_query->execute();
    $email_check_query->store_result();

    if ($email_check_query->num_rows > 0) {
        echo json_encode(['success' => false, 'message' => "This account is already registered."]);
        exit();
    }
    $email_check_query->close();

    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $images = null;
    $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 10 * 1024 * 1024; // 10 MB

    if (isset($_FILES['images']) && $_FILES['images']['error'] == UPLOAD_ERR_OK) {
        $imagesTmpPath = $_FILES['images']['tmp_name'];
        $imagesName = basename($_FILES['images']['name']);
        $imagesMimeType = mime_content_type($imagesTmpPath); 
        $imagesSize = $_FILES['images']['size'];

        if (!in_array($imagesMimeType, $allowedFileTypes)) {
            http_response_code(400); 
            echo json_encode(['success' => false, 'message' => "Invalid image type. Only JPG, PNG, and GIF allowed."]);
            exit();
        }

        if ($imagesSize > $maxFileSize) {
            http_response_code(400); 
            echo json_encode(['success' => false, 'message' => "Image size exceeds the 10MB limit."]);
            exit();
        }

        $imagesName = preg_replace("/[^a-zA-Z0-9\._-]/", "", $imagesName); 
        $imagesPath = 'uploads/' . uniqid() . '_' . $imagesName;

        if (!move_uploaded_file($imagesTmpPath, $imagesPath)) {
            http_response_code(500); 
            echo json_encode(['success' => false, 'message' => "Image upload failed."]);
            exit();
        }

        $images = $imagesPath;
    } else {
        http_response_code(400); 
        echo json_encode(['success' => false, 'message' => "No image uploaded or upload error."]);
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO users (name, contact_number, address, email, password, images, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("ssssss", $name, $contact_number, $address, $email, $password, $images);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => "Registration successful! Awaiting approval."]);
    } else {
        http_response_code(500); 
        echo json_encode(['success' => false, 'message' => "Error: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>
