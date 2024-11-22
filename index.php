<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");  // If not logged in, redirect to login page
    exit;
}

require_once 'db/config.php';

$searchTerm = '';
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $stmt = $pdo->prepare("SELECT * FROM assets WHERE asset_number LIKE ? OR brand LIKE ? OR location LIKE ?");
    $stmt->execute(["%$searchTerm%", "%$searchTerm%", "%$searchTerm%"]);
} else {
    $stmt = $pdo->query("SELECT * FROM assets");
}
$assets = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Management - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Asset Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="add_asset.php">Add Asset</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2>Asset List</h2>
        
        <!-- Search Form -->
        <form method="get" action="" class="mb-4">
            <input type="text" class="form-control" name="search" value="<?php echo $searchTerm ?>" placeholder="Search by asset number, brand, or location">
            <button type="submit" class="btn btn-primary mt-2">Search</button>
        </form>

        <!-- Display assets -->
        <?php if (count($assets) > 0): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Asset Number</th>
                        <th>Brand</th>
                        <th>Location</th>
                        <th>Asset Type</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($assets as $asset): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($asset['asset_number']); ?></td>
                            <td><?php echo htmlspecialchars($asset['brand']); ?></td>
                            <td><?php echo htmlspecialchars($asset['location']); ?></td>
                            <td><?php echo htmlspecialchars($asset['asset_type']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No assets found.</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
