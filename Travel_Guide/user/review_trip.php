<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../config/dbConnection.php');
include('../maininclude/userheader.php');
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Check database connection
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get parameters safely
$trip_id = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : 0;
$destination_id = isset($_GET['destination_id']) ? intval($_GET['destination_id']) : 0;

// Redirect if no valid ID provided
if ($trip_id === 0 && $destination_id === 0) {
    header("Location: past_trips.php");
    exit();
}

// Process review submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $destination_id = intval($_POST['destination_id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    
    // Validate inputs
    if ($rating < 1 || $rating > 5) {
        $error = "Please select a valid rating between 1 and 5 stars.";
    } elseif (empty($comment)) {
        $error = "Please provide a review comment.";
    } else {
        try {
            // Check if review already exists
            $check_stmt = $conn->prepare("SELECT review_id FROM reviews WHERE user_id = ? AND destination_id = ?");
            $check_stmt->bind_param("si", $_SESSION['user_id'], $destination_id);
            
            if (!$check_stmt->execute()) {
                throw new Exception("Database error: " . $check_stmt->error);
            }
            
            $result = $check_stmt->get_result();
            
            if ($result->num_rows > 0) {
                // Update existing review
                $stmt = $conn->prepare("
                    UPDATE reviews 
                    SET rating = ?, comment = ?, is_approved = 0, review_at = CURRENT_TIMESTAMP 
                    WHERE user_id = ? AND destination_id = ?
                ");
                $stmt->bind_param("issi", $rating, $comment, $_SESSION['user_id'], $destination_id);
            } else {
                // Insert new review
                $stmt = $conn->prepare("
                    INSERT INTO reviews (destination_id, user_id, rating, comment, is_approved)
                    VALUES (?, ?, ?, ?, 0)
                ");
                $stmt->bind_param("isis", $destination_id, $_SESSION['user_id'], $rating, $comment);
            }
            
            if ($stmt->execute()) {
                $success = "Review submitted successfully! It will be visible after admin approval.";
            } else {
                throw new Exception("Failed to submit review: " . $stmt->error);
            }
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    }
}

// Get trip details if trip_id is provided
if ($trip_id > 0) {
    $trip_stmt = $conn->prepare("
        SELECT p.*, d.destination_id, dest.name as destination_name, dest.image_link
        FROM planned_trips p
        JOIN planned_destinations d ON p.plan_id = d.plan_id
        JOIN destinations dest ON d.destination_id = dest.destination_id
        WHERE p.plan_id = ? AND p.user_id = ?
        ORDER BY d.sequence_order
    ");
    
    if (!$trip_stmt) {
        die("Prepare failed: " . $conn->error);
    }
    
    $trip_stmt->bind_param("is", $trip_id, $_SESSION['user_id']);
    
    if (!$trip_stmt->execute()) {
        die("Execute failed: " . $trip_stmt->error);
    }
    
    $result = $trip_stmt->get_result();
    $trip_destinations = $result->fetch_all(MYSQLI_ASSOC);

    if (empty($trip_destinations)) {
        header("Location: past_trips.php");
        exit();
    }

    $trip = $trip_destinations[0]; // First row contains trip info
}

// Get destination details if destination_id is provided
if ($destination_id > 0 && ($trip_id === 0 || !isset($trip_destinations))) {
    $dest_stmt = $conn->prepare("SELECT * FROM destinations WHERE destination_id = ?");
    $dest_stmt->bind_param("i", $destination_id);
    
    if (!$dest_stmt->execute()) {
        die("Execute failed: " . $dest_stmt->error);
    }
    
    $result = $dest_stmt->get_result();
    $destination = $result->fetch_assoc();
    
    if (!$destination) {
        header("Location: past_trips.php");
        exit();
    }
}
?>

<div class="container mt-4">
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($trip_id > 0): ?>
        <h2 class="mb-4">Review Trip: <?php echo htmlspecialchars($trip['trip_name']); ?></h2>
        
        <div class="card mb-4">
            <div class="card-header">
                <h5>Trip Summary</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <p><strong>Dates:</strong> <?php echo date('M j, Y', strtotime($trip['start_date'])); ?> to <?php echo date('M j, Y', strtotime($trip['end_date'])); ?></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Transport:</strong> <?php echo htmlspecialchars($trip['mode_of_transport']); ?></p>
                    </div>
                    <div class="col-md-4">
                        <p><strong>Budget:</strong> ₹<?php echo number_format($trip['budget']); ?></p>
                    </div>
                </div>
            </div>
        </div>
        
        <h4 class="mb-3">Rate Destinations</h4>
        
        <?php foreach ($trip_destinations as $dest): ?>
            <div class="card mb-4">
                <div class="card-body">
                    <div class="row">
                        <?php if (!empty($dest['image_link'])): ?>
                            <div class="col-md-3">
                                <img src="<?php echo htmlspecialchars($dest['image_link']); ?>" 
                                     class="img-fluid rounded" 
                                     alt="<?php echo htmlspecialchars($dest['destination_name']); ?>"
                                     onerror="this.src='../assets/default-destination.jpg'">
                            </div>
                        <?php endif; ?>
                        
                        <div class="<?php echo !empty($dest['image_link']) ? 'col-md-9' : 'col-12'; ?>">
                            <h5><?php echo htmlspecialchars($dest['destination_name']); ?></h5>
                            
                            <?php
                            // Check for existing review
                            $review_stmt = $conn->prepare("
                                SELECT rating, comment 
                                FROM reviews 
                                WHERE user_id = ? AND destination_id = ?
                            ");
                            $review_stmt->bind_param("si", $_SESSION['user_id'], $dest['destination_id']);
                            $review_stmt->execute();
                            $review_result = $review_stmt->get_result();
                            $existing_review = $review_result->fetch_assoc();
                            ?>
                            
                            <form method="POST">
                                <input type="hidden" name="destination_id" value="<?php echo $dest['destination_id']; ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">Rating</label>
                                    <div class="rating">
                                        <?php for ($i = 5; $i >= 1; $i--): ?>
                                            <input type="radio" id="star<?php echo $i; ?>_<?php echo $dest['destination_id']; ?>" 
                                                   name="rating" value="<?php echo $i; ?>"
                                                   <?php if (isset($existing_review) && $existing_review['rating'] == $i) echo 'checked'; ?> required>
                                            <label for="star<?php echo $i; ?>_<?php echo $dest['destination_id']; ?>">★</label>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Review Comment</label>
                                    <textarea class="form-control" name="comment" rows="3" required><?php 
                                        echo isset($existing_review) ? htmlspecialchars($existing_review['comment']) : ''; 
                                    ?></textarea>
                                </div>
                                
                                <button type="submit" class="btn btn-primary">Submit Review</button>
                                <a href="past_trips.php" class="btn btn-secondary">Cancel</a>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
    <?php elseif ($destination_id > 0): ?>
        <h2 class="mb-4">Review Destination: <?php echo htmlspecialchars($destination['name']); ?></h2>
        
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <?php if (!empty($destination['image_link'])): ?>
                        <div class="col-md-3">
                            <img src="<?php echo htmlspecialchars($destination['image_link']); ?>" 
                                 class="img-fluid rounded" 
                                 alt="<?php echo htmlspecialchars($destination['name']); ?>"
                                 onerror="this.src='../assets/default-destination.jpg'">
                        </div>
                    <?php endif; ?>
                    
                    <div class="<?php echo !empty($destination['image_link']) ? 'col-md-9' : 'col-12'; ?>">
                        <?php
                        // Check for existing review
                        $review_stmt = $conn->prepare("
                            SELECT rating, comment 
                            FROM reviews 
                            WHERE user_id = ? AND destination_id = ?
                        ");
                        $review_stmt->bind_param("si", $_SESSION['user_id'], $destination_id);
                        $review_stmt->execute();
                        $review_result = $review_stmt->get_result();
                        $existing_review = $review_result->fetch_assoc();
                        ?>
                        
                        <form method="POST">
                            <input type="hidden" name="destination_id" value="<?php echo $destination_id; ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <div class="rating">
                                    <?php for ($i = 5; $i >= 1; $i--): ?>
                                        <input type="radio" id="star<?php echo $i; ?>_<?php echo $destination_id; ?>" 
                                               name="rating" value="<?php echo $i; ?>"
                                               <?php if (isset($existing_review) && $existing_review['rating'] == $i) echo 'checked'; ?> required>
                                        <label for="star<?php echo $i; ?>_<?php echo $destination_id; ?>">★</label>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Review Comment</label>
                                <textarea class="form-control" name="comment" rows="3" required><?php 
                                    echo isset($existing_review) ? htmlspecialchars($existing_review['comment']) : ''; 
                                ?></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                            <a href="past_trips.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}
.rating input {
    display: none;
}
.rating label {
    color: #ccc;
    font-size: 2em;
    cursor: pointer;
    transition: color 0.2s;
}
.rating input:checked ~ label {
    color: #ffc107;
}
.rating label:hover,
.rating label:hover ~ label,
.rating input:checked ~ label:hover {
    color: #ffc107;
}
</style>

<?php include('../maininclude/userfooter.php'); ?>