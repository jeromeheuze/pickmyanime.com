<?php
// Disable caching
header('Expires: Tue, 01 Jan 2000 00:00:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Content-Type: application/json');

// File: track-click.php
$logfile = __DIR__ . '/clicks.log';

// Ensure file exists
if (!file_exists($logfile)) {
    file_put_contents($logfile, "0");
}

// Read current count
$count = (int) file_get_contents($logfile);

// Increment
$count++;

// Save new count
file_put_contents($logfile, $count);

// Return JSON response
header('Content-Type: application/json');
echo json_encode(['success' => true, 'count' => $count]);
