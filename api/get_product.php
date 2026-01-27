<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Get product ID from URL parameter
    $product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($product_id <= 0) {
        throw new Exception('Invalid product ID');
    }
    
    // Query untuk mengambil produk spesifik dengan kategori
    $query = "SELECT p.id, p.nama_produk, p.deskripsi_singkat, p.deskripsi, p.foto, p.created_at, p.is_active,
                     c.nama_kategori, c.icon as category_icon, c.id as category_id
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              WHERE p.id = :product_id AND p.is_active = 1";
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
    $stmt->execute();
    
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        throw new Exception('Product not found or inactive');
    }
    
    // Format response
    $response = [
        'success' => true,
        'message' => 'Produk berhasil dimuat',
        'product' => $product
    ];
    
    echo json_encode($response);
    
} catch(Exception $e) {
    $response = [
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'product' => null
    ];
    
    http_response_code(404);
    echo json_encode($response);
}
?>