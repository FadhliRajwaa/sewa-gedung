<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once '../../config.php';

try {
    // Get recent activity from different tables
    $activities = [];
    
    // Recent bookings
    $stmt = $pdo->query("
        SELECT 
            'booking' as type,
            CONCAT('Pemesanan baru: ', a.nama_acara, ' oleh ', 
                CASE WHEN pen.tipe_penyewa = 'instansi' THEN pen.nama_instansi ELSE pen.nama_lengkap END
            ) as description,
            p.tanggal_pesan as created_at
        FROM pemesanan p
        LEFT JOIN acara a ON p.id_acara = a.id_acara
        LEFT JOIN penyewa pen ON p.id_penyewa = pen.id_penyewa
        ORDER BY p.tanggal_pesan DESC
        LIMIT 3
    ");
    $bookingActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $activities = array_merge($activities, $bookingActivities);
    
    // Recent payments
    $stmt = $pdo->query("
        SELECT 
            'payment' as type,
            CONCAT('Pembayaran ', pb.status_pembayaran, ' untuk pemesanan ID: ', pb.id_pemesanan) as description,
            pb.tanggal_upload as created_at
        FROM pembayaran pb
        WHERE pb.tanggal_upload IS NOT NULL
        ORDER BY pb.tanggal_upload DESC
        LIMIT 3
    ");
    $paymentActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $activities = array_merge($activities, $paymentActivities);
    
    // Recent registrations
    $stmt = $pdo->query("
        SELECT 
            'registration' as type,
            CONCAT('Penyewa baru: ', 
                CASE WHEN tipe_penyewa = 'instansi' THEN nama_instansi ELSE nama_lengkap END
            ) as description,
            created_at
        FROM penyewa
        WHERE created_at IS NOT NULL
        ORDER BY created_at DESC
        LIMIT 2
    ");
    $registrationActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $activities = array_merge($activities, $registrationActivities);
    
    // Sort by created_at desc
    usort($activities, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });
    
    // Take only latest 8 activities
    $activities = array_slice($activities, 0, 8);
    
    echo json_encode($activities);
    
} catch (Exception $e) {
    error_log("Error in get_recent_activity.php: " . $e->getMessage());
    echo json_encode(['error' => 'Internal server error']);
}
?>
