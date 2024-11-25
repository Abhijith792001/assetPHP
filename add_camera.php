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

    // Insert camera into database
    $sql = "INSERT INTO cameras (asset_number, brand, location, ip_address, mac_address, switch_id, port) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssi', $asset_number, $brand, $location, $ip_address, $mac_address, $switch_id, $port);
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Camera added successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error adding camera.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Camera</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                <label for="switch_id">Switch</label>
                <select class="form-control" id="switch_id" name="switch_id" required>
                    <option value="">Select a Switch</option>
                    <?php while ($row = $switches_result->fetch_assoc()) { ?>
                        <option value="<?php echo $row['id']; ?>"><?php echo $row['asset_number']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="form-group">
                <label for="port">Port</label>
                <select class="form-control" id="port" name="port" required>
                    <option value="">Select Port</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Camera</button>
        </form>
    </div>

    <script>
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