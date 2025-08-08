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
    
    // Get confirmed count (status = 'confirmed' OR 'completed')
    $confirmedQuery = "SELECT COUNT(*) as count FROM pemesanan WHERE status IN ('confirmed', 'completed')";
    $stmt = $pdo->prepare($confirmedQuery);
    $stmt->execute();
    $confirmed = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get pending count (status = 'pending')
    $pendingQuery = "SELECT COUNT(*) as count FROM pemesanan WHERE status = 'pending' OR status IS NULL";
    $stmt = $pdo->prepare($pendingQuery);
    $stmt->execute();
    $pending = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    // Get total revenue from confirmed bookings
    $revenueQuery = "SELECT COALESCE(SUM(p.total), 0) as revenue 
                    FROM pemesanan p 
                    LEFT JOIN pembayaran pb ON p.id_pemesanan = pb.id_pemesanan 
                    WHERE pb.status_pembayaran = 'Lunas' OR p.status IN ('confirmed', 'completed')";
    $stmt = $pdo->prepare($revenueQuery);
    $stmt->execute();
    $revenue = $stmt->fetch(PDO::FETCH_ASSOC)['revenue'];
    
    $result = [
        'total' => $total,
        'confirmed' => $confirmed,
        'pending' => $pending,
        'revenue' => number_format($revenue, 0, ',', '.')
    ];
    
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
