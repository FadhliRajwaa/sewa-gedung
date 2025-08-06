<?php
require_once 'config.php';

try {
    // Update existing events status
    $update_query = "UPDATE acara SET status = 'tersedia' WHERE status = '' OR status IS NULL";
    $update_stmt = $pdo->prepare($update_query);
    $update_stmt->execute();
    echo "Updated existing events status\n";
    
    // Add more events if they don't exist
    $events = [
        ['Pernikahan', 500, 8000000, 'Gedung Pernikahan Mewah, Jl. Harmoni No. 88, Jakarta', 'tersedia', 'AC, Audio Visual, Dekorasi, Catering, Parkir Luas, Ruang Rias, Wi-Fi'],
        ['Rapat', 100, 2500000, 'Ruang Rapat Eksekutif, Jl. Sudirman No. 25, Jakarta', 'tersedia', 'AC, Proyektor, Whiteboard, Wi-Fi, Coffee Break, Parkir'],
        ['Seminar', 200, 4000000, 'Auditorium Modern, Jl. Thamrin No. 15, Jakarta', 'tersedia', 'AC, Sound System, Proyektor, Microphone, Wi-Fi, Parkir, Catering']
    ];
    
    foreach ($events as $event) {
        // Check if event already exists
        $check_query = "SELECT id_acara FROM acara WHERE nama_acara = ?";
        $check_stmt = $pdo->prepare($check_query);
        $check_stmt->execute([$event[0]]);
        
        if (!$check_stmt->fetch()) {
            $insert_query = "INSERT INTO acara (nama_acara, kapasitas, harga, lokasi, status, fasilitas) VALUES (?, ?, ?, ?, ?, ?)";
            $insert_stmt = $pdo->prepare($insert_query);
            $insert_stmt->execute($event);
            echo "Added event: " . $event[0] . "\n";
        } else {
            echo "Event already exists: " . $event[0] . "\n";
        }
    }
    
    // Show all events
    echo "\nAll events in database:\n";
    $query = "SELECT * FROM acara ORDER BY id_acara";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($events as $event) {
        echo "- " . $event['nama_acara'] . " (Status: " . $event['status'] . ")\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
