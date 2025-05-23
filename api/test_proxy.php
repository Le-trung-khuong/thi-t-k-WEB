<?php
// This is a simple test file to check if PHP is working correctly
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'message' => 'PHP server is working correctly',
    'time' => date('Y-m-d H:i:s'),
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
]);
?> 