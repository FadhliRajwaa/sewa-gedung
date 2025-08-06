<?php
require_once 'config.php';
require_once 'includes/pricing.php';

$eventPricing = getEventPricing($pdo);
echo "Pernikahan: " . $eventPricing[1]['formatted_price'] . "\n";
echo "Rapat/Meeting: " . $eventPricing[2]['formatted_price'] . "\n";
echo "Seminar: " . $eventPricing[3]['formatted_price'] . "\n";
?>
