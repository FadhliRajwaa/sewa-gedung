<?php
require_once 'config.php';

echo "=== STRUKTUR TABEL PEMBAYARAN ===\n";
$result = mysqli_query($conn, 'DESCRIBE pembayaran');
while($row = mysqli_fetch_assoc($result)) {
    echo $row['Field'] . " - " . $row['Type'] . "\n";
}

echo "\n=== DATA SAMPLE PEMBAYARAN ===\n";
$result2 = mysqli_query($conn, 'SELECT * FROM pembayaran LIMIT 1');
if($row2 = mysqli_fetch_assoc($result2)) {
    foreach($row2 as $key => $value) {
        echo "$key: $value\n";
    }
} else {
    echo "Tidak ada data pembayaran\n";
}

mysqli_close($conn);
?>
