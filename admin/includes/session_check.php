<?php
// Mulai session jika belum dimulai
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Set timeout 1 jam (3600 detik)
$session_timeout = 3600;

// Cek apakah user sudah login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Cek apakah session sudah expired
if (isset($_SESSION['last_activity'])) {
    $inactive_time = time() - $_SESSION['last_activity'];
    
    if ($inactive_time > $session_timeout) {
        // Session expired, destroy session dan redirect ke login
        session_unset();
        session_destroy();
        
        // Set pesan untuk ditampilkan di login page
        session_start();
        $_SESSION['session_expired'] = true;
        
        header('Location: login.php');
        exit();
    }
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Set session start time jika belum ada
if (!isset($_SESSION['session_start'])) {
    $_SESSION['session_start'] = time();
}

// Hitung sisa waktu session
$remaining_time = $session_timeout - (time() - $_SESSION['last_activity']);
?>