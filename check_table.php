<?php
require_once 'config.php';

try {
    $stmt = $pdo->query('DESCRIBE pemesanan');
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Struktur tabel pemesanan:\n";
    foreach($columns as $column) {
        echo $column['Field'] . ' - ' . $column['Type'] . "\n";
    }
} catch(Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
?>
