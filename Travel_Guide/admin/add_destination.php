<?php
session_start();
include('../config/dbConnection.php');

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: ../login.php");
    exit();
}

$admin_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $location = $_POST['location'];
    $category = $_POST['category'];
    $best_season = $_POST['best_season'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $status = $_POST['status'];
    
    $stmt = $conn->prepare("INSERT INTO Destinations (name, description, location, category, best_season, latitude, longitude, added_by, status) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssdis", $name, $description, $location, $category, $best_season, $latitude, $longitude, $admin_id, $status);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Destination added successfully!";
        header("Location: destinations.php");
        exit();
    } else {
        $error = "Failed to add destination. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Destination | IITG Travel Guide</title>
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
        
        .form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        
        .form-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .map-container {
            height: 300px;
            background-color: #eee;
            border-radius: 8px;
            margin-bottom: 20px;
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
                    <h1 class="h2">Add New Destination</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="destinations.php" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Destinations
                        </a>
                    </div>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
                
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="add_destination.php">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Destination Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="location" class="form-label">Location</label>
                                        <input type="text" class="form-control" id="location" name="location" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <select class="form-select" id="category" name="category" required>
                                            <option value="">Select category</option>
                                            <option value="nature">Nature</option>
                                            <option value="adventure">Adventure</option>
                                            <option value="cultural">Cultural</option>
                                            <option value="historical">Historical</option>
                                            <option value="religious">Religious</option>
                                            <option value="shopping">Shopping</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="best_season" class="form-label">Best Season to Visit</label>
                                        <input type="text" class="form-control" id="best_season" name="best_season" 
                                               placeholder="e.g., October to March" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="status" class="form-label">Status</label>
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="latitude" class="form-label">Latitude</label>
                                        <input type="number" step="any" class="form-control" id="latitude" name="latitude" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="longitude" class="form-label">Longitude</label>
                                        <input type="number" step="any" class="form-control" id="longitude" name="longitude" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Location Map</label>
                                <div class="map-container" id="mapPreview">
                                    <!-- Map will be rendered here -->
                                </div>
                                <small class="text-muted">Click on the map to set coordinates</small>
                            </div>
                            
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary px-5 py-2">
                                    <i class="fas fa-save"></i> Save Destination
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Simple map implementation for coordinate selection -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const map = document.getElementById('mapPreview');
        const latitudeInput = document.getElementById('latitude');
        const longitudeInput = document.getElementById('longitude');
        
        // This is a simplified implementation. In a real app, you would use a map API like Google Maps or Leaflet
        map.addEventListener('click', function(e) {
            const rect = map.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            // Convert click position to approximate coordinates (just for demo)
            const lat = 26.18 + (0.1 * (y / rect.height));
            const lng = 91.69 + (0.1 * (x / rect.width));
            
            latitudeInput.value = lat.toFixed(6);
            longitudeInput.value = lng.toFixed(6);
            
            // Show a marker (simplified)
            map.innerHTML = '<div style="position:relative;width:100%;height:100%;">' +
                           '<div style="position:absolute;width:20px;height:20px;background:red;border-radius:50%;' +
                           'left:' + (x-10) + 'px;top:' + (y-10) + 'px;"></div></div>';
        });
        
        // If coordinates are manually entered, update the map marker
        latitudeInput.addEventListener('change', updateMapMarker);
        longitudeInput.addEventListener('change', updateMapMarker);
        
        function updateMapMarker() {
            if (latitudeInput.value && longitudeInput.value) {
                // In a real implementation, you would update the map marker position
                map.innerHTML = '<div style="position:relative;width:100%;height:100%;">' +
                               '<div style="position:absolute;width:20px;height:20px;background:red;border-radius:50%;' +
                               'left:50%;top:50%;transform:translate(-50%,-50%);"></div></div>';
            }
        }
    });
    </script>
</body>
</html>