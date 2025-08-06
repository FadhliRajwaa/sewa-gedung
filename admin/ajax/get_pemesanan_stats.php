<?php
session_start();
require_once '../../config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

try {
    // Get total count
    $totalQuery = "SELECT COUNT(*) as total FROM pemesanan";
    $stmt = $pdo->prepare($totalQuery);
    $stmt->execute();
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Get count by payment status
    $statusQuery = "SELECT pb.status_pembayaran, COUNT(*) as count 
                   FROM pemesanan p 
                   LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan 
                   GROUP BY pb.status_pembayaran";
    $stmt = $pdo->prepare($statusQuery);
    $stmt->execute();
    $statusCounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $result = [
        'total' => $total,
        'lunas' => 0,
        'belum_lunas' => 0,
        'no_payment' => 0
    ];
    
    foreach ($statusCounts as $status) {
        if ($status['status_pembayaran'] === 'Lunas') {
            $result['lunas'] = $status['count'];
        } elseif ($status['status_pembayaran'] === 'Belum Lunas') {
            $result['belum_lunas'] = $status['count'];
        } elseif ($status['status_pembayaran'] === null) {
            $result['no_payment'] = $status['count'];
        }
    }
    
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
