<?php
// Include database connection
include 'db_connection.php';

// Get switch ID from POST data
$switch_id = $_POST['switch_id'];

// Fetch the switch's port count
$query = "SELECT port_count FROM switches WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('i', $switch_id);
$stmt->execute();
$stmt->bind_result($port_count);
$stmt->fetch();

// Return the available ports as JSON
echo json_encode(range(1, $port_count));
