<?php
include('../config/dbConnection.php');
include('../maininclude/userheader.php');
include('./map_utils.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Initialize current trip
if (!isset($_SESSION['current_trip'])) {
    $_SESSION['current_trip'] = [];
}

$current_trip = $_SESSION['current_trip'];

// Remove destination
if (isset($_GET['remove'])) {
    $index = intval($_GET['remove']);
    if (isset($current_trip[$index])) {
        array_splice($current_trip, $index, 1);
        $_SESSION['current_trip'] = $current_trip;
    }
}

// Reorder destinations
if (isset($_GET['move_up'])) {
    $index = intval($_GET['move_up']);
    if ($index > 0 && isset($current_trip[$index])) {
        $temp = $current_trip[$index-1];
        $current_trip[$index-1] = $current_trip[$index];
        $current_trip[$index] = $temp;
        $_SESSION['current_trip'] = $current_trip;
    }
}

if (isset($_GET['move_down'])) {
    $index = intval($_GET['move_down']);
    if ($index < count($current_trip)-1 && isset($current_trip[$index])) {
        $temp = $current_trip[$index+1];
        $current_trip[$index+1] = $current_trip[$index];
        $current_trip[$index] = $temp;
        $_SESSION['current_trip'] = $current_trip;
    }
}

// Clear entire trip
if (isset($_GET['clear'])) {
    $_SESSION['current_trip'] = [];
    header("Location: current_trip.php");
    exit();
}

// Calculate costs and distances
$total_stay_cost = 0;
$total_food_cost = 0;
$total_stay_time = 0;
$transport_cost = 0;
$route_distance = 0;

// Prepare points for distance calculation
$points = [['lat' => IITG_LAT, 'lng' => IITG_LNG, 'name' => 'IIT Guwahati']]; // Start from IITG
foreach ($current_trip as $dest) {
    $points[] = ['lat' => $dest['latitude'], 'lng' => $dest['longitude'], 'name' => $dest['name']];
    $total_stay_cost += $dest['stay_cost'];
    $total_food_cost += $dest['food_cost'];
    $total_stay_time += $dest['stay_time'];
}

// Calculate distances between each point
$distances = [];
$total_distance = 0;
for ($i = 0; $i < count($points) - 1; $i++) {
    $distance = calculateDistanceOSRM(
        $points[$i]['lat'],
        $points[$i]['lng'],
        $points[$i+1]['lat'],
        $points[$i+1]['lng']
    );
    $distances[] = [
        'from' => $points[$i]['name'],
        'to' => $points[$i+1]['name'],
        'distance' => $distance
    ];
    $total_distance += $distance;
}

// Default transport mode
$transport_mode = 'Bike';
$error = null;

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($current_trip)) {
    // Validate and sanitize inputs
    $trip_name = trim(filter_input(INPUT_POST, 'trip_name', FILTER_SANITIZE_STRING));
    $transport_mode = filter_input(INPUT_POST, 'mode_of_transport', FILTER_SANITIZE_STRING);
    $start_date = filter_input(INPUT_POST, 'start_date', FILTER_SANITIZE_STRING);
    $end_date = filter_input(INPUT_POST, 'end_date', FILTER_SANITIZE_STRING);
    $notes = trim(filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_STRING));
    
    // Validate dates
    if (!strtotime($start_date) || !strtotime($end_date) || strtotime($start_date) > strtotime($end_date)) {
        $error = "Invalid date range. Please check your dates.";
    } elseif (empty($trip_name)) {
        $error = "Trip name is required.";
    } else {
        // Calculate transport cost
        $transport_cost = estimateTransportCost($total_distance, $transport_mode);
        $total_budget = $total_stay_cost + $total_food_cost + $transport_cost;
        
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // Insert into planned_trips
            $stmt_trip = $conn->prepare("
                INSERT INTO planned_trips (user_id, trip_name, mode_of_transport, start_date, end_date, budget, created_at)
                VALUES (?, ?, ?, ?, ?, ?, NOW())
            ");
            $stmt_trip->bind_param("sssssd", 
                $_SESSION['user_id'],
                $trip_name,
                $transport_mode,
                $start_date,
                $end_date,
                $total_budget
            );

            if ($stmt_trip->execute()) {
                $plan_id = $stmt_trip->insert_id;
                $stmt_trip->close();
                
                // Insert into planned_destinations
                foreach ($current_trip as $index => $dest) {
                    $stmt_dest = $conn->prepare("
                        INSERT INTO planned_destinations (plan_id, destination_id, visit_duration, notes, sequence_order)
                        VALUES (?, ?, ?, ?, ?)
                    ");
                    $visit_duration = $dest['stay_time'];
                    $dest_notes = "Part of trip: " . $conn->real_escape_string($trip_name);
                    $sequence_order = $index + 1;
                    $stmt_dest->bind_param("iiisi", $plan_id, $dest['destination_id'], $visit_duration, $dest_notes, $sequence_order);
                    
                    if (!$stmt_dest->execute()) {
                        throw new Exception("Failed to save destination: " . $stmt_dest->error);
                    }
                    $stmt_dest->close();
                }
                
                // Commit transaction
                $conn->commit();
                
                // Clear current trip
                unset($_SESSION['current_trip']);
                
                // Redirect to past trips
                header("Location: past_trips.php?success=1");
                exit();
            } else {
                throw new Exception("Failed to save trip: " . $stmt_trip->error);
            }
        } catch (Exception $e) {
            $conn->rollback();
            $error = $e->getMessage();
        }
    }
}

