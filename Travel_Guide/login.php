<?php
include('./config/dbConnection.php');
include('./maininclude/header.php');

// Login logic
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if it's an admin login attempt
    if (isset($_POST['admin_login'])) {
        $admin_username = $_POST['admin_username'];
        $admin_password = $_POST['admin_password'];
        
        // Hardcoded admin credentials
        if ($admin_username === "Abhiram" && $admin_password === "giveup") {
            session_start();
            $_SESSION['admin'] = true;
            header("Location: admin/dashboard.php");
            exit();
        } else {
            $error = "Invalid admin credentials";
        }
    } else {
        // Regular user login
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $stmt = $conn->prepare("SELECT user_id, username, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                session_start();
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                header("Location: user/dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <!-- User Login Card -->
        <div class="col-md-6 col-lg-5 mb-4">
            <div class="login-card shadow-lg p-4">
                <h2 class="text-center mb-4">User Login</h2>
                
                <?php if (isset($error) && !isset($_POST['admin_login'])): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="login.php">
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required 
                               placeholder="Enter your email">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="Enter your password">
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100 mb-3">Login</button>
                    
                    <div class="text-center">
                        <p class="mb-0">Don't have an account? <a href="signup.php" class="text-decoration-none">Sign up</a></p>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Admin Login Card -->
        <div class="col-md-6 col-lg-5">
            <div class="login-card shadow-lg p-4" style="border-left: 5px solid #dc3545;">
                <h2 class="text-center mb-4">Admin Login</h2>
                
                <?php if (isset($error) && isset($_POST['admin_login'])): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <form method="POST" action="login.php">
                    <input type="hidden" name="admin_login" value="1">
                    <div class="form-group mb-3">
                        <label for="admin_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="admin_username" name="admin_username" required 
                               placeholder="Enter admin username">
                    </div>
                    
                    <div class="form-group mb-3">
                        <label for="admin_password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="admin_password" name="admin_password" required 
                               placeholder="Enter admin password">
                    </div>
                    
                    <button type="submit" class="btn btn-danger w-100 mb-3">Admin Login</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.login-card {
    border-radius: 15px;
    background-color: #f8f9fa;
    border-left: 5px solid #006a4e;
}

.login-card h2 {
    color: #006a4e;
    font-weight: 600;
}

.form-control {
    border-radius: 8px;
    padding: 10px 15px;
    border: 1px solid #ced4da;
}

.form-control:focus {
    border-color: #006a4e;
    box-shadow: 0 0 0 0.25rem rgba(0, 106, 78, 0.25);
}

.btn-primary {
    background-color: #006a4e;
    border: none;
    padding: 10px;
    border-radius: 8px;
    font-weight: 500;
}

.btn-primary:hover {
    background-color: #005a43;
}

.btn-danger {
    background-color: #dc3545;
    border: none;
    padding: 10px;
    border-radius: 8px;
    font-weight: 500;
}

.btn-danger:hover {
    background-color: #bb2d3b;
}

.alert {
    border-radius: 8px;
}
</style>

