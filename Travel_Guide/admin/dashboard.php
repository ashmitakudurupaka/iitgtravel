<?php
session_start();
include('../config/dbConnection.php');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get stats for dashboard
$stats = [];
$result = $conn->query("SELECT COUNT(*) as total_users FROM Users");
$stats['total_users'] = $result->fetch_assoc()['total_users'];

$result = $conn->query("SELECT COUNT(*) as total_destinations FROM Destinations");
$stats['total_destinations'] = $result->fetch_assoc()['total_destinations'];

$result = $conn->query("SELECT COUNT(*) as total_reviews FROM Reviews");
$stats['total_reviews'] = $result->fetch_assoc()['total_reviews'];

$result = $conn->query("SELECT COUNT(*) as pending_reviews FROM Reviews WHERE status = 'pending'");
$stats['pending_reviews'] = $result->fetch_assoc()['pending_reviews'];

// Get recent destinations
$recent_destinations = [];
$result = $conn->query("SELECT d.*, u.username as added_by 
                       FROM Destinations d
                       JOIN Users u ON d.added_by = u.user_id
                       ORDER BY d.destination_id DESC LIMIT 5");
while ($row = $result->fetch_assoc()) {
    $recent_destinations[] = $row;
}

// Get recent reviews
$recent_reviews = [];
$result = $conn->query("SELECT r.*, u.username, d.name as destination_name 
                       FROM Reviews r
                       JOIN Users u ON r.user_id = u.user_id
                       JOIN Destinations d ON r.destination_id = d.destination_id
                       ORDER BY r.date_posted DESC LIMIT 5");
while ($row = $result->fetch_assoc()) {
    $recent_reviews[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | IITG Travel Guide</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #006a4e, #1a5f7a);
            color: white;
        }
        
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 5px;
            border-radius: 5px;
            padding: 10px 15px;
        }
        
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .stat-card {
            border-radius: 10px;
            transition: transform 0.3s;
            border: none;
            color: white;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-card .card-body {
            padding: 20px;
        }
        
        .stat-card i {
            font-size: 2.5rem;
            opacity: 0.8;
        }
        
        .recent-item {
            border-left: 3px solid #006a4e;
            transition: all 0.3s;
        }
        
        .recent-item:hover {
            background-color: #f8f9fa;
            border-left: 3px solid #1a5f7a;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 d-md-block sidebar collapse bg-dark">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <h4>IITG Travel Guide</h4>
                        <hr>
                        <p class="small">Admin Dashboard</p>
                    </div>
                    
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="destinations.php">
                                <i class="fas fa-map-marked-alt"></i> Destinations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="accommodations.php">
                                <i class="fas fa-hotel"></i> Accommodations
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="transportation.php">
                                <i class="fas fa-bus"></i> Transportation
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reviews.php">
                                <i class="fas fa-star"></i> Reviews
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="users.php">
                                <i class="fas fa-users"></i> Users
                            </a>
                        </li>
                        <li class="nav-item mt-3">
                            <a class="nav-link" href="../logout.php">
                                <i class="fas fa-sign-out-alt"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Dashboard</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <div class="btn-group me-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary">Share</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle">
                            <i class="fas fa-calendar"></i> This week
                        </button>
                    </div>
                </div>
                
                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="stat-card bg-primary">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Total Users</h6>
                                        <h2 class="mb-0"><?php echo $stats['total_users']; ?></h2>
                                    </div>
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="stat-card bg-success">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Destinations</h6>
                                        <h2 class="mb-0"><?php echo $stats['total_destinations']; ?></h2>
                                    </div>
                                    <i class="fas fa-map-marked-alt"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="stat-card bg-info">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Total Reviews</h6>
                                        <h2 class="mb-0"><?php echo $stats['total_reviews']; ?></h2>
                                    </div>
                                    <i class="fas fa-star"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="stat-card bg-warning">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-0">Pending Reviews</h6>
                                        <h2 class="mb-0"><?php echo $stats['pending_reviews']; ?></h2>
                                    </div>
                                    <i class="fas fa-clock"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Content -->
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Destinations</h5>
                                <a href="destinations.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php foreach ($recent_destinations as $destination): ?>
                                    <div class="recent-item mb-3 p-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($destination['name']); ?></h6>
                                                <p class="text-muted mb-1 small">
                                                    <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($destination['location']); ?>
                                                </p>
                                                <span class="badge bg-info"><?php echo ucfirst($destination['category']); ?></span>
                                            </div>
                                            <small class="text-muted">
                                                Added by <?php echo htmlspecialchars($destination['added_by']); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-6 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Recent Reviews</h5>
                                <a href="reviews.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <div class="card-body">
                                <?php foreach ($recent_reviews as $review): ?>
                                    <div class="recent-item mb-3 p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($review['username']); ?></h6>
                                            <div class="rating">
                                                <?php
                                                for ($i = 1; $i <= 5; $i++) {
                                                    if ($i <= $review['rating']) {
                                                        echo '<i class="fas fa-star text-warning"></i>';
                                                    } else {
                                                        echo '<i class="far fa-star text-warning"></i>';
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <p class="mb-2 small"><?php echo htmlspecialchars(substr($review['comment'], 0, 100)); ?>...</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($review['destination_name']); ?>
                                            </small>
                                            <small class="text-muted">
                                                <?php echo date('M d, Y', strtotime($review['date_posted'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="mb-0">Quick Actions</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <a href="add_destination.php" class="btn btn-outline-primary w-100 py-3">
                                            <i class="fas fa-plus-circle fa-2x mb-2"></i><br>
                                            Add New Destination
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <a href="reviews.php?filter=pending" class="btn btn-outline-warning w-100 py-3">
                                            <i class="fas fa-star-half-alt fa-2x mb-2"></i><br>
                                            Review Pending Feedback
                                        </a>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <a href="users.php" class="btn btn-outline-info w-100 py-3">
                                            <i class="fas fa-user-plus fa-2x mb-2"></i><br>
                                            Manage Users
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>