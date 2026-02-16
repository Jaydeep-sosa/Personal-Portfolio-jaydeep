<?php
// visitor-count.php - Live visitor counter with file-based storage

// File to store visitor count
$counterFile = 'visitor_count.txt';

// Initialize counter file if it doesn't exist
if (!file_exists($counterFile)) {
    file_put_contents($counterFile, '0');
}

// Read current count
$count = (int)file_get_contents($counterFile);

// Increment count
$count++;

// Save new count
file_put_contents($counterFile, $count);

// Return JSON response
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'count' => $count
]);
?>
