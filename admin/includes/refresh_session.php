<?php
header('Content-Type: application/json');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode([
        'success' => false,
        'message' => 'Session not found'
    ]);
    exit();
}

// Refresh session activity time
$_SESSION['last_activity'] = time();

echo json_encode([
    'success' => true,
    'message' => 'Session refreshed successfully',
    'timestamp' => time()
]);
?>