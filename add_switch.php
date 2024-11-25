<?php
// Include database connection
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect POST data and ensure that location is not empty
    $asset_number = $_POST['asset_number'];
    $ip_address = $_POST['ip_address'];
    $port_count = $_POST['port_count'];
    $location = isset($_POST['location']) ? trim($_POST['location']) : '';  // Check if location is set and not empty

    // Ensure location is not empty before inserting
    if (empty($location)) {
        echo "<div class='alert alert-danger'>Location is required!</div>";
    } else {
        // Insert the switch into the database, including the location
        $sql = "INSERT INTO switches (asset_number, ip_address, port_count, location) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssis', $asset_number, $ip_address, $port_count, $location);  // Bind the new parameter 'location'

        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Switch added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error adding switch: " . $stmt->error . "</div>";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Switch</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Add Switch</h2>
        <form action="add_switch.php" method="POST">
            <div class="form-group">
                <label for="asset_number">Asset Number</label>
                <input type="text" class="form-control" id="asset_number" name="asset_number" required>
            </div>
            <div class="form-group">
                <label for="ip_address">IP Address</label>
                <input type="text" class="form-control" id="ip_address" name="ip_address" required>
            </div>
            <div class="form-group">
                <label for="port_count">Port Count</label>
                <input type="number" class="form-control" id="port_count" name="port_count" required>
            </div>
            <div class="form-group">
    <label for="location">Location</label>
    <input type="text" class="form-control" id="location" name="location" required>
</div>


            <button type="submit" class="btn btn-primary">Add Switch</button>
        </form>
    </div>
</body>
</html>
