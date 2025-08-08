<?php
require_once '../config.php';

// Check database structure
echo "<h2>Struktur Tabel Pemesanan:</h2>";
$query = "DESCRIBE pemesanan";
$result = mysqli_query($conn, $query);

if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        foreach ($row as $value) {
            echo "<td>" . htmlspecialchars($value) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . mysqli_error($conn);
}

// Check if kebutuhan_tambahan column exists and has data
echo "<h2>Data Kebutuhan Tambahan yang Ada:</h2>";
$query = "SELECT id_pemesanan, kebutuhan_tambahan FROM pemesanan WHERE kebutuhan_tambahan IS NOT NULL AND kebutuhan_tambahan != ''";
$result = mysqli_query($conn, $query);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1'>";
        echo "<tr><th>ID Pemesanan</th><th>Kebutuhan Tambahan</th></tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['id_pemesanan'] . "</td>";
            echo "<td>" . htmlspecialchars($row['kebutuhan_tambahan']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Tidak ada data kebutuhan tambahan yang terisi.";
    }
} else {
    echo "Error: " . mysqli_error($conn);
}

// Test sample data
echo "<h2>Semua Data Pemesanan (5 terakhir):</h2>";
$query = "SELECT id_pemesanan, id_penyewa, kebutuhan_tambahan, tanggal_pesan FROM pemesanan ORDER BY id_pemesanan DESC LIMIT 5";
$result = mysqli_query($conn, $query);

if ($result) {
    echo "<table border='1'>";
    echo "<tr><th>ID Pemesanan</th><th>ID Penyewa</th><th>Kebutuhan Tambahan</th><th>Tanggal Pesan</th></tr>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>";
        echo "<td>" . $row['id_pemesanan'] . "</td>";
        echo "<td>" . $row['id_penyewa'] . "</td>";
        echo "<td>" . htmlspecialchars($row['kebutuhan_tambahan'] ?: '(kosong)') . "</td>";
        echo "<td>" . $row['tanggal_pesan'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
