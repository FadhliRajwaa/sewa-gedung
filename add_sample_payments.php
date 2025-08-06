<?php
require_once 'config.php';

try {
    // Insert sample payment data
    $payments = [
        [1, 'payment_1_sample.jpg', 'Belum Lunas'],
        [2, 'payment_2_sample.jpg', 'Lunas']
    ];
    
    foreach ($payments as $payment) {
        $check_query = "SELECT id_pembayaran FROM pembayaran WHERE id_pemesanan = ?";
        $check_stmt = $pdo->prepare($check_query);
        $check_stmt->execute([$payment[0]]);
        
        if (!$check_stmt->fetch()) {
            $insert_query = "INSERT INTO pembayaran (id_pemesanan, bukti_pembayaran, status_pembayaran, tanggal_upload) VALUES (?, ?, ?, NOW())";
            $insert_stmt = $pdo->prepare($insert_query);
            $insert_stmt->execute($payment);
            echo "Inserted payment for booking ID: " . $payment[0] . "\n";
        } else {
            echo "Payment already exists for booking ID: " . $payment[0] . "\n";
        }
    }
    
    echo "Sample payment data added successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
