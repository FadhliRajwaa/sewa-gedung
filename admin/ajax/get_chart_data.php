<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once '../../config.php';

try {
    // Get chart data for last 6 months
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(p.tanggal_pesan, '%Y-%m') as period,
            COUNT(*) as count
        FROM pemesanan p
        WHERE p.tanggal_pesan >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(p.tanggal_pesan, '%Y-%m')
        ORDER BY period
    ");
    
    $chartData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $labels = [];
    $values = [];
    
    // Generate last 6 months labels
    for ($i = 5; $i >= 0; $i--) {
        $date = date('Y-m', strtotime("-$i months"));
        $labels[] = date('M Y', strtotime("-$i months"));
        
        // Find matching data
        $found = false;
        foreach ($chartData as $data) {
            if ($data['period'] === $date) {
                $values[] = (int)$data['count'];
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            $values[] = 0;
        }
    }
    
    echo json_encode([
        'labels' => $labels,
        'values' => $values
    ]);
    
} catch (Exception $e) {
    error_log("Error in get_chart_data.php: " . $e->getMessage());
    echo json_encode(['error' => 'Internal server error']);
}
?>
