<?php
// Include database connection
include_once 'db/config.php';

// Initialize filter variables
$search_term = isset($_GET['search_term']) ? $_GET['search_term'] : '';
$filter_asset_type = isset($_GET['asset_type']) ? $_GET['asset_type'] : '';

// Build the query dynamically based on the search inputs
$query = "SELECT * FROM switches WHERE 1";
if ($search_term) {
    $query .= " AND (asset_number LIKE :search_term OR brand LIKE :search_term OR location LIKE :search_term OR ip_address LIKE :search_term)";
}

// Fetch switches from database
$stmt = $pdo->prepare($query);
if ($search_term) {
    $stmt->bindValue(':search_term', '%' . $search_term . '%');
}
$stmt->execute();
$switches = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch cameras with optional filter
$query2 = "SELECT * FROM cameras WHERE 1";
if ($search_term) {
    $query2 .= " AND (asset_number LIKE :search_term OR brand LIKE :search_term OR location LIKE :search_term OR ip_address LIKE :search_term)";
}

$stmt2 = $pdo->prepare($query2);
if ($search_term) {
    $stmt2->bindValue(':search_term', '%' . $search_term . '%');
}
$stmt2->execute();
$cameras = $stmt2->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Management - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .filter-panel { display: none; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg bg-light">
    <a class="navbar-brand" href="#">Asset Management</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
            
            <!-- Add Device Dropdown -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Add Device
                </a>
                <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <li><a class="dropdown-item" href="add_switch.php">Add Switch</a></li>
                    <li><a class="dropdown-item" href="add_camera.php">Add Camera</a></li>
                </ul>
            </li>
            
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <h2>Device Search History</h2>

    <!-- Search Box and Filter Button -->
    <div class="d-flex justify-content-between mb-3">
        <input type="text" class="form-control w-75" id="searchBox" name="search_term" placeholder="Search Assets..." value="<?= htmlspecialchars($search_term) ?>" />
        <button class="btn btn-primary" id="filterBtn">Filter</button>
    </div>

    <!-- Filter Panel -->
    <div class="filter-panel">
        <h5>Filter Options</h5>
        <form method="GET" action="" id="filterForm">
            <div class="row">
                <div class="col-md-3">
                    <label for="asset_type" class="form-label">Asset Type</label>
                    <select class="form-control" name="asset_type" id="assetType">
                        <option value="">All</option>
                        <option value="Switch" <?= $filter_asset_type == 'Switch' ? 'selected' : ''; ?>>Switch</option>
                        <option value="Camera" <?= $filter_asset_type == 'Camera' ? 'selected' : ''; ?>>Camera</option>
                    </select>
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-success">Apply Filters</button>
                <button type="button" class="btn btn-danger" id="clearFilters">Clear Filters</button>
            </div>
        </form>
    </div>

    <!-- Search Results Section -->
    <?php if ($search_term || $filter_asset_type): ?>
    <h3>Search Results</h3>
    <p><strong>Showing results for:</strong> <?= htmlspecialchars($search_term) ?: 'All Assets' ?> <?= htmlspecialchars($filter_asset_type) ? " | Asset Type: " . htmlspecialchars($filter_asset_type) : '' ?></p>

    <!-- Unified Device Search Table (Switches and Cameras) -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Asset Type</th>
                <th>Asset Number</th>
                <th>Brand</th>
                <th>Location</th>
                <th>MAC Address</th>
                <th>IP Address</th>
                <th>Switch (if Camera)</th>
                <th>Port Number (if Camera)</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            // Display Switches in the table
            foreach ($switches as $switch): 
                if ($filter_asset_type && $filter_asset_type != 'Switch') continue; // If filtered, only show matching asset type
            ?>
            <tr>
                <td>Switch</td>
                <td><?= htmlspecialchars($switch['asset_number']); ?></td>
                <td><?= htmlspecialchars($switch['brand']); ?></td>
                <td><?= htmlspecialchars($switch['location']); ?></td>
                <td>--</td>
                <td><?= htmlspecialchars($switch['ip_address']); ?></td>
                <td>--</td>
                <td><?= htmlspecialchars($switch['port_number']); ?></td>
            </tr>
            <?php endforeach; ?>

            <?php 
            // Display Cameras in the table
            foreach ($cameras as $camera): 
                if ($filter_asset_type && $filter_asset_type != 'Camera') continue; // If filtered, only show matching asset type
            ?>
            <tr>
                <td>Camera</td>
                <td><?= htmlspecialchars($camera['asset_number']); ?></td>
                <td><?= htmlspecialchars($camera['brand']); ?></td>
                <td><?= htmlspecialchars($camera['location']); ?></td>
                <td><?= htmlspecialchars($camera['mac_address']); ?></td>
                <td><?= htmlspecialchars($camera['ip_address']); ?></td>
                <td>
                    <?php 
                    // Find the corresponding switch (if exists)
                    $switch_id = isset($camera['switch_id']) ? $camera['switch_id'] : null;
                    echo $switch_id ? htmlspecialchars($switches[$switch_id - 1]['asset_number']) : '--';
                    ?>
                </td>
                <td>
                    <?php 
                    // Show port number if switch exists
                    echo $switch_id ? htmlspecialchars($switches[$switch_id - 1]['port_number']) : '--';
                    ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <?php else: ?>
    <!-- No Search Results, message -->
    <p>No filters applied yet. Please enter search criteria and select filters.</p>
    <?php endif; ?>
</div>

<!-- JavaScript for Filter Toggle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('filterBtn').addEventListener('click', function() {
        document.querySelector('.filter-panel').style.display = 'block';
    });

    document.getElementById('clearFilters').addEventListener('click', function() {
        // Clear the search and filter options
        document.getElementById('searchBox').value = '';
        document.getElementById('assetType').value = '';
        window.location.href = 'index.php'; // Reload the page to clear GET params
    });
</script>
</body>
</html>
