<?php
require_once '../../config.php';
header('Content-Type: application/json');

try {
    // Mock session untuk testing
    session_start();
    $_SESSION['admin_logged_in'] = true;
    
    // Test get_riwayat
    echo "\n=== Testing get_riwayat.php ===\n";
    ob_start();
    include 'get_riwayat.php';
    $riwayat_output = ob_get_clean();
    echo "get_riwayat.php output:\n" . $riwayat_output . "\n\n";
    
    // Test get_riwayat_stats
    echo "=== Testing get_riwayat_stats.php ===\n";
    ob_start();
    include 'get_riwayat_stats.php';
    $riwayat_stats_output = ob_get_clean();
    echo "get_riwayat_stats.php output:\n" . $riwayat_stats_output . "\n\n";
    
    // Test get_laporan
    echo "=== Testing get_laporan.php ===\n";
    ob_start();
    include 'get_laporan.php';
    $laporan_output = ob_get_clean();
    echo "get_laporan.php output:\n" . $laporan_output . "\n\n";
    
    // Test get_laporan_stats
    echo "=== Testing get_laporan_stats.php ===\n";
    ob_start();
    include 'get_laporan_stats.php';
    $laporan_stats_output = ob_get_clean();
    echo "get_laporan_stats.php output:\n" . $laporan_stats_output . "\n\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
