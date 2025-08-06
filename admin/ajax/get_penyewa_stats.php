<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Get total count
    $totalQuery = "SELECT COUNT(*) as total FROM penyewa";
    $stmt = $pdo->prepare($totalQuery);
    $stmt->execute();
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get count by type
    $typeQuery = "SELECT tipe_penyewa, COUNT(*) as count FROM penyewa GROUP BY tipe_penyewa";
    $stmt = $pdo->prepare($typeQuery);
    $stmt->execute();
    $typeCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get verified count
    $verifiedQuery = "SELECT COUNT(*) as count FROM penyewa WHERE email_terverifikasi = 1";
    $stmt = $pdo->prepare($verifiedQuery);
    $stmt->execute();
    $verified = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    $result = [
        'total' => $total,
        'individu' => 0,
        'instansi' => 0,
        'verified' => $verified
    ];
    
    foreach ($typeCounts as $type) {
        $result[$type['tipe_penyewa']] = $type['count'];
    }
    
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
