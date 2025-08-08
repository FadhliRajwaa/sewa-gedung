<?php
// Script untuk menjalankan perbaikan relasi database
require_once 'config.php';

echo "=== MEMPERBAIKI RELASI DATABASE ===\n\n";

try {
    // 1. Tambah kolom id_pembayaran di tabel pemesanan
    echo "1. Menambahkan kolom id_pembayaran di tabel pemesanan...\n";
    $sql1 = "ALTER TABLE pemesanan ADD COLUMN id_pembayaran INT(11) NULL";
    
    if (mysqli_query($conn, $sql1)) {
        echo "✅ Kolom id_pembayaran berhasil ditambahkan\n";
    } else {
        echo "⚠️ Kolom id_pembayaran mungkin sudah ada: " . mysqli_error($conn) . "\n";
    }

    // 2. Tambah foreign key constraint untuk pembayaran
    echo "\n2. Menambahkan foreign key constraint pembayaran...\n";
    $sql2 = "ALTER TABLE pemesanan 
             ADD CONSTRAINT fk_pemesanan_pembayaran 
             FOREIGN KEY (id_pembayaran) REFERENCES pembayaran(id_pembayaran) 
             ON DELETE SET NULL ON UPDATE CASCADE";
    
    if (mysqli_query($conn, $sql2)) {
        echo "✅ Foreign key pembayaran berhasil ditambahkan\n";
    } else {
        echo "⚠️ Foreign key pembayaran mungkin sudah ada: " . mysqli_error($conn) . "\n";
    }

    // 3. Tambah kolom id_verifikasi di tabel penyewa
    echo "\n3. Menambahkan kolom id_verifikasi di tabel penyewa...\n";
    $sql3 = "ALTER TABLE penyewa ADD COLUMN id_verifikasi INT(11) NULL";
    
    if (mysqli_query($conn, $sql3)) {
        echo "✅ Kolom id_verifikasi berhasil ditambahkan\n";
    } else {
        echo "⚠️ Kolom id_verifikasi mungkin sudah ada: " . mysqli_error($conn) . "\n";
    }

    // 4. Tambah foreign key constraint untuk verifikasi
    echo "\n4. Menambahkan foreign key constraint verifikasi...\n";
    $sql4 = "ALTER TABLE penyewa 
             ADD CONSTRAINT fk_penyewa_verifikasi 
             FOREIGN KEY (id_verifikasi) REFERENCES verifikasi_email(id_verifikasi) 
             ON DELETE SET NULL ON UPDATE CASCADE";
    
    if (mysqli_query($conn, $sql4)) {
        echo "✅ Foreign key verifikasi berhasil ditambahkan\n";
    } else {
        echo "⚠️ Foreign key verifikasi mungkin sudah ada: " . mysqli_error($conn) . "\n";
    }

    // 5. Update data existing - sinkronisasi pembayaran
    echo "\n5. Mensinkronisasi data pembayaran existing...\n";
    $sql5 = "UPDATE pemesanan p 
             SET id_pembayaran = (
                 SELECT pb.id_pembayaran 
                 FROM pembayaran pb 
                 WHERE pb.id_pemesanan = p.id_pemesanan 
                 LIMIT 1
             ) 
             WHERE EXISTS (
                 SELECT 1 FROM pembayaran pb2 
                 WHERE pb2.id_pemesanan = p.id_pemesanan
             )";
    
    if (mysqli_query($conn, $sql5)) {
        $affected = mysqli_affected_rows($conn);
        echo "✅ $affected record pemesanan berhasil disinkronisasi dengan pembayaran\n";
    } else {
        echo "❌ Error sinkronisasi pembayaran: " . mysqli_error($conn) . "\n";
    }

    // 6. Update data existing - sinkronisasi verifikasi
    echo "\n6. Mensinkronisasi data verifikasi existing...\n";
    $sql6 = "UPDATE penyewa py 
             SET id_verifikasi = (
                 SELECT v.id_verifikasi 
                 FROM verifikasi_email v 
                 WHERE v.id_penyewa = py.id_penyewa 
                 LIMIT 1
             ) 
             WHERE EXISTS (
                 SELECT 1 FROM verifikasi_email v2 
                 WHERE v2.id_penyewa = py.id_penyewa
             )";
    
    if (mysqli_query($conn, $sql6)) {
        $affected = mysqli_affected_rows($conn);
        echo "✅ $affected record penyewa berhasil disinkronisasi dengan verifikasi\n";
    } else {
        echo "❌ Error sinkronisasi verifikasi: " . mysqli_error($conn) . "\n";
    }

    // 7. Tambah index untuk performa
    echo "\n7. Menambahkan index untuk performa...\n";
    
    $index1 = "CREATE INDEX idx_pemesanan_pembayaran ON pemesanan(id_pembayaran)";
    if (mysqli_query($conn, $index1)) {
        echo "✅ Index idx_pemesanan_pembayaran berhasil ditambahkan\n";
    } else {
        echo "⚠️ Index mungkin sudah ada: " . mysqli_error($conn) . "\n";
    }
    
    $index2 = "CREATE INDEX idx_penyewa_verifikasi ON penyewa(id_verifikasi)";
    if (mysqli_query($conn, $index2)) {
        echo "✅ Index idx_penyewa_verifikasi berhasil ditambahkan\n";
    } else {
        echo "⚠️ Index mungkin sudah ada: " . mysqli_error($conn) . "\n";
    }

    echo "\n=== PERBAIKAN SELESAI ===\n";
    echo "\n📋 RELASI SETELAH PERBAIKAN:\n";
    echo "✅ penyewa.id_penyewa -> FK di pemesanan, verifikasi_email\n";
    echo "✅ acara.id_acara -> FK di pemesanan\n";
    echo "✅ pemesanan.id_pemesanan -> FK di pembayaran\n";
    echo "✅ pembayaran.id_pembayaran -> FK di pemesanan (BARU)\n";
    echo "✅ verifikasi_email.id_verifikasi -> FK di penyewa (BARU)\n";
    echo "✅ admin.id_admin -> standalone (tidak perlu relasi)\n";

    // 8. Tampilkan struktur relasi saat ini
    echo "\n📊 STRUKTUR TABEL SETELAH PERBAIKAN:\n";
    
    // Cek kolom pemesanan
    $check_pemesanan = mysqli_query($conn, "SHOW COLUMNS FROM pemesanan");
    echo "\n🔸 TABEL PEMESANAN:\n";
    while ($col = mysqli_fetch_assoc($check_pemesanan)) {
        $key_info = $col['Key'] ? " ({$col['Key']})" : "";
        echo "  - {$col['Field']}: {$col['Type']}{$key_info}\n";
    }
    
    // Cek kolom penyewa
    $check_penyewa = mysqli_query($conn, "SHOW COLUMNS FROM penyewa");
    echo "\n🔸 TABEL PENYEWA:\n";
    while ($col = mysqli_fetch_assoc($check_penyewa)) {
        $key_info = $col['Key'] ? " ({$col['Key']})" : "";
        echo "  - {$col['Field']}: {$col['Type']}{$key_info}\n";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

mysqli_close($conn);
?>
