<?php
/**
 * Meta tags untuk cache control
 * Gunakan di development untuk mencegah caching
 */

// Untuk development - prevent caching
if ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false) {
    echo '
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    ';
}

// Untuk production - cache dengan versioning
else {
    echo '
    <meta http-equiv="Cache-Control" content="public, max-age=3600">
    ';
}
?>