<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Cek apakah tabel settings ada
    $table_check = $db->query("SHOW TABLES LIKE 'settings'");
    
    if ($table_check->rowCount() == 0) {
        // Tabel settings belum ada, gunakan default
        $default_settings = [
            'whatsapp_link' => 'https://wa.me/6281234567890',
            'whatsapp_channel' => 'https://whatsapp.com/channel/0029VaABC123',
            'site_name' => 'Toko Online',
            'site_email' => 'info@tokoonline.com',
            'site_phone' => '+62 812-3456-7890',
            'site_address' => 'Jl. Contoh No. 123, Kota Contoh, Provinsi'
        ];
        
        $response = [
            'success' => true,
            'message' => 'Menggunakan pengaturan default (tabel settings belum ada)',
            'settings' => $default_settings,
            'warning' => 'Jalankan setup_database.php atau add_settings_table.php untuk membuat tabel settings'
        ];
    } else {
        // Ambil semua pengaturan dari database
        $query = "SELECT setting_key, setting_value FROM settings";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $settings_raw = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Format response
        $response = [
            'success' => true,
            'message' => 'Pengaturan berhasil dimuat dari database',
            'settings' => $settings_raw
        ];
    }
    
    echo json_encode($response);
    
} catch(Exception $e) {
    // Fallback ke pengaturan default jika ada error
    $default_settings = [
        'whatsapp_link' => 'https://wa.me/6281234567890',
        'whatsapp_channel' => 'https://whatsapp.com/channel/0029VaABC123',
        'site_name' => 'Toko Online',
        'site_email' => 'info@tokoonline.com',
        'site_phone' => '+62 812-3456-7890',
        'site_address' => 'Jl. Contoh No. 123, Kota Contoh, Provinsi'
    ];
    
    $response = [
        'success' => false,
        'message' => 'Error database, menggunakan pengaturan default: ' . $e->getMessage(),
        'settings' => $default_settings,
        'error' => $e->getMessage()
    ];
    
    echo json_encode($response);
}
?>