<?php
require_once 'config.php';
require_once 'includes/pricing.php';

// Test pricing system
$eventPricing = getEventPricing($pdo);

echo "Testing pricing system:\n";
echo "Pernikahan (ID: 1): " . $eventPricing[1]['formatted_price'] . "\n";
echo "Rapat/Meeting (ID: 2): " . $eventPricing[2]['formatted_price'] . "\n";
echo "Seminar (ID: 3): " . $eventPricing[3]['formatted_price'] . "\n";

// Test individual functions
echo "\nTesting individual functions:\n";
$pernikahan = getPriceByEventId($pdo, 1);
echo "Pernikahan by ID: " . $pernikahan['formatted_price'] . "\n";

$rapat = getPriceByEventName($pdo, 'Rapat/Meeting');
echo "Rapat by name: " . $rapat['formatted_price'] . "\n";
?>
