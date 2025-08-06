<?php
require_once 'config.php';

echo "=== ALL DATA EXPORT ===\n\n";

// Get all table data
$tables = ['admin', 'penyewa', 'acara', 'pemesanan', 'pembayaran', 'verifikasi_email'];

foreach ($tables as $table) {
    echo "=== $table TABLE DATA ===\n";
    $result = $pdo->query("SELECT * FROM $table");
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        print_r($row);
        echo "\n";
    }
    echo "\n";
}
?>
