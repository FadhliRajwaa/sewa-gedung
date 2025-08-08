<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once '../../config.php';

// Get period parameter
$period = $_GET['period'] ?? '6months';

try {
    // Determine date range based on period
    $dateCondition = '';
    $monthsBack = 6;
    
    switch ($period) {
        case '1year':
            $monthsBack = 12;
            $dateCondition = "WHERE p.tanggal_sewa >= DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH)";
            break;
        case 'all':
            $monthsBack = 24; // Show last 24 months for 'all'
            $dateCondition = "WHERE p.tanggal_sewa IS NOT NULL";
            break;
        case '6months':
        default:
            $monthsBack = 6;
            $dateCondition = "WHERE p.tanggal_sewa >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 MONTH)";
            break;
    }

    // Get chart data based on tanggal_sewa
    $stmt = $pdo->query("
        SELECT 
            DATE_FORMAT(p.tanggal_sewa, '%Y-%m') as period,
            COUNT(*) as count
        FROM pemesanan p
        $dateCondition
        GROUP BY DATE_FORMAT(p.tanggal_sewa, '%Y-%m')
        ORDER BY period
    ");
    
    $chartData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $labels = [];
    $values = [];
    
    // Generate labels based on period
    for ($i = $monthsBack - 1; $i >= 0; $i--) {
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
