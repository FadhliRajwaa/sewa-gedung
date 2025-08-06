<?php
require_once 'config.php';

try {
    // Update Acara A and Acara B to more descriptive names
    $updates = [
        [1, 'Gedung Serbaguna A', 'Gedung Serbaguna Premium, Jl. Raya No. 123, Jakarta'],
        [2, 'Gedung Serbaguna B', 'Gedung Serbaguna Standar, Jl. Sudirman No. 45, Bandung']
    ];
    
    foreach ($updates as $update) {
        $query = "UPDATE acara SET nama_acara = ?, lokasi = ? WHERE id_acara = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$update[1], $update[2], $update[0]]);
        echo "Updated event ID " . $update[0] . " to: " . $update[1] . "\n";
    }
    
    echo "\nAll events now:\n";
    $query = "SELECT id_acara, nama_acara, kapasitas, harga FROM acara ORDER BY id_acara";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($events as $event) {
        echo "- " . $event['nama_acara'] . " (Kapasitas: " . $event['kapasitas'] . ", Harga: Rp " . number_format($event['harga']) . ")\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
