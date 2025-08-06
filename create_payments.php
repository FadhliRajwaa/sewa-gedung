<?php
require_once 'config.php';
// Add some sample payment records
$payments = [
    [1, 'bukti1.jpg', 'Lunas'],
    [2, 'bukti2.jpg', 'Belum Lunas'],
    [3, 'bukti3.jpg', 'Lunas']
];

foreach ($payments as $payment) {
    $stmt = $pdo->prepare('INSERT INTO pembayaran (id_pemesanan, bukti_pembayaran, status_pembayaran, tanggal_upload) VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE status_pembayaran = VALUES(status_pembayaran)');
    $stmt->execute($payment);
    echo 'Payment record created for pemesanan ' . $payment[0] . ' with status ' . $payment[2] . PHP_EOL;
}
echo 'Sample payment data created successfully!' . PHP_EOL;
?>
