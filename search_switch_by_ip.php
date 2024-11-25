<?php
include_once 'db/config.php';

if (isset($_POST['ip_address'])) {
    $ip = $_POST['ip_address'];

    $stmt = $pdo->prepare("SELECT id, ip_address FROM switches WHERE ip_address = :ip");
    $stmt->execute([':ip' => $ip]);
    $switch = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($switch) {
        echo json_encode(['success' => true, 'switch_id' => $switch['id']]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>
