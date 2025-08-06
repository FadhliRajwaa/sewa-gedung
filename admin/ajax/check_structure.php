<?php
require_once '../../config.php';

try {
    echo "=== Struktur Tabel Pemesanan ===\n";
    $stmt = $pdo->query("DESCRIBE pemesanan");
    $columns = $stmt->fetchAll();
    foreach ($columns as $col) {
        echo $col['Field'] . " - " . $col['Type'] . "\n";
    }
    
    echo "\n=== Struktur Tabel Pembayaran ===\n";
    $stmt = $pdo->query("DESCRIBE pembayaran");
    $columns = $stmt->fetchAll();
    foreach ($columns as $col) {
        echo $col['Field'] . " - " . $col['Type'] . "\n";
    }
    
    echo "\n=== Sample Data Pemesanan ===\n";
    $stmt = $pdo->query("SELECT * FROM pemesanan LIMIT 2");
    $data = $stmt->fetchAll();
    foreach ($data as $row) {
        print_r($row);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
