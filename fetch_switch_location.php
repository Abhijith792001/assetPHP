<?php
// Include database connection
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get switch ID from the POST request
    $switch_id = $_POST['switch_id'];

    // Query to fetch the switch location
    $query = "SELECT location FROM switches WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $switch_id);  // Assuming switch_id is an integer
    $stmt->execute();
    $stmt->bind_result($switch_location);
    $stmt->fetch();
    
    // Output the switch location or an empty string if not found
    echo $switch_location ? $switch_location : '';
}
?>
