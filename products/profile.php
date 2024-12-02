<?php
session_start();
@include '../connections/config.php';

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: profile.php');
    exit();
}

$email = $_SESSION['email']; 

// Fetch user data from the database
$query = "SELECT * FROM customers WHERE email = '$email'";
$result = mysqli_query($conn, $query);
$lender = mysqli_fetch_assoc($result); 

// Handle profile update
if (isset($_POST['update_profile'])) {
    $name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);

    // Handle profile image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $profile_image = $_FILES['profile_image']['name'];
        $profile_image_tmp_name = $_FILES['profile_image']['tmp_name'];
        $profile_image_folder = 'profile_pics/' . basename($profile_image);

        // Check file type and size (optional)
        $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
        $file_type = $_FILES['profile_image']['type'];
        if (in_array($file_type, $allowed_types)) {
            // Move uploaded file to the specified directory
            if (move_uploaded_file($profile_image_tmp_name, $profile_image_folder)) {
                // Update the profile_image in the database
                $update_image = "UPDATE customers SET profile_image = '$profile_image' WHERE email = '$email'";
                mysqli_query($conn, $update_image);
            }
        }
    }

    // Update other profile details (name, address, contact_number)
    $update = "UPDATE customers SET customer_name = '$name', address = '$address', contact_number = '$contact_number' WHERE email = '$email'";
    $update_query = mysqli_query($conn, $update);

    // Check if the profile update was successful
    if ($update_query) {
        $message = 'Profile updated successfully';
    } else {
        $message = 'Failed to update profile';
    }

    header('Location: profile.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="assets/profile.css?v=1.0">
</head>
<body>

<?php
if (isset($message)) {
    echo '<span class="message">' . htmlspecialchars($message) . '</span>';
}
?>

<div class="container">
    <div class="profile-container">
        <!-- Profile Form -->
        <form action="profile.php" method="post" enctype="multipart/form-data">
            <h3>Your Profile</h3>

            <!-- Display Profile Picture -->
            <div class="profile-picture">
                <?php if (!empty($lender['profile_image'])): ?>
                    <img src="profile_pics/<?php echo htmlspecialchars($lender['profile_image']); ?>" alt="Profile Picture" height="150">
                <?php else: ?>
                    <img src="profile_pics/default.png" alt="Default Profile Picture" height="150">
                <?php endif; ?>
            </div>

            <!-- Upload New Profile Picture -->
            <label for="profile_image">Upload New Profile Picture</label>
            <input type="file" name="profile_image" accept="image/png, image/jpeg, image/jpg" class="box">
            
            <label for="id">ID</label>
            <input type="text" name="id" value="<?php echo htmlspecialchars($lender['id']); ?>" class="box" disabled>

            <label for="name">Name</label>
            <input type="text" name="customer_name" value="<?php echo htmlspecialchars($lender['customer_name']); ?>" class="box" required>

            <label for="email">Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($lender['email']); ?>" class="box" disabled>

            <label for="address">Address</label>
            <input type="text" name="address" value="<?php echo htmlspecialchars($lender['address']); ?>" class="box" required>

            <label for="contact_number">Contact Number</label>
            <input type="text" name="contact_number" value="<?php echo htmlspecialchars($lender['contact_number']); ?>" class="box" required>

            <!-- Submit Button -->
            <input type="submit" class="btn" name="update_profile" value="Update Profile">
        </form>

        <a href="customer.php" class="btn" style="margin-top: 1rem;">Home</a>
    </div>
</div>

</body>
</html>
