<?php
// Script untuk test produk di database
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Ambil semua produk
    $query = "SELECT * FROM products ORDER BY created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>🛍️ Test Produk Database</h2>";
    echo "<p><strong>Total produk:</strong> " . count($products) . "</p>";
    
    if (count($products) > 0) {
        echo "<table border='1' style='width: 100%; border-collapse: collapse;'>";
        echo "<tr style='background: #f8f9fa;'>";
        echo "<th>ID</th><th>Nama Produk</th><th>Deskripsi</th><th>Status</th><th>Foto</th><th>Tanggal</th>";
        echo "</tr>";
        
        foreach ($products as $product) {
            $status = isset($product['is_active']) ? ($product['is_active'] ? '✅ Aktif' : '❌ Nonaktif') : '⚠️ Unknown';
            $statusColor = isset($product['is_active']) ? ($product['is_active'] ? 'green' : 'red') : 'orange';
            
            echo "<tr>";
            echo "<td>" . $product['id'] . "</td>";
            echo "<td><strong>" . htmlspecialchars($product['nama_produk']) . "</strong></td>";
            echo "<td>" . htmlspecialchars(substr($product['deskripsi'], 0, 50)) . "...</td>";
            echo "<td style='color: {$statusColor}; font-weight: bold;'>" . $status . "</td>";
            echo "<td>" . htmlspecialchars($product['foto']) . "</td>";
            echo "<td>" . $product['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ Tidak ada produk di database</p>";
    }
    
    // Test API
    echo "<hr>";
    echo "<h3>🔗 Test API Response</h3>";
    
    $api_url = 'http://localhost/Jb%20website/api/get_products.php';
    $api_response = @file_get_contents($api_url);
    
    if ($api_response) {
        $api_data = json_decode($api_response, true);
        echo "<p><strong>API Status:</strong> " . ($api_data['success'] ? '✅ Success' : '❌ Failed') . "</p>";
        echo "<p><strong>API Message:</strong> " . $api_data['message'] . "</p>";
        echo "<p><strong>API Count:</strong> " . $api_data['count'] . "</p>";
        
        echo "<h4>API Response:</h4>";
        echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto;'>";
        echo htmlspecialchars(json_encode($api_data, JSON_PRETTY_PRINT));
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>❌ API tidak dapat diakses</p>";
    }
    
} catch(Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Test Produk Database</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { margin: 20px 0; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <a href="index.html" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">← Kembali ke Website</a>
</body>
</html>