<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Cek apakah ada parameter limit
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : null;
    
    // Query untuk mengambil produk yang aktif saja dengan kategori
    $query = "SELECT p.id, p.nama_produk, p.deskripsi_singkat, p.deskripsi, p.foto, p.created_at, 
                     p.category_id, c.nama_kategori, c.icon as category_icon
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.is_active = 1 
              ORDER BY p.created_at DESC";
    
    if ($limit && $limit > 0) {
        $query .= " LIMIT :limit";
    }
    
    $stmt = $db->prepare($query);
    
    if ($limit && $limit > 0) {
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format response
    $response = [
        'success' => true,
        'message' => 'Produk berhasil dimuat',
        'count' => count($products),
        'products' => $products
    ];
    
    echo json_encode($response);
    
} catch(Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'products' => []
    ];
    
    echo json_encode($response);
}
?>