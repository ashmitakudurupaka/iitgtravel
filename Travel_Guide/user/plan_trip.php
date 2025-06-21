<?php
include('../config/dbConnection.php');
include('../maininclude/userheader.php');
include('./map_utils.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get all destinations
$stmt = $conn->prepare("
    SELECT d.*, COALESCE(AVG(r.rating), 0) as avg_rating
    FROM destinations d
    LEFT JOIN reviews r ON d.destination_id = r.destination_id
    GROUP BY d.destination_id
");
$stmt->execute();
$result = $stmt->get_result();
$all_destinations = $result->fetch_all(MYSQLI_ASSOC);

// Get current trip destinations
$current_trip = $_SESSION['current_trip'] ?? [];

// Search functionality
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Add destination to trip
if (isset($_GET['add'])) {
    $destination_id = intval($_GET['add']);
    foreach ($all_destinations as $dest) {
        if ($dest['destination_id'] == $destination_id) {
            $_SESSION['current_trip'][] = $dest;
            header("Location: current_trip.php");
            exit();
        }
    }
}

// Show destination details if requested
if (isset($_GET['view'])) {
    $destination_id = intval($_GET['view']);
    $stmt = $conn->prepare("
        SELECT d.*, COALESCE(AVG(r.rating), 0) as avg_rating 
        FROM destinations d
        LEFT JOIN reviews r ON d.destination_id = r.destination_id
        WHERE d.destination_id = ?
        GROUP BY d.destination_id
    ");
    $stmt->bind_param("i", $destination_id);
    $stmt->execute();
    $destination = $stmt->get_result()->fetch_assoc();
    
    // Get reviews for this destination
    $stmt = $conn->prepare("
        SELECT r.*, u.username, u.profile_image
        FROM reviews r
        JOIN users u ON r.user_id = u.user_id
        WHERE r.destination_id = ? AND r.is_approved = TRUE
        ORDER BY r.review_at DESC
    ");
    $stmt->bind_param("i", $destination_id);
    $stmt->execute();
    $reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Apply filters
$filtered_destinations = $all_destinations;
if ($search_query) {
    $filtered_destinations = array_filter($all_destinations, function($dest) use ($search_query) {
        return stripos($dest['name'], $search_query) !== false;
    });
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_GET['view'])) {
    $best_time = isset($_POST['best_time_to_visit']) ? $_POST['best_time_to_visit'] : null;
    $trip_type = isset($_POST['trip_type']) ? $_POST['trip_type'] : null;
    $max_distance = isset($_POST['max_distance']) ? floatval($_POST['max_distance']) : null;
    
    $filtered_destinations = array_filter($all_destinations, function($dest) use ($best_time, $trip_type, $max_distance, $current_trip) {
        // Best time to visit filter (month-based)
        if ($best_time) {
            if ($best_time == 'Year-round') {
                if (stripos($dest['best_time_to_visit'], 'Year-round') === false) {
                    return false;
                }
            } else {
                // Check if the month is mentioned directly
                $month_match = stripos($dest['best_time_to_visit'], $best_time) !== false;
                
                // Check if it's in a range
                $range_match = false;
                if (strpos($dest['best_time_to_visit'], 'to') !== false) {
                    $parts = explode('to', $dest['best_time_to_visit']);
                    $start_month = trim($parts[0]);
                    $end_month = trim($parts[1]);
                    
                    $start_month_num = date('n', strtotime($start_month));
                    $end_month_num = date('n', strtotime($end_month));
                    $current_month_num = date('n', strtotime($best_time));
                    
                    if ($start_month_num <= $end_month_num) {
                        // Normal range (e.g., October to April)
                        $range_match = ($current_month_num >= $start_month_num && $current_month_num <= $end_month_num);
                    } else {
                        // Wrapping range (e.g., November to March)
                        $range_match = ($current_month_num >= $start_month_num || $current_month_num <= $end_month_num);
                    }
                }
                
                if (!$month_match && !$range_match) {
                    return false;
                }
            }
        }
        
        // Distance filter (from IITG for trip type)
        $distance_from_iitg = calculateDistanceOSRM(
            IITG_LAT,
            IITG_LNG,
            $dest['latitude'],
            $dest['longitude']
        );
        
        // Trip type filter based on distance from IITG
        if ($trip_type) {
            if ($trip_type == 'day_trip' && $distance_from_iitg > 100) {
                return false;
            } elseif ($trip_type == 'weekend' && $distance_from_iitg > 300) {
                return false;
            } elseif ($trip_type == 'week' && $distance_from_iitg <= 300) {
                return false;
            }
        }
        
        // Additional distance filter if specified
        if ($max_distance) {
            if (!empty($current_trip)) {
                $last_dest = end($current_trip);
                $distance = calculateDistanceOSRM(
                    $last_dest['latitude'], 
                    $last_dest['longitude'],
                    $dest['latitude'],
                    $dest['longitude']
                );
            } else {
                $distance = $distance_from_iitg;
            }
            
            if ($distance > $max_distance) {
                return false;
            }
        }
        
        return true;
    });
}
?>

<div class="container mt-4">
    <?php if (isset($destination)): ?>
        <!-- Destination Details View -->
        <div class="destination-details">
            <a href="plan_trip.php" class="btn btn-secondary mb-4">← Back to All Destinations</a>
            
            <div class="card mb-4">
                <img src="<?php echo $destination['image_link']; ?>" 
                     class="card-img-top" 
                     alt="<?php echo $destination['name']; ?>"
                     style="max-height: 400px; object-fit: cover;">
                <div class="card-body">
                    <h2 class="card-title"><?php echo $destination['name']; ?></h2>
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge bg-primary me-2">
                            ★ <?php echo number_format($destination['avg_rating'], 1); ?>
                        </span>
                        <span class="text-muted">
                            <?php echo calculateDistanceOSRM(IITG_LAT, IITG_LNG, $destination['latitude'], $destination['longitude']); ?> km from IITG
                        </span>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="info-box">
                                <h5>Stay Cost</h5>
                                <p>₹<?php echo $destination['stay_cost']; ?> per night</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <h5>Food Cost</h5>
                                <p>₹<?php echo $destination['food_cost']; ?> per day</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-box">
                                <h5>Recommended Stay</h5>
                                <p><?php echo $destination['stay_time']; ?> hours</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h4>Best Time to Visit</h4>
                        <p><?php echo $destination['best_time_to_visit']; ?></p>
                    </div>
                    
                    <div class="mb-4">
                        <h4>Description</h4>
                        <p><?php echo $destination['description']; ?></p>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="plan_trip.php?add=<?php echo $destination['destination_id']; ?>" 
                           class="btn btn-primary">
                            <i class="fas fa-plus"></i> Add to Trip
                        </a>
                        <a href="review_trip.php?destination_id=<?php echo $destination['destination_id']; ?>" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-star"></i> Write a Review
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Reviews Section -->
            <div class="reviews-section">
                <h3 class="mb-4">User Reviews</h3>
                
                <?php if (empty($reviews)): ?>
                    <div class="alert alert-info">
                        No reviews yet. Be the first to review!
                    </div>
                <?php else: ?>
                    <?php foreach ($reviews as $review): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <img src="uploads/profile_images/<?php echo $review['profile_image']; ?>" 
                                         class="rounded-circle me-3" 
                                         width="50" 
                                         height="50"
                                         style="object-fit: cover;"
                                         onerror="this.src='uploads/profile_images/default_profile.png'"
                                         alt="<?php echo $review['username']; ?>">
                                    <div>
                                        <h5 class="mb-0"><?php echo $review['username']; ?></h5>
                                        <div class="rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?php echo $i > $review['rating'] ? '-empty' : ''; ?> text-warning"></i>
                                            <?php endfor; ?>
                                            <small class="text-muted ms-2">
                                                <?php echo date('M j, Y', strtotime($review['review_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <p><?php echo $review['comment']; ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Main Trip Planning View -->
        <h2 class="mb-4">Plan Your Trip</h2>
        
        <!-- Search Form -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" class="row">
                    <div class="col-md-8 mb-2">
                        <input type="text" class="form-control" name="search" 
                               placeholder="Search destinations by name" 
                               value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>
                    <div class="col-md-4 mb-2">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Current Trip Summary -->
        <?php if (!empty($current_trip)): ?>
        <div class="alert alert-info mb-4">
            <h5>Current Trip Plan</h5>
            <ol>
                <?php foreach ($current_trip as $dest): ?>
                    <li><?php echo $dest['name']; ?> (<?php echo $dest['stay_time']; ?> hrs)</li>
                <?php endforeach; ?>
            </ol>
            <a href="current_trip.php" class="btn btn-sm btn-primary">View/Edit Current Trip</a>
        </div>
        <?php endif; ?>
        
        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h5>Filter Destinations</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Best Time to Visit</label>
                                        <select class="form-select" name="best_time_to_visit">
                                            <option value="">Any Time</option>
                                            <option value="January">January</option>
                                            <option value="February">February</option>
                                            <option value="March">March</option>
                                            <option value="April">April</option>
                                            <option value="May">May</option>
                                            <option value="June">June</option>
                                            <option value="July">July</option>
                                            <option value="August">August</option>
                                            <option value="September">September</option>
                                            <option value="October">October</option>
                                            <option value="November">November</option>
                                            <option value="December">December</option>
                                            <option value="Year-round">Year-round</option>
                                        </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Trip Type</label>
                            <select class="form-select" name="trip_type">
                                <option value="">Any Type</option>
                                <option value="day_trip">1 Day Trip (≤100km)</option>
                                <option value="weekend">Weekend Getaway (≤300km)</option>
                                <option value="week">One Week Trip (>300km)</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Max Distance (km)</label>
                            <input type="number" class="form-control" name="max_distance" placeholder="e.g. 100">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                    <a href="plan_trip.php" class="btn btn-secondary">Reset</a>
                </form>
            </div>
        </div>
        
        <!-- All Destinations -->
        <div class="row">
            <?php if (empty($filtered_destinations)): ?>
                <div class="col-12">
                    <div class="alert alert-warning">
                        No destinations match your filters. Please try different criteria.
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($filtered_destinations as $destination): ?>
                    <?php 
                    $distance_from_iitg = calculateDistanceOSRM(
                        IITG_LAT,
                        IITG_LNG,
                        $destination['latitude'],
                        $destination['longitude']
                    );
                    
                    if (!empty($current_trip)) {
                        $last_dest = end($current_trip);
                        $distance_from_last = calculateDistanceOSRM(
                            $last_dest['latitude'], 
                            $last_dest['longitude'],
                            $destination['latitude'],
                            $destination['longitude']
                        );
                        $distance_from = "last destination";
                        $display_distance = $distance_from_last;
                    } else {
                        $distance_from = "IITG";
                        $display_distance = $distance_from_iitg;
                    }
                    ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100 destination-card" onclick="window.location='plan_trip.php?view=<?php echo $destination['destination_id']; ?>'">
                            <img src="<?php echo $destination['image_link']; ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo $destination['name']; ?>"
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo $destination['name']; ?></h5>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="badge bg-primary">
                                        ★ <?php echo number_format($destination['avg_rating'], 1); ?>
                                    </span>
                                    <span class="text-muted"><?php echo $display_distance; ?> km from <?php echo $distance_from; ?></span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Stay: ₹<?php echo $destination['stay_cost']; ?>/night</small>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Food: ₹<?php echo $destination['food_cost']; ?>/day</small>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Best Time: <?php echo $destination['best_time_to_visit']; ?></small>
                                </div>
                                <div class="mt-auto">
                                    <a href="plan_trip.php?add=<?php echo $destination['destination_id']; ?>" 
                                       class="btn btn-sm btn-primary w-100" 
                                       onclick="event.stopPropagation()">
                                        <i class="fas fa-plus"></i> Add to Trip
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
.destination-card .btn {
    cursor: pointer;
}
.info-box {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    height: 100%;
}
.info-box h5 {
    font-size: 1rem;
    color: #6c757d;
    margin-bottom: 5px;
}
.info-box p {
    font-size: 1.1rem;
    margin-bottom: 0;
}
.rating {
    color: #ffc107;
}
</style>

<script>
// Make entire card clickable except for buttons
document.querySelectorAll('.destination-card .btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});
</script>

<?php include('../maininclude/userfooter.php'); ?>

