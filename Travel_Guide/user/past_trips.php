<?php
session_start();
include('../config/dbConnection.php');
include('../maininclude/userheader.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get current date for filtering
$current_date = date('Y-m-d');

// Get user's past trips (completed)
$stmt_past = $conn->prepare("
    SELECT p.*, 
           COUNT(d.id) as destination_count,
           SUM(d.visit_duration) as total_duration
    FROM planned_trips p
    LEFT JOIN planned_destinations d ON p.plan_id = d.plan_id
    WHERE p.user_id = ? AND p.end_date < ?
    GROUP BY p.plan_id
    ORDER BY p.start_date DESC
");
$stmt_past->bind_param("ss", $_SESSION['user_id'], $current_date);
$stmt_past->execute();
$past_trips = $stmt_past->get_result()->fetch_all(MYSQLI_ASSOC);

// Get user's upcoming trips (planned)
$stmt_upcoming = $conn->prepare("
    SELECT p.*, 
           COUNT(d.id) as destination_count,
           SUM(d.visit_duration) as total_duration
    FROM planned_trips p
    LEFT JOIN planned_destinations d ON p.plan_id = d.plan_id
    WHERE p.user_id = ? AND p.start_date > ?
    GROUP BY p.plan_id
    ORDER BY p.start_date ASC
");
$stmt_upcoming->bind_param("ss", $_SESSION['user_id'], $current_date);
$stmt_upcoming->execute();
$upcoming_trips = $stmt_upcoming->get_result()->fetch_all(MYSQLI_ASSOC);

// Get ongoing trips (started but not ended)
$stmt_ongoing = $conn->prepare("
    SELECT p.*, 
           COUNT(d.id) as destination_count,
           SUM(d.visit_duration) as total_duration
    FROM planned_trips p
    LEFT JOIN planned_destinations d ON p.plan_id = d.plan_id
    WHERE p.user_id = ? AND p.start_date <= ? AND p.end_date >= ?
    GROUP BY p.plan_id
    ORDER BY p.start_date ASC
");
$stmt_ongoing->bind_param("sss", $_SESSION['user_id'], $current_date, $current_date);
$stmt_ongoing->execute();
$ongoing_trips = $stmt_ongoing->get_result()->fetch_all(MYSQLI_ASSOC);

// Success message
if (isset($_GET['success'])) {
    $success = htmlspecialchars($_GET['success']);
}
?>

<div class="container mt-4">
    <h2 class="mb-4">Your Planned Trips</h2>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <!-- Ongoing Trips -->
    <?php if (!empty($ongoing_trips)): ?>
        <div class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4>Ongoing Trips</h4>
                <span class="badge bg-warning text-dark">Active Now</span>
            </div>
            
            <div class="row">
                <?php foreach ($ongoing_trips as $trip): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <?php include('trip_card.php'); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Upcoming Trips -->
    <?php if (!empty($upcoming_trips)): ?>
        <div class="mb-5">
            <h4 class="mb-3">Upcoming Trips</h4>
            <div class="row">
                <?php foreach ($upcoming_trips as $trip): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <?php include('trip_card.php'); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Past Trips -->
    <?php if (!empty($past_trips)): ?>
        <div class="mb-5">
            <h4 class="mb-3">Past Trips</h4>
            <div class="row">
                <?php foreach ($past_trips as $trip): ?>
                    <div class="col-md-6 col-lg-4 mb-4">
                        <?php include('trip_card.php'); ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (empty($ongoing_trips) && empty($upcoming_trips) && empty($past_trips)): ?>
        <div class="alert alert-info">
            You don't have any trips yet. <a href="plan_trip.php">Plan a new trip</a> to get started.
        </div>
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