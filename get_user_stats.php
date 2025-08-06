<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_penyewa'])) {
    echo json_encode(['total' => 0, 'active' => 0, 'completed' => 0]);
    exit;
}

$id_penyewa = $_SESSION['id_penyewa'];

try {
    // Get total bookings
    $totalQuery = "SELECT COUNT(*) as total FROM pemesanan WHERE id_penyewa = ?";
    $stmt = $pdo->prepare($totalQuery);
    $stmt->execute([$id_penyewa]);
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get active bookings (future events)
    $activeQuery = "SELECT COUNT(*) as active FROM pemesanan WHERE id_penyewa = ? AND tanggal_sewa >= CURDATE()";
    $stmt = $pdo->prepare($activeQuery);
    $stmt->execute([$id_penyewa]);
    $active = $stmt->fetch(PDO::FETCH_ASSOC)['active'];

    // Get completed bookings (past events)
    $completedQuery = "SELECT COUNT(*) as completed FROM pemesanan WHERE id_penyewa = ? AND tanggal_selesai < CURDATE()";
    $stmt = $pdo->prepare($completedQuery);
    $stmt->execute([$id_penyewa]);
    $completed = $stmt->fetch(PDO::FETCH_ASSOC)['completed'];

    echo json_encode([
        'total' => (int)$total,
        'active' => (int)$active,
        'completed' => (int)$completed
    ]);

} catch (PDOException $e) {
    echo json_encode(['total' => 0, 'active' => 0, 'completed' => 0]);
}
?>
