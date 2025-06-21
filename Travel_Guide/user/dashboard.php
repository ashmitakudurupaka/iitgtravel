<?php
include('../config/dbConnection.php');
include('../maininclude/userheader.php');
include('./map_utils.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get current month recommendations
$current_month = date('F');
$stmt = $conn->prepare("
    SELECT d.*, COALESCE(AVG(r.rating), 0) as avg_rating
    FROM destinations d
    LEFT JOIN reviews r ON d.destination_id = r.destination_id
    WHERE d.best_time_to_visit LIKE ?
    GROUP BY d.destination_id
    ORDER BY avg_rating DESC
    LIMIT 5
");
$month_pattern = "%$current_month%";
$stmt->bind_param("s", $month_pattern);
$stmt->execute();
$result = $stmt->get_result();
$top_destinations = $result->fetch_all(MYSQLI_ASSOC);

// Get user's pending reviews
$stmt = $conn->prepare("
    SELECT r.*, d.name as destination_name, d.image_link
    FROM reviews r
    JOIN destinations d ON r.destination_id = d.destination_id
    WHERE r.user_id = ? AND r.is_approved = FALSE
    ORDER BY r.review_at DESC
    LIMIT 3
");
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$pending_reviews = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Get user's upcoming trips
$stmt = $conn->prepare("
    SELECT p.*, COUNT(d.id) as destination_count
    FROM planned_trips p
    LEFT JOIN planned_destinations d ON p.plan_id = d.plan_id
    WHERE p.user_id = ? AND p.start_date >= CURDATE()
    GROUP BY p.plan_id
    ORDER BY p.start_date ASC
    LIMIT 2
");
$stmt->bind_param("s", $_SESSION['user_id']);
$stmt->execute();
$upcoming_trips = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

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
?>

<div class="container mt-4">
    <?php if (isset($destination)): ?>
        <!-- Destination Details View -->
        <div class="destination-details">
            <a href="dashboard.php" class="btn btn-secondary mb-4">← Back to Dashboard</a>
            
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
        <!-- Main Dashboard View -->
        <!-- Welcome Section -->
        <div class="welcome-section mb-5 p-4 rounded" style="background-color: #f8f9fa; border-left: 5px solid #1a5f7a;">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
                    <p class="lead mb-0">Ready to plan your next adventure? Here are some recommendations for <?php echo htmlspecialchars($current_month); ?>.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <img src="uploads/profile_images/<?php echo htmlspecialchars($_SESSION['profile_image'] ?? 'default_profile.png'); ?>" 
                         class="rounded-circle" 
                         width="100" 
                         height="100"
                         style="object-fit: cover; border: 3px solid #1a5f7a;"
                         onerror="this.src='uploads/profile_images/default_profile.png'"
                         alt="Profile Image">
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-map-marker-alt fa-2x mb-3 text-primary"></i>
                        <h3><?php echo count($upcoming_trips); ?></h3>
                        <p class="mb-0">Upcoming Trips</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-star fa-2x mb-3 text-warning"></i>
                        <h3><?php echo count($pending_reviews); ?></h3>
                        <p class="mb-0">Pending Reviews</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card stat-card h-100">
                    <div class="card-body text-center">
                        <i class="fas fa-history fa-2x mb-3 text-success"></i>
                        <h3>
                            <?php 
                            $stmt = $conn->prepare("SELECT COUNT(*) FROM planned_trips WHERE user_id = ? AND end_date < CURDATE()");
                            $stmt->bind_param("s", $_SESSION['user_id']);
                            $stmt->execute();
                            echo $stmt->get_result()->fetch_row()[0];
                            ?>
                        </h3>
                        <p class="mb-0">Past Trips</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recommended Destinations -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Best Places to Visit in <?php echo $current_month; ?></h4>
                    <a href="plan_trip.php" class="btn btn-outline-primary">View All Destinations</a>
                </div>
                <div class="row">
                    <?php foreach ($top_destinations as $destination): ?>
                        <?php 
                        $distance = calculateDistanceOSRM(
                            IITG_LAT, 
                            IITG_LNG,
                            $destination['latitude'],
                            $destination['longitude']
                        );
                        ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 destination-card" onclick="window.location='dashboard.php?view=<?php echo $destination['destination_id']; ?>'">
                                <img src="<?php echo $destination['image_link']; ?>" 
                                     class="card-img-top" 
                                     alt="<?php echo $destination['name']; ?>"
                                     style="height: 200px; object-fit: cover;">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $destination['name']; ?></h5>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="badge bg-primary">
                                            ★ <?php echo number_format($destination['avg_rating'], 1); ?>
                                        </span>
                                        <span class="text-muted"><?php echo $distance; ?> km from IITG</span>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Stay: ₹<?php echo $destination['stay_cost']; ?>/night</small>
                                    </div>
                                    <div class="mb-2">
                                        <small class="text-muted">Food: ₹<?php echo $destination['food_cost']; ?>/day</small>
                                    </div>
                                    <div class="mb-3">
                                        <small class="text-muted">Stay Time: <?php echo $destination['stay_time']; ?> hrs</small>
                                    </div>
                                    <div class="d-grid">
                                        <a href="plan_trip.php?add=<?php echo $destination['destination_id']; ?>" 
                                           class="btn btn-sm btn-primary"
                                           onclick="event.stopPropagation()">
                                            <i class="fas fa-plus"></i> Add to Trip
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Pending Reviews Section -->
        <?php if (!empty($pending_reviews)): ?>
        <div class="row mb-5">
            <div class="col-12">
                <h4 class="mb-3">Your Pending Reviews</h4>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Destination</th>
                                        <th>Rating</th>
                                        <th>Review Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pending_reviews as $review): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo $review['image_link']; ?>" 
                                                     class="rounded me-3" 
                                                     width="50" 
                                                     height="50"
                                                     style="object-fit: cover;"
                                                     alt="<?php echo $review['destination_name']; ?>">
                                                <?php echo $review['destination_name']; ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star<?php echo $i > $review['rating'] ? '-empty' : ''; ?> text-warning"></i>
                                            <?php endfor; ?>
                                        </td>
                                        <td><?php echo date('M j, Y', strtotime($review['review_at'])); ?></td>
                                        <td><span class="badge bg-warning">Pending Approval</span></td>
                                        <td>
                                            <a href="review_trip.php?destination_id=<?php echo $review['destination_id']; ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                Edit Review
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end mt-3">
                            <a href="past_trips.php" class="btn btn-primary">View All Reviews</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Upcoming Trips Section -->
        <?php if (!empty($upcoming_trips)): ?>
        <div class="row mb-5">
            <div class="col-12">
                <h4 class="mb-3">Your Upcoming Trips</h4>
                <div class="row">
                    <?php foreach ($upcoming_trips as $trip): ?>
                    <div class="col-md-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $trip['trip_name']; ?></h5>
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="text-muted">
                                        <?php echo date('M j, Y', strtotime($trip['start_date'])); ?> - 
                                        <?php echo date('M j, Y', strtotime($trip['end_date'])); ?>
                                    </small>
                                    <span class="badge bg-primary">
                                        <?php echo $trip['destination_count']; ?> destinations
                                    </span>
                                </div>
                                <div class="mb-2">
                                    <small class="text-muted">Transport: <?php echo $trip['mode_of_transport']; ?></small>
                                </div>
                                <div class="mb-3">
                                    <small class="text-muted">Budget: ₹<?php echo $trip['budget']; ?></small>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <a href="current_trip.php?trip_id=<?php echo $trip['plan_id']; ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        View Details
                                    </a>
                                    <a href="plan_trip.php?copy=<?php echo $trip['plan_id']; ?>" 
                                       class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-copy"></i> Copy Plan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Quick Links -->
        <div class="row">
            <div class="col-md-3 mb-4">
                <a href="plan_trip.php" class="card-link">
                    <div class="card h-100 text-center py-4">
                        <i class="fas fa-map-marked-alt fa-3x mb-3 text-primary"></i>
                        <h5>Plan New Trip</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="current_trip.php" class="card-link">
                    <div class="card h-100 text-center py-4">
                        <i class="fas fa-route fa-3x mb-3 text-warning"></i>
                        <h5>Current Trip</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="past_trips.php" class="card-link">
                    <div class="card h-100 text-center py-4">
                        <i class="fas fa-history fa-3x mb-3 text-success"></i>
                        <h5>Planned Trips</h5>
                    </div>
                </a>
            </div>
            <div class="col-md-3 mb-4">
                <a href="edit_profile.php" class="card-link">
                    <div class="card h-100 text-center py-4">
                        <i class="fas fa-user-edit fa-3x mb-3 text-info"></i>
                        <h5>Edit Profile</h5>
                    </div>
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include('../maininclude/userfooter.php'); ?>

<style>
.welcome-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.card-link {
    text-decoration: none;
    color: inherit;
}

.card-link:hover .card {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.destination-card {
    cursor: pointer;
    transition: all 0.3s ease;
}

.destination-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.stat-card {
    border-left: 4px solid;
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.stat-card:nth-child(1) {
    border-left-color: #1a5f7a;
}

.stat-card:nth-child(2) {
    border-left-color: #FFD700;
}

.stat-card:nth-child(3) {
    border-left-color: #28a745;
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
// Make sure buttons inside clickable cards don't trigger the card click
document.querySelectorAll('.destination-card .btn').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
    });
});
</script>
