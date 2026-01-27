<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Query untuk mengambil kategori dengan jumlah produk aktif
    $query = "SELECT c.id, c.nama_kategori, c.deskripsi, c.icon, 
                     COUNT(p.id) as product_count
              FROM categories c 
              LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
              GROUP BY c.id, c.nama_kategori, c.deskripsi, c.icon
              HAVING product_count > 0
              ORDER BY c.nama_kategori";
    
    $stmt = $db->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format response
    $response = [
        'success' => true,
        'message' => 'Kategori berhasil dimuat',
        'count' => count($categories),
        'categories' => $categories
    ];
    
    echo json_encode($response);
    
} catch(Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'categories' => []
    ];
    
    echo json_encode($response);
}
?>