// Calculate transport cost for display
$transport_cost = estimateTransportCost($total_distance, $transport_mode);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Trip Plan</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <!-- Leaflet Routing Machine CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
    <style>
        #routeMap {
            min-height: 400px;
        }
        
        .start-marker {
            filter: hue-rotate(120deg) !important;
        }
        
        .end-marker {
            filter: hue-rotate(300deg) !important;
        }
        
        .route-steps {
            position: relative;
            padding-left: 30px;
        }
        
        .route-step {
            display: flex;
            margin-bottom: 15px;
            position: relative;
        }
        
        .step-marker {
            width: 30px;
            height: 30px;
            margin-right: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .step-marker i {
            font-size: 1.2rem;
        }
        
        .step-number {
            width: 30px;
            height: 30px;
            background-color: #1a5f7a;
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        
        .step-content {
            flex-grow: 1;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .route-connection {
            position: relative;
            height: 40px;
            margin-left: 15px;
            display: flex;
            align-items: center;
        }
        
        .connection-line {
            position: absolute;
            left: 15px;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #dee2e6;
        }
        
        .connection-distance {
            margin-left: 40px;
            padding: 5px 10px;
            background-color: #e9ecef;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: bold;
        }
        
        .step-details {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #dee2e6;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Current Trip Plan</h2>
    
    <?php if (empty($current_trip)): ?>
        <div class="alert alert-info">
            Your current trip is empty. <a href="plan_trip.php" class="alert-link">Add destinations</a> to start planning.
        </div>
    <?php else: ?>
        <!-- Trip Summary -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Trip Summary</h5>
                <div>
                    <a href="plan_trip.php" class="btn btn-sm btn-primary me-2">
                        <i class="fas fa-plus"></i> Add More Destinations
                    </a>
                    <a href="current_trip.php?clear=1" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to clear this trip?')">
                        <i class="fas fa-trash"></i> Clear Trip
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="card bg-light mb-3">
                            <div class="card-body text-center">
                                <h6>Total Distance</h6>
                                <h4><?php echo round($total_distance, 1); ?> km</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light mb-3">
                            <div class="card-body text-center">
                                <h6>Total Stay Time</h6>
                                <h4><?php echo $total_stay_time; ?> hrs</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light mb-3">
                            <div class="card-body text-center">
                                <h6>Stay Cost</h6>
                                <h4>₹<?php echo $total_stay_cost; ?></h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-light mb-3">
                            <div class="card-body text-center">
                                <h6>Food Cost</h6>
                                <h4>₹<?php echo $total_food_cost; ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Route Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Route Breakdown</h5>
            </div>
            <div class="card-body">
                <div class="route-steps">
                    <div class="route-step">
                        <div class="step-marker">
                            <i class="fas fa-map-marker-alt text-success"></i>
                        </div>
                        <div class="step-content">
                            <h6>Start: IIT Guwahati</h6>
                        </div>
                    </div>
                    
                    <?php foreach ($current_trip as $index => $dest): ?>
                        <div class="route-connection">
                            <div class="connection-line"></div>
                            <div class="connection-distance">
                                <?php echo round($distances[$index]['distance'], 1); ?> km
                            </div>
                        </div>
                        
                        <div class="route-step">
                            <div class="step-marker">
                                <span class="step-number"><?php echo $index + 1; ?></span>
                            </div>
                            <div class="step-content">
                                <div class="d-flex justify-content-between">
                                    <h6><?php echo htmlspecialchars($dest['name']); ?></h6>
                                    <div>
                                        <a href="current_trip.php?move_up=<?php echo $index; ?>" class="btn btn-sm btn-outline-secondary <?php echo $index == 0 ? 'disabled' : ''; ?>">
                                            <i class="fas fa-arrow-up"></i>
                                        </a>
                                        <a href="current_trip.php?move_down=<?php echo $index; ?>" class="btn btn-sm btn-outline-secondary <?php echo $index == count($current_trip)-1 ? 'disabled' : ''; ?>">
                                            <i class="fas fa-arrow-down"></i>
                                        </a>
                                        <a href="current_trip.php?remove=<?php echo $index; ?>" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                </div>
                                <div class="step-details">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <small class="text-muted">
                                                <i class="fas fa-hotel"></i> Stay: ₹<?php echo $dest['stay_cost']; ?>/night
                                            </small>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted">
                                                <i class="fas fa-utensils"></i> Food: ₹<?php echo $dest['food_cost']; ?>/day
                                            </small>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i> Time: <?php echo $dest['stay_time']; ?> hrs
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <!-- Route Map -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Route Map</h5>
            </div>
            <div class="card-body p-0" style="height: 400px;">
                <div id="routeMap" style="height: 100%; width: 100%;"></div>
            </div>
        </div>
        
        <!-- Confirm Trip Form -->
        <div class="card">
            <div class="card-header">
                <h5>Confirm Trip</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Trip Name</label>
                            <input type="text" class="form-control" name="trip_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Transport Mode</label>
                            <select class="form-select" name="mode_of_transport" required onchange="updateTransportCost(this.value)">
                                <option value="Bus" <?php echo $transport_mode == 'Bus' ? 'selected' : ''; ?>>Bus</option>
                                <option value="Train" <?php echo $transport_mode == 'Train' ? 'selected' : ''; ?>>Train</option>
                                <option value="Car" <?php echo $transport_mode == 'Car' ? 'selected' : ''; ?>>Car</option>
                                <option value="Bike" <?php echo $transport_mode == 'Bike' ? 'selected' : ''; ?>>Bike</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" class="form-control" name="start_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" class="form-control" name="end_date" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Additional Notes</label>
                        <textarea class="form-control" name="notes" rows="3"></textarea>
                    </div>
                    <div class="alert alert-info">
                        <h6>Estimated Total Budget: ₹<span id="totalBudget"><?php echo ($total_stay_cost + $total_food_cost + $transport_cost); ?></span></h6>
                        <small>
                            (Stay: ₹<?php echo $total_stay_cost; ?> + 
                            Food: ₹<?php echo $total_food_cost; ?> + 
                            Transport: ₹<span id="transportCost"><?php echo $transport_cost; ?></span>)
                        </small>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i> Confirm Trip
                    </button>
                    <a href="plan_trip.php" class="btn btn-outline-secondary">
                        <i class="fas fa-plus"></i> Add More Destinations
                    </a>
                </form>
            </div>
        </div>
        
        <!-- JavaScript Libraries -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
        <script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>
        
        <script>
        function updateTransportCost(mode) {
            const distance = <?php echo $total_distance; ?>;
            const rates = {
                'Bus': 8,
                'Train': 5,
                'Car': 10,
                'Bike': 4
            };
            
            const rate = rates[mode] || 8;
            const transportCost = Math.round(distance * rate * 2);
            const totalBudget = <?php echo $total_stay_cost + $total_food_cost; ?> + transportCost;
            
            document.getElementById('transportCost').textContent = transportCost;
            document.getElementById('totalBudget').textContent = totalBudget;
        }
        
        // Initialize map with Leaflet
        function initMap() {
            const routePoints = <?php echo json_encode($points); ?>;
            
            if (routePoints.length < 2) return;
            
            // Create map centered on first point
            const map = L.map('routeMap').setView(
                [routePoints[0].lat, routePoints[0].lng], 
                10
            );
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Add markers
            routePoints.forEach((point, i) => {
                const marker = L.marker([point.lat, point.lng], {
                    title: point.name
                }).addTo(map);
                
                if (i === 0) {
                    marker.bindPopup("<b>Start:</b> " + point.name);
                    marker._icon.classList.add('start-marker');
                } else if (i === routePoints.length - 1) {
                    marker.bindPopup("<b>End:</b> " + point.name);
                    marker._icon.classList.add('end-marker');
                } else {
                    marker.bindPopup("<b>Stop " + i + ":</b> " + point.name);
                }
            });
            
            // Add route if we have at least 2 points
            if (routePoints.length >= 2) {
                const waypoints = routePoints.map(point => L.latLng(point.lat, point.lng));
                
                L.Routing.control({
                    waypoints: waypoints,
                    routeWhileDragging: false,
                    show: false,
                    addWaypoints: false,
                    draggableWaypoints: false,
                    fitSelectedRoutes: true,
                    lineOptions: {
                        styles: [{color: '#1a5f7a', opacity: 0.7, weight: 5}]
                    }
                }).addTo(map);
            }
        }
        
        // Initialize the map when the page loads
        document.addEventListener('DOMContentLoaded', initMap);
        </script>
    <?php endif; ?>
</div>

<?php include('../maininclude/userfooter.php'); ?>

<style>
    .footer-fixed {
     position: fixed; 
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 999;
}
</style>