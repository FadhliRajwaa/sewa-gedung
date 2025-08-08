<?php
// Konfigurasi Database untuk Hosting
// Ganti nilai-nilai berikut sesuai dengan detail hosting Anda

// Database Configuration untuk Hosting
$host = 'sql205.byethost7.com'; // Ganti dengan hostname database hosting Anda
$dbname = 'b7_39639306_gedung'; // Nama database di hosting
$username = 'b7_39639306'; // Username database hosting
$password = 'password_database_anda'; // Password database hosting

// Timezone
date_default_timezone_set('Asia/Jakarta');

try {
    // PDO Connection untuk Hosting
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4", 
        $username, 
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    );
} catch (PDOException $e) {
    // Error handling yang lebih baik untuk production
    error_log("Database connection failed: " . $e->getMessage());
    die("Koneksi database gagal. Silakan coba lagi nanti.");
}

// Session configuration
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Base URL untuk hosting
$base_url = 'https://yourdomain.byethost7.com/'; // Ganti dengan URL hosting Anda

// Email configuration (opsional, untuk verifikasi email)
$email_config = [
    'smtp_host' => 'smtp.gmail.com',
    'smtp_port' => 587,
    'smtp_username' => 'your-email@gmail.com',
    'smtp_password' => 'your-app-password',
    'from_email' => 'your-email@gmail.com',
    'from_name' => 'PT. Aneka Usaha'
];

// Upload directory
$upload_dir = __DIR__ . '/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Helper function untuk debugging (hanya di development)
function debug_log($message, $data = null) {
    if (isset($_GET['debug']) && $_GET['debug'] == '1') {
        echo "<pre>DEBUG: $message\n";
        if ($data) {
            print_r($data);
        }
        echo "</pre>";
    }
}

// Function untuk format mata uang
function format_currency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

// Function untuk format tanggal Indonesia
function format_date($date) {
    $months = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
    ];
    
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = $months[(int)date('n', $timestamp)];
    $year = date('Y', $timestamp);
    
    return "$day $month $year";
}

// Security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');

// CSRF Token function
function generate_csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
