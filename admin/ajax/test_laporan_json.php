<?php
session_start();
$_SESSION['admin_logged_in'] = true;

// Test get_laporan.php
echo "Testing get_laporan.php:\n";
ob_start();
include 'get_laporan.php';
$output = ob_get_clean();

// Remove headers dari output
$lines = explode("\n", $output);
$jsonStart = false;
$jsonOutput = '';

foreach ($lines as $line) {
    if (strpos($line, '[{') !== false || $jsonStart) {
        $jsonStart = true;
        $jsonOutput .= $line . "\n";
    }
}

echo "JSON Output Length: " . strlen(trim($jsonOutput)) . "\n";
echo "First 100 chars: " . substr(trim($jsonOutput), 0, 100) . "\n";
echo "Last 100 chars: " . substr(trim($jsonOutput), -100) . "\n";

// Test JSON validity
$decoded = json_decode(trim($jsonOutput));
if (json_last_error() === JSON_ERROR_NONE) {
    echo "JSON is valid! Records count: " . count($decoded) . "\n";
} else {
    echo "JSON Error: " . json_last_error_msg() . "\n";
    echo "Full output:\n" . $output . "\n";
}
?>
