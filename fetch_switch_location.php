<?php
// Include database connection
include 'db_connection.php';

// Check if switch_id is provided
if (isset($_POST['switch_id'])) {
    $switch_id = $_POST['switch_id'];

    // Query to get the switch location
    $query = "SELECT location FROM switches WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $switch_id);
    $stmt->execute();
    $stmt->bind_result($switch_location);

    // Check if a result was found
    if ($stmt->fetch()) {
        // Return switch location as a JSON response
        echo json_encode(['success' => true, 'switch_location' => $switch_location]);
    } else {
        // If no location found, return error
        echo json_encode(['success' => false]);
    }

    $stmt->close();
}
?>
