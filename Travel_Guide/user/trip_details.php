<?php
session_start();
include('../config/dbConnection.php');
include('../maininclude/userheader.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: plan_trip.php");
    exit();
}

$trip_id = $_GET['id'];

// Get trip details
$stmt_trip = $conn->prepare("
    SELECT p.*, 
           COUNT(d.id) as destination_count,
           SUM(d.visit_duration) as total_duration
    FROM planned_trips p
    LEFT JOIN planned_destinations d ON p.plan_id = d.plan_id
    WHERE p.plan_id = ? AND p.user_id = ?
    GROUP BY p.plan_id
");
$stmt_trip->bind_param("is", $trip_id, $_SESSION['user_id']);
$stmt_trip->execute();
$trip = $stmt_trip->get_result()->fetch_assoc();

if (!$trip) {
    header("Location: plan_trip.php");
    exit();
}

// Get trip destinations
$stmt_destinations = $conn->prepare("
    SELECT pd.*, d.name, d.latitude, d.longitude, d.image_link
    FROM planned_destinations pd
    JOIN destinations d ON pd.destination_id = d.destination_id
    WHERE pd.plan_id = ?
    ORDER BY pd.sequence_order ASC
");
$stmt_destinations->bind_param("i", $trip_id);
$stmt_destinations->execute();
$destinations = $stmt_destinations->get_result()->fetch_all(MYSQLI_ASSOC);

// Determine trip status
$current_date = date('Y-m-d');
$trip_status = '';
if ($trip['start_date'] > $current_date) {
    $trip_status = 'upcoming';
} elseif ($trip['end_date'] < $current_date) {
    $trip_status = 'past';
} else {
    $trip_status = 'ongoing';
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><?php echo htmlspecialchars($trip['trip_name']); ?></h2>
        <div>
            <?php if ($trip_status == 'upcoming'): ?>
                <span class="badge bg-info">Upcoming</span>
            <?php elseif ($trip_status == 'ongoing'): ?>
                <span class="badge bg-warning text-dark">Ongoing</span>
            <?php else: ?>
                <span class="badge bg-secondary">Completed</span>
            <?php endif; ?>
            <a href="past_trips.php" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-arrow-left"></i> Back to Trips
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5>Trip Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Start Date:</strong> <?php echo date('M j, Y', strtotime($trip['start_date'])); ?></p>
                            <p><strong>End Date:</strong> <?php echo date('M j, Y', strtotime($trip['end_date'])); ?></p>
                            <p><strong>Duration:</strong> <?php echo $trip['total_duration'] ?? 0; ?> hours</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Transport:</strong> <?php echo htmlspecialchars($trip['mode_of_transport']); ?></p>
                            <p><strong>Budget:</strong> ₹<?php echo $trip['budget']; ?></p>
                            <p><strong>Destinations:</strong> <?php echo $trip['destination_count']; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <h5>Destination Itinerary</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($destinations)): ?>
                        <div class="list-group">
                            <?php foreach ($destinations as $destination): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h5 class="mb-1"><?php echo htmlspecialchars($destination['name']); ?></h5>
                                        <small><?php echo $destination['visit_duration']; ?> hours</small>
                                    </div>
                                    <?php if (!empty($destination['notes'])): ?>
                                        <p class="mb-1"><?php echo htmlspecialchars($destination['notes']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No destinations added to this trip yet.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('tripChart').getContext('2d');
    const tripChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Travel Time', 'Stay Time', 'Budget Used'],
            datasets: [{
                data: [30, 50, 20],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.7)',
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 99, 132, 0.7)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>

<?php include('../maininclude/userfooter.php'); ?>