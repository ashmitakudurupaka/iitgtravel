<?php
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

<div class="card h-100 trip-card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <h5 class="card-title mb-0"><?php echo htmlspecialchars($trip['trip_name']); ?></h5>
            <span class="badge bg-primary">
                <?php echo $trip['destination_count']; ?> stops
            </span>
        </div>
        
        <div class="trip-dates mb-2">
            <small class="text-muted">
                <i class="fas fa-calendar-alt me-1"></i>
                <?php echo date('M j, Y', strtotime($trip['start_date'])); ?> - 
                <?php echo date('M j, Y', strtotime($trip['end_date'])); ?>
            </small>
        </div>
        
        <div class="trip-stats mb-3">
            <div class="d-flex justify-content-between">
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i>
                    <?php echo $trip['total_duration'] ?? 0; ?> hrs
                </small>
                <small class="text-muted">
                    <i class="fas fa-rupee-sign me-1"></i>
                    ₹<?php echo $trip['budget']; ?>
                </small>
                <small class="text-muted">
                    <i class="fas fa-bus me-1"></i>
                    <?php echo htmlspecialchars($trip['mode_of_transport']); ?>
                </small>
            </div>
        </div>
        
        <div class="trip-actions d-flex justify-content-between">
            <a href="trip_details.php?id=<?php echo $trip['plan_id']; ?>" 
               class="btn btn-sm btn-outline-primary">
                <i class="fas fa-info-circle"></i> Details
            </a>
            <?php if ($trip_status == 'ongoing'): ?>
                <a href="#" class="btn btn-sm btn-warning">
                    <i class="fas fa-road"></i> On Trip
                </a>
            <?php else: ?>
                <a href="review_trip.php?trip_id=<?php echo $trip['plan_id']; ?>" 
                   class="btn btn-sm btn-outline-success">
                    <i class="fas fa-star"></i> Review
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-footer bg-transparent">
        <small class="text-muted">
            Created on <?php echo date('M j, Y H:i', strtotime($trip['created_at'])); ?>
        </small>
    </div>
</div>

<style>
.trip-card {
    transition: transform 0.2s;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
}

.trip-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
}

.trip-actions .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
}

.card-title {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 200px;
}
</style>