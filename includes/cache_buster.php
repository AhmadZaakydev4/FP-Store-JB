<?php
/**
 * Cache Buster Helper
 * Generates version parameter based on file modification time
 */

function getCacheBuster($filePath) {
    $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim($filePath, '/');
    
    if (file_exists($fullPath)) {
        return '?v=' . filemtime($fullPath);
    }
    
    // Fallback to current timestamp if file not found
    return '?v=' . time();
}

function asset($filePath) {
    return $filePath . getCacheBuster($filePath);
}
?>