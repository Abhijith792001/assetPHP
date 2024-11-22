<?php
$host = 'localhost';  // MySQL server address
$dbname = 'asset_management';  // Database name
$username = 'root';  // MySQL username
$password = '';  // MySQL password (default is empty)

try {
    // Create a PDO instance to handle the connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
