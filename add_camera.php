<?php
// Include database connection
include 'db_connection.php';

// Fetch all switches
$switches_query = "SELECT * FROM switches";
$switches_result = $conn->query($switches_query);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $asset_number = $_POST['asset_number'];
    $brand = $_POST['brand'];
    $location = $_POST['location'];
    $ip_address = $_POST['ip_address'];
    $mac_address = $_POST['mac_address'];
    $switch_id = $_POST['switch_id'];
    $port = $_POST['port'];
    $switch_location = isset($_POST['switch_location']) ? $_POST['switch_location'] : '';  // Fetch switch location

    // Validate switch_location (ensure it's not empty)
    if (empty($switch_location)) {
        echo "<div class='alert alert-danger'>Switch location is required!</div>";
    } else {
        // Insert camera into database, including the new switch location
        $sql = "INSERT INTO cameras (asset_number, brand, location, ip_address, mac_address, switch_id, port, switch_location) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssssssis', $asset_number, $brand, $location, $ip_address, $mac_address, $switch_id, $port, $switch_location);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success'>Camera added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error adding camera: " . $stmt->error . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Camera</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Include Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h2>Add Camera</h2>
        <form id="add_camera_form" method="POST">
            <div class="form-group">
                <label for="asset_number">Asset Number</label>
                <input type="text" class="form-control" id="asset_number" name="asset_number" required>
            </div>
            <div class="form-group">
                <label for="brand">Brand</label>
                <input type="text" class="form-control" id="brand" name="brand" required>
            </div>
            <div class="form-group">
                <label for="location">Location</label>
                <input type="text" class="form-control" id="location" name="location" required>
            </div>
            <div class="form-group">
                <label for="ip_address">IP Address</label>
                <input type="text" class="form-control" id="ip_address" name="ip_address" required>
            </div>
            <div class="form-group">
                <label for="mac_address">MAC Address</label>
                <input type="text" class="form-control" id="mac_address" name="mac_address" required>
            </div>
            <div class="form-group">
                <label for="switch_id">Connected Switch</label>
                <select class="form-control" id="switch_id" name="switch_id" required>
                    <option value="">Select a Switch</option>
                    <?php while ($row = $switches_result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['ip_address']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="port">Port</label>
                <select class="form-control" id="port" name="port" required>
                    <option value="">Select Port</option>
                </select>
            </div>
            <!-- New field for Switch Location -->
            <div class="form-group">
    <label for="switch_location">Switch Location</label>
    <input type="text" class="form-control" id="switch_location" name="switch_location" required>
</div>

            <button type="submit" class="btn btn-primary">Add Camera</button>
        </form>
    </div>

    <script>
        // Apply Select2 to the switch select element for search functionality
        $('#switch_id').select2({
            placeholder: "Select a Switch",
            allowClear: true
        });

        // Fetch ports for selected switch using AJAX
        $('#switch_id').change(function() {
            var switch_id = $(this).val();
            if (switch_id) {
                $.ajax({
                    url: 'fetch_ports.php',
                    method: 'POST',
                    data: { switch_id: switch_id },
                    success: function(response) {
                        var ports = JSON.parse(response);
                        var portOptions = '<option value="">Select Port</option>';
                        for (var i = 1; i <= ports.length; i++) {
                            portOptions += '<option value="' + i + '">Port ' + i + '</option>';
                        }
                        $('#port').html(portOptions);
                    }
                });
            }
        });
    </script>
</body>
</html>
