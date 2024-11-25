<?php
// Include database connection
include 'db_connection.php';

// Initialize variables
$view_type = 'camera'; // Default to 'camera' view
$search_query = '';

// Check if form is submitted and get view type (Camera or Switch)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $view_type = isset($_POST['view_type']) ? $_POST['view_type'] : 'camera';  // Default to 'camera' view
    $search_query = isset($_POST['search_query']) ? $_POST['search_query'] : '';  // Capture search query
}

// Query to fetch cameras, with search filter applied
$sql_cameras = "SELECT * FROM cameras WHERE 1";
if ($search_query) {
    $sql_cameras .= " AND (asset_number LIKE '%$search_query%' OR brand LIKE '%$search_query%' 
        OR location LIKE '%$search_query%' OR ip_address LIKE '%$search_query%' OR mac_address LIKE '%$search_query%')";
}
$cameras_result = $conn->query($sql_cameras);

// Query to fetch switches, with search filter applied
$sql_switches = "SELECT * FROM switches WHERE 1";
if ($search_query) {
    $sql_switches .= " AND (asset_number LIKE '%$search_query%' OR ip_address LIKE '%$search_query%' 
        OR location LIKE '%$search_query%')";
}
$switches_result = $conn->query($sql_switches);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Devices</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Style for the pop-up filter modal */
        .modal-body {
            padding: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Search Devices</h2>

        <!-- Search Form -->
        <form method="POST" id="searchForm">
            <input type="hidden" name="view_type" id="view_type" value="<?php echo htmlspecialchars($view_type); ?>">
            <div class="form-row">
                <!-- Search Input (Optional) -->
                <div class="form-group col-md-8">
                    <input type="text" class="form-control" placeholder="Search..." id="search_query" name="search_query" value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
                <!-- Filter Button -->
                <div class="form-group col-md-4">
                    <button type="button" class="btn btn-info" data-toggle="modal" data-target="#filterModal">Filter</button>
                </div>
            </div>
        </form>

        <!-- Buttons to switch between Camera and Switch View -->
        <div class="btn-group" role="group" aria-label="View Switcher">
            <button type="button" class="btn btn-primary" id="cameraViewBtn">Camera </button>
            <button type="button" class="btn btn-secondary" id="switchViewBtn">Switch </button>
        </div>

        <!-- Cameras Table (Visible by default) -->
        <div id="cameraTable" style="display: <?php echo ($view_type == 'camera') ? 'block' : 'none'; ?>;">
            <h4>Cameras</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Asset Number</th>
                        <th>Brand</th>
                        <th>Location</th>
                        <th>IP Address</th>
                        <th>MAC Address</th>
                        <th>Port</th>
                        <th>Switch Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($camera = $cameras_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $camera['asset_number']; ?></td>
                            <td><?php echo $camera['brand']; ?></td>
                            <td><?php echo $camera['location']; ?></td>
                            <td><?php echo $camera['ip_address']; ?></td>
                            <td><?php echo $camera['mac_address']; ?></td>
                            <td><?php echo $camera['port']; ?></td>
                            <td><?php echo $camera['switch_location']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Switches Table (Hidden by default) -->
        <div id="switchTable" style="display: <?php echo ($view_type == 'switch') ? 'block' : 'none'; ?>;">
            <h4>Switches</h4>
            <table class="table">
                <thead>
                    <tr>
                        <th>Asset Number</th>
                        <th>IP Address</th>
                        <th>Port Count</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($switch = $switches_result->fetch_assoc()) { ?>
                        <tr>
                            <td><?php echo $switch['asset_number']; ?></td>
                            <td><?php echo $switch['ip_address']; ?></td>
                            <td><?php echo $switch['port_count']; ?></td>
                            <td><?php echo $switch['location']; ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Select View</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <button class="btn btn-primary" id="applyCameraView">Camera</button>
                    <button class="btn btn-secondary" id="applySwitchView">Switch</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle Camera View Button click
       // Handle Camera View Button click
$('#cameraViewBtn').click(function() {
    $('#view_type').val('camera'); // Set view type to 'camera'
    $('#searchForm').submit();  // Submit the form

    // Change the color of the buttons to reflect the active view
    $('#cameraViewBtn').addClass('btn-primary').removeClass('btn-secondary');
    $('#switchViewBtn').addClass('btn-secondary').removeClass('btn-primary');
});

// Handle Switch View Button click
$('#switchViewBtn').click(function() {
    $('#view_type').val('switch'); // Set view type to 'switch'
    $('#searchForm').submit();  // Submit the form

    // Change the color of the buttons to reflect the active view
    $('#cameraViewBtn').addClass('btn-secondary').removeClass('btn-primary');
    $('#switchViewBtn').addClass('btn-primary').removeClass('btn-secondary');
});

// Apply Camera View from Modal
$('#applyCameraView').click(function() {
    $('#view_type').val('camera'); // Set view type to 'camera'
    $('#searchForm').submit();  // Submit the form

    // Change the color of the buttons to reflect the active view
    $('#cameraViewBtn').addClass('btn-primary').removeClass('btn-secondary');
    $('#switchViewBtn').addClass('btn-secondary').removeClass('btn-primary');
});

// Apply Switch View from Modal
$('#applySwitchView').click(function() {
    $('#view_type').val('switch'); // Set view type to 'switch'
    $('#searchForm').submit();  // Submit the form

    // Change the color of the buttons to reflect the active view
    $('#cameraViewBtn').addClass('btn-secondary').removeClass('btn-primary');
    $('#switchViewBtn').addClass('btn-primary').removeClass('btn-secondary');
});


        // Optional: Use AJAX for dynamic updates (avoid page reload)
        $('#search_query').on('input', function() {
            var searchValue = $(this).val();
            $.ajax({
                url: 'your-ajax-handler.php',  // Replace with the appropriate script
                method: 'GET',
                data: { search_query: searchValue, view_type: '<?php echo $view_type; ?>' },
                success: function(response) {
                    // Update table with the response
                    $('#cameraTable tbody').html(response);
                }
            });
        });
    </script>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
