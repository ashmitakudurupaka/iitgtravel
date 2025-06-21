<?php
include('../config/dbConnection.php');
include('../maininclude/userheader.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    $errors = [];
    
    // Validate inputs
    if (empty($username)) {
        $errors[] = "Username is required";
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required";
    }
    
    // Check email uniqueness
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ? AND user_id != ?");
    $stmt->bind_param("ss", $email, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $errors[] = "Email is already registered";
    }
    
    // Password change logic
    $password_changed = false;
    if (!empty($new_password)) {
        if (empty($current_password)) {
            $errors[] = "Current password is required";
        } elseif (!password_verify($current_password, $user['password'])) {
            $errors[] = "Current password is incorrect";
        } elseif ($new_password !== $confirm_password) {
            $errors[] = "New passwords don't match";
        } else {
            $password_changed = true;
        }
    }
    
// Handle profile image upload
$profile_image = $user['profile_image'];
if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
    // Use absolute path to avoid any path resolution issues
    $upload_dir = __DIR__ . '/uploads/profile_images/';
    
    // Create directory if it doesn't exist
    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0755, true)) {
            $errors[] = "Could not create upload directory at: " . $upload_dir;
        }
    }
    
    // Verify directory is writable
    if (file_exists($upload_dir) && !is_writable($upload_dir)) {
        // Try to make it writable
        if (!chmod($upload_dir, 0755)) {
            $errors[] = "Upload directory exists but is not writable and couldn't change permissions";
        }
    }
    
    // Only proceed if directory is ready
    if (empty($errors)) {
        $file_ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
        $file_name = $_SESSION['user_id'] . '_' . time() . '.' . $file_ext;
        $file_path = $upload_dir . $file_name;
        
        // Validate image
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($file_ext, $allowed_types)) {
            $errors[] = "Only JPG, JPEG, PNG & GIF files are allowed";
        } elseif ($_FILES['profile_image']['size'] > 5000000) {
            $errors[] = "File size must be less than 5MB";
        } elseif (!move_uploaded_file($_FILES['profile_image']['tmp_name'], $file_path)) {
            $error_message = error_get_last() ? error_get_last()['message'] : 'Unknown error';
            $errors[] = "Failed to upload image: " . $error_message;
        } else {
            // Delete old image if not default
            if ($profile_image != 'default_profile.png' && file_exists($upload_dir . $profile_image)) {
                @unlink($upload_dir . $profile_image);
            }
            $profile_image = $file_name;
        }
    }
}
    // Update if no errors
    if (empty($errors)) {
        if ($password_changed) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("
                UPDATE users 
                SET username = ?, email = ?, password = ?, profile_image = ?
                WHERE user_id = ?
            ");
            $stmt->bind_param("sssss", $username, $email, $hashed_password, $profile_image, $_SESSION['user_id']);
        } else {
            $stmt = $conn->prepare("
                UPDATE users 
                SET username = ?, email = ?, profile_image = ?
                WHERE user_id = ?
            ");
            $stmt->bind_param("ssss", $username, $email, $profile_image, $_SESSION['user_id']);
        }
        
        if ($stmt->execute()) {
            $_SESSION['username'] = $username;
            $success = "Profile updated successfully!";
            
            // Refresh user data
            $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
            $stmt->bind_param("s", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
        } else {
            $errors[] = "Failed to update profile";
        }
    }
}
?>

<div class="container mt-4">
    <h2 class="mb-4">Edit Profile</h2>
    
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <img src="./uploads/profile_images/<?php echo $user['profile_image']; ?>" 
                         class="rounded-circle mb-3" 
                         width="150" 
                         height="150" 
                         onerror="this.src='./uploads/profile_images/default_profile.png'"
                         alt="Profile Image">
                    <h5><?php echo $user['username']; ?></h5>
                    <p class="text-muted"><?php echo $user['email']; ?></p>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Roll Number</label>
                                <input type="text" class="form-control" value="<?php echo $user['user_id']; ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Profile Image</label>
                                <input type="file" class="form-control" name="profile_image" accept="image/*">
                                <small class="form-text text-muted">Max file size: 5MB. Allowed formats: JPG, JPEG, PNG, GIF</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" class="form-control" name="username" 
                                   value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5 class="mb-3">Change Password</h5>
                        
                        <div class="mb-3">
                            <label class="form-label">Current Password</label>
                            <input type="password" class="form-control" name="current_password">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" class="form-control" name="new_password">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Confirm New Password</label>
                            <input type="password" class="form-control" name="confirm_password">
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('../maininclude/userfooter.php'); ?>