<?php
include('./config/dbConnection.php');
include('./maininclude/header.php');

// Signup logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = trim($_POST['roll_number']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password_raw = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if any field is empty
    if (empty($user_id) || empty($username) || empty($email) || empty($password_raw) || empty($confirm_password)) {
        $error = "All fields are required.";
    } elseif (!preg_match('/^\d{9}$/', $user_id)) {
        $error = "Roll number must be exactly 9 digits.";
    } elseif ($password_raw !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $password = password_hash($password_raw, PASSWORD_DEFAULT);

        // Check if user ID (roll number) or email already exists
        $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ? OR email = ?");
        $stmt->bind_param("ss", $user_id, $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Roll number or email already registered.";
        } else {
            // Insert new user with default profile image
            $stmt = $conn->prepare("INSERT INTO users (user_id, username, email, password, profile_image) 
                                    VALUES (?, ?, ?, ?, 'default_profile.png')");
            $stmt->bind_param("ssss", $user_id, $username, $email, $password);

            if ($stmt->execute()) {
                $success = "Registration successful! Redirecting to login...";
                header("refresh:2;url=login.php");
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="signup-card shadow-lg p-4">
                <h2 class="text-center mb-4">Create Your Account</h2>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <form method="POST" action="signup.php" novalidate>
                    <div class="mb-3">
                        <label for="username" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="username" name="username" required placeholder="Enter your full name">
                    </div>

                    <div class="mb-3">
                        <label for="roll_number" class="form-label">IITG Roll Number</label>
                        <input type="text" class="form-control" id="roll_number" name="roll_number" required pattern="\d{9}" 
                               placeholder="e.g. 2101070XX">
                        <small class="text-muted">Must be exactly 9 digits.</small>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">IITG Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="Enter your IITG email">
                        <small class="text-muted">We'll never share your email with anyone else.</small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Create a password">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required placeholder="Confirm your password">
                        </div>
                    </div>

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" id="terms" required>
                        <label class="form-check-label" for="terms">
                            I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and 
                            <a href="#" class="text-decoration-none">Privacy Policy</a>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mb-3">Create Account</button>

                    <div class="text-center">
                        <p class="mb-0">Already have an account? <a href="login.php" class="text-decoration-none">Log in</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.signup-card {
    border-radius: 15px;
    background-color: #f8f9fa;
    border-left: 5px solid #1a5f7a;
}

.signup-card h2 {
    color: #1a5f7a;
    font-weight: 600;
}

.alert {
    border-radius: 8px;
}

.btn-primary {
    background-color: #1a5f7a;
    border: none;
    padding: 10px;
    border-radius: 8px;
    font-weight: 500;
}

.btn-primary:hover {
    background-color: #144c63;
}

.form-control {
    border-radius: 8px;
    padding: 10px 15px;
    border: 1px solid #ced4da;
}

.form-control:focus {
    border-color: #1a5f7a;
    box-shadow: 0 0 0 0.25rem rgba(26, 95, 122, 0.25);
}
</style>

<script>
// Password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function () {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;

    if (password !== confirmPassword) {
        this.setCustomValidity("Passwords don't match");
    } else {
        this.setCustomValidity('');
    }
});
</script>

