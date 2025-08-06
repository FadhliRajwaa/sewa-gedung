<?php
require_once 'config.php';
try {
    $query = 'SELECT id_acara, nama_acara, harga FROM acara ORDER BY id_acara';
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Daftar Acara dan Harga:\n";
    foreach ($events as $event) {
        echo "ID: " . $event['id_acara'] . " - " . $event['nama_acara'] . " - Rp " . number_format($event['harga'], 0, ',', '.') . "\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
