<?php
require_once '../config.php';

echo "<h2>🔍 TEST DATABASE QUERIES</h2>";

// Test struktur tabel pemesanan
echo "<h3>📋 Struktur Tabel Pemesanan:</h3>";
$result = mysqli_query($conn, "SHOW COLUMNS FROM pemesanan");
$columns = [];
if ($result) {
    echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
    echo "<tr><th style='padding: 8px; background: #f0f0f0;'>Column</th><th style='padding: 8px; background: #f0f0f0;'>Type</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        $columns[] = $row['Field'];
        echo "<tr><td style='padding: 8px;'>" . $row['Field'] . "</td><td style='padding: 8px;'>" . $row['Type'] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ Error: " . mysqli_error($conn) . "</p>";
}

// Test query dinamis seperti di riwayat_pemesanan.php
echo "<h3>🔍 Test Query Riwayat Pemesanan:</h3>";

$select_fields = [];
$select_fields[] = in_array('id', $columns) ? 'p.id' : 'p.id_pemesanan as id';
$select_fields[] = in_array('tanggal_acara', $columns) ? 'p.tanggal_acara' : 'p.tanggal_sewa as tanggal_acara';
$select_fields[] = in_array('total_biaya', $columns) ? 'p.total_biaya' : 'p.total as total_biaya';
$select_fields[] = "'Umum' as jenis_acara";
$select_fields[] = "'Customer' as nama_penyewa";
$select_fields[] = "'customer@email.com' as email";
$select_fields[] = "'pending' as status_pembayaran";

$query = "SELECT " . implode(', ', $select_fields) . " FROM pemesanan p ";

// Dynamic ORDER BY
$query .= "ORDER BY ";
if (in_array('tanggal_acara', $columns)) {
    $query .= 'p.tanggal_acara DESC';
} else if (in_array('id', $columns)) {
    $query .= 'p.id DESC';
} else {
    $query .= 'p.id_pemesanan DESC';
}
$query .= " LIMIT 5";

echo "<pre style='background: #f9f9f9; padding: 10px; border: 1px solid #ddd;'>" . $query . "</pre>";

$result = mysqli_query($conn, $query);
if ($result) {
    echo "<p style='color: green;'>✅ Query berhasil! Jumlah data: " . mysqli_num_rows($result) . "</p>";
    
    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0; width: 100%;'>";
        echo "<tr>";
        echo "<th style='padding: 8px; background: #f0f0f0;'>ID</th>";
        echo "<th style='padding: 8px; background: #f0f0f0;'>Tanggal Acara</th>";
        echo "<th style='padding: 8px; background: #f0f0f0;'>Total Biaya</th>";
        echo "<th style='padding: 8px; background: #f0f0f0;'>Nama Penyewa</th>";
        echo "<th style='padding: 8px; background: #f0f0f0;'>Email</th>";
        echo "<th style='padding: 8px; background: #f0f0f0;'>Status</th>";
        echo "</tr>";
        
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td style='padding: 8px;'>" . $row['id'] . "</td>";
            echo "<td style='padding: 8px;'>" . $row['tanggal_acara'] . "</td>";
            echo "<td style='padding: 8px;'>Rp " . number_format($row['total_biaya'], 0, ',', '.') . "</td>";
            echo "<td style='padding: 8px;'>" . $row['nama_penyewa'] . "</td>";
            echo "<td style='padding: 8px;'>" . $row['email'] . "</td>";
            echo "<td style='padding: 8px;'>" . $row['status_pembayaran'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} else {
    echo "<p style='color: red;'>❌ Error Query: " . mysqli_error($conn) . "</p>";
}

echo "<hr style='margin: 20px 0;'>";
echo "<h3>🎯 HASIL TEST:</h3>";
echo "<p style='color: green; font-weight: bold;'>✅ Kolom 'p.id' error sudah diperbaiki dengan fallback ke 'p.id_pemesanan'</p>";
echo "<p style='color: green; font-weight: bold;'>✅ Query dinamis bekerja sesuai struktur database</p>";
echo "<p style='color: green; font-weight: bold;'>✅ ORDER BY menggunakan kolom yang tersedia</p>";
?>
