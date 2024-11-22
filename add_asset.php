<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");  // Redirect to login if not logged in
    exit;
}

require_once 'db/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $asset_number = $_POST['asset_number'];
    $brand = $_POST['brand'];
    $location = $_POST['location'];
    $asset_type = $_POST['asset_type'];

    // Check for duplicate asset number
    $stmt = $pdo->prepare("SELECT * FROM assets WHERE asset_number = ?");
    $stmt->execute([$asset_number]);
    if ($stmt->rowCount() > 0) {
        $error = "Asset number already exists!";
    } else {
        // Insert the new asset into the database
        $stmt = $pdo->prepare("INSERT INTO assets (asset_number, brand, location, asset_type) VALUES (?, ?, ?, ?)");
        $stmt->execute([$asset_number, $brand, $location, $asset_type]);
        $success = "Asset added successfully!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Asset - Asset Management</title>
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
        <h2>Add New Asset</h2>

        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <?php if (isset($success)) { echo "<div class='alert alert-success'>$success</div>"; } ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="asset_number" class="form-label">Asset Number</label>
                <input type="text" class="form-control" name="asset_number" required>
            </div>
            <div class="mb-3">
                <label for="brand" class="form-label">Brand</label>
                <input type="text" class="form-control" name="brand" required>
            </div>
            <div class="mb-3">
                <label for="location" class="form-label">Location</label>
                <input type="text" class="form-control" name="location" required>
            </div>
            <div class="mb-3">
                <label for="asset_type" class="form-label">Asset Type</label>
                <select name="asset_type" class="form-control" required>
                    <option value="Camera">Camera</option>
                    <option value="Switch">Switch</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Add Asset</button>
        </form>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
