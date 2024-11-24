<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ahl_user";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => "Database connection failed: " . $conn->connect_error]));
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input data
    $name = trim($conn->real_escape_string($_POST['name']));
    $contact_number = trim($conn->real_escape_string($_POST['contact']));
    $address = trim($conn->real_escape_string($_POST['address']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Invalid email format."]);
        exit();
    }

    // Check if email already exists
    $email_check_query = $conn->prepare("SELECT email FROM customers WHERE email = ?");
    if (!$email_check_query) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => "Error preparing email check query: " . $conn->error]);
        exit();
    }
    $email_check_query->bind_param("s", $email);
    $email_check_query->execute();
    $email_check_query->store_result();

    if ($email_check_query->num_rows > 0) {
        http_response_code(409); // Conflict
        echo json_encode(['success' => false, 'message' => "This email is already registered."]);
        $email_check_query->close();
        exit();
    }
    $email_check_query->close();

    // Password hashing
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Handle image upload
    $imagePath = null;
    $allowedFileTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxFileSize = 10 * 1024 * 1024; // 10 MB

    if (isset($_FILES['images']) && $_FILES['images']['error'] == UPLOAD_ERR_OK) {
        $imagesTmpPath = $_FILES['images']['tmp_name'];
        $imagesName = basename($_FILES['images']['name']);
        $imagesMimeType = mime_content_type($imagesTmpPath);
        $imagesSize = $_FILES['images']['size'];

        // Validate image type and size
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

        // Sanitize file name and set the upload path
        $imagesName = preg_replace("/[^a-zA-Z0-9\._-]/", "", $imagesName);
        $imagePath = 'uploads_img/' . uniqid() . '_' . $imagesName;

        // Ensure the uploads directory exists
        if (!file_exists('uploads_img')) {
            mkdir('uploads_img', 0777, true); // Create the directory if it doesn't exist
        }

        // Move the uploaded file to the desired location
        if (!move_uploaded_file($imagesTmpPath, $imagePath)) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => "Failed to upload image."]);
            exit();
        }
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "No image uploaded or upload error."]);
        exit();
    }

    // Prepare the SQL statement to insert the user into the database
    $stmt = $conn->prepare("INSERT INTO customers (customer_name, contact_number, address, email, password, images, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
    if (!$stmt) {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => "Error preparing insert query: " . $conn->error]);
        exit();
    }
    $stmt->bind_param("ssssss", $name, $contact_number, $address, $email, $password, $imagePath);

    // Execute the query and return the response
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => "Registration successful! Awaiting approval."]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => "Error: " . $stmt->error]);
    }

    // Close the statement
    $stmt->close();
}

// Close the database connection
$conn->close();
?>
