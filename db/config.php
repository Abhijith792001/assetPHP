<?php
$host = 'localhost';  // MySQL server address
$dbname = 'asset_management';  // Database name
$username = 'root';  // MySQL username (default is root)
$password = '';  // MySQL password (default is empty for 'root')

try {
    // Establishing a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);  // Set PDO error mode to Exception
} catch (PDOException $e) {
    // If the connection fails, show an error message
    die("Could not connect to the database $dbname :" . $e->getMessage());
}
?>
