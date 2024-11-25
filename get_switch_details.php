<?php
include_once 'db/config.php';

if (isset($_GET['id'])) {
    $switch_id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT ip_address, ports FROM switches WHERE id = :switch_id");
    $stmt->execute([':switch_id' => $switch_id]);
    $switch = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($switch) {
        // Assuming ports are stored as comma-separated strings
        $ports = explode(',', $switch['ports']);
        echo json_encode([
            'ip_address' => $switch['ip_address'],
            'ports' => $ports
        ]);
    } else {
        echo json_encode(['error' => 'Switch not found']);
    }
}
