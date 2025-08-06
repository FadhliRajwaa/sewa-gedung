<?php
// Configuration file for event pricing
// This ensures consistent pricing across all pages

// Get event pricing from database
function getEventPricing($pdo) {
    try {
        $query = 'SELECT id_acara, nama_acara, harga FROM acara ORDER BY id_acara';
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $pricing = [];
        foreach ($events as $event) {
            $pricing[$event['id_acara']] = [
                'name' => $event['nama_acara'],
                'price' => $event['harga'],
                'formatted_price' => 'Rp ' . number_format($event['harga'], 0, ',', '.')
            ];
        }
        return $pricing;
    } catch (PDOException $e) {
        // Fallback pricing if database fails
        return [
            1 => ['name' => 'Pernikahan', 'price' => 6150000, 'formatted_price' => 'Rp 6.150.000'],
            2 => ['name' => 'Rapat/Meeting', 'price' => 3885000, 'formatted_price' => 'Rp 3.885.000'],
            3 => ['name' => 'Seminar', 'price' => 4350000, 'formatted_price' => 'Rp 4.350.000']
        ];
    }
}

// Get pricing by event ID
function getPriceByEventId($pdo, $event_id) {
    $pricing = getEventPricing($pdo);
    return isset($pricing[$event_id]) ? $pricing[$event_id] : null;
}

// Get pricing by event name (for compatibility)
function getPriceByEventName($pdo, $event_name) {
    $pricing = getEventPricing($pdo);
    foreach ($pricing as $event) {
        if (strtolower($event['name']) === strtolower($event_name)) {
            return $event;
        }
    }
    return null;
}
?>
