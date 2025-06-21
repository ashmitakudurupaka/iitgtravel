<?php
session_start();
include('../config/dbConnection.php');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

// Get all destinations
$destinations = [];
$query = "SELECT d.*, u.username as added_by 
          FROM Destinations d
          JOIN Users u ON d.added_by = u.user_id
          ORDER BY d.destination_id DESC";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $destinations[] = $row;
}

// Handle destination deletion
if (isset($_GET['delete'])) {
    $destination_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM Destinations WHERE destination_id = ?");
    $stmt->bind_param("i", $destination_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Destination deleted successfully!";
        header("Location: destinations.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to delete destination.";
        header("Location: destinations.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Destinations | IITG Travel Guide</title>
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
        
        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
        }
        
        .table th {
            background-color: #006a4e;
            color: white;
        }
        
        .badge {
            font-weight: 500;
        }
        
        .action-btn {
            width: 30px;
            height: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
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
                            <a class="nav-link" href="dashboard.php">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="destinations.php">
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
                    <h1 class="h2">Manage Destinations</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="add_destination.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add Destination
                        </a>
                    </div>
                </div>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Name</th>
                                        <th>Location</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Added By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($destinations as $destination): ?>
                                        <tr>
                                            <td><?php echo $destination['destination_id']; ?></td>
                                            <td><?php echo htmlspecialchars($destination['name']); ?></td>
                                            <td><?php echo htmlspecialchars($destination['location']); ?></td>
                                            <td>
                                                <span class="badge bg-info"><?php echo ucfirst($destination['category']); ?></span>
                                            </td>
                                            <td>
                                                <span class="badge bg-<?php echo $destination['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                                    <?php echo ucfirst($destination['status']); ?>
                                                </span>
                                            </td>
                                            <td><?php echo htmlspecialchars($destination['added_by']); ?></td>
                                            <td>
                                                <a href="edit_destination.php?id=<?php echo $destination['destination_id']; ?>" 
                                                   class="action-btn btn btn-sm btn-outline-primary me-1" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="#" 
                                                   class="action-btn btn btn-sm btn-outline-danger" 
                                                   title="Delete"
                                                   onclick="confirmDelete(<?php echo $destination['destination_id']; ?>)">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function confirmDelete(destinationId) {
        if (confirm('Are you sure you want to delete this destination? This action cannot be undone.')) {
            window.location.href = 'destinations.php?delete=' + destinationId;
        }
    }
    </script>
</body>
</html>