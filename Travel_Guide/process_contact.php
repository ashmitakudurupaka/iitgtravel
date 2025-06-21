<?php
// Include database connection
include('./config/dbConnection.php');
include('./maininclude/header.php');

// Initialize response variables
$response = "";
$alertClass = "";

// Process form submission
if(isset($_POST['submit'])) {
    // Sanitize and get form data
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $roll = mysqli_real_escape_string($conn, $_POST['roll']); // Optional field
    $query_type = mysqli_real_escape_string($conn, $_POST['query_type']);
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    
    // Check if newsletter subscription is checked
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    
    // Current date and time
    $submission_date = date('Y-m-d H:i:s');
    
    // Default status for new inquiries
    $status = 'pending';
    
    // SQL query to insert data into contacts table
    $sql = "INSERT INTO contacts (name, roll_number, query_type, subject, email, message, newsletter, submission_date, status) 
            VALUES ('$name', '$roll', '$query_type', '$subject', '$email', '$message', $newsletter, '$submission_date', '$status')";
    
    // Execute query and check if successful
    if(mysqli_query($conn, $sql)) {
        $response = "Thank you for contacting us! Your message has been received.";
        $alertClass = "alert-success";
        
        // You could also add email notification code here to alert administrators
        
    } else {
        $response = "Error: " . mysqli_error($conn);
        $alertClass = "alert-danger";
    }
}
?>

<!-- Display response message if exists -->
<?php if(!empty($response)): ?>
<div class="container mt-4">
    <div class="alert <?php echo $alertClass; ?> alert-dismissible fade show" role="alert">
        <?php echo $response; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>
<?php endif; ?>

<!-- Redirect back after showing message -->
<script>
    // Wait for 3 seconds before redirecting
    setTimeout(function() {
        window.location.href = 'index.php#Contact';
    }, 3000);
</script>

<?php
// Include footer
include('./maininclude/footer.php');
?>