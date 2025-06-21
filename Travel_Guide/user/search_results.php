<?php
include('../config/dbConnection.php');
include('../maininclude/userheader.php');
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$search_query = isset($_GET['search_query']) ? trim($_GET['search_query']) : '';

// Search destinations by name
$stmt = $conn->prepare("
    SELECT d.*, COALESCE(AVG(r.rating), 0) as avg_rating
    FROM destinations d
    LEFT JOIN reviews r ON d.destination_id = r.destination_id
    WHERE d.name LIKE ?
    GROUP BY d.destination_id
    ORDER BY avg_rating DESC
");
$search_param = "%$search_query%";
$stmt->bind_param("s", $search_param);
$stmt->execute();
$search_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<div class="container mt-4">
    <h2 class="mb-4">Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h2>
    
    <?php if (empty($search_results)): ?>
        <div class="alert alert-info">
            No destinations found matching your search. Try a different name.
        </div>
    <?php else: ?>
        <div class="row">
            <?php foreach ($search_results as $destination): ?>
                <?php 
                $distance = calculateDistanceOSRM(
                    IITG_LAT, 
                    IITG_LNG,
                    $destination['latitude'],
                    $destination['longitude']
                );
                ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100 destination-card">
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
                                   class="btn btn-sm btn-primary">
                                    <i class="fas fa-plus"></i> Add to Trip
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include('../maininclude/userfooter.php'); ?>