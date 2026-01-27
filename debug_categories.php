<?php
// Debug script untuk memeriksa kategori dan produk
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>🔍 Debug Kategori dan Produk</h2>";
    
    // Cek kategori
    echo "<h3>📂 Kategori yang tersedia:</h3>";
    $query = "SELECT * FROM categories ORDER BY nama_kategori";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($categories) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Nama Kategori</th><th>Icon</th><th>Deskripsi</th></tr>";
        foreach ($categories as $cat) {
            echo "<tr>";
            echo "<td>" . $cat['id'] . "</td>";
            echo "<td>" . htmlspecialchars($cat['nama_kategori']) . "</td>";
            echo "<td>" . htmlspecialchars($cat['icon']) . "</td>";
            echo "<td>" . htmlspecialchars($cat['deskripsi']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ Tidak ada kategori</p>";
    }
    
    // Cek produk dengan kategori
    echo "<h3>📦 Produk dengan kategori:</h3>";
    $query = "SELECT p.id, p.nama_produk, p.category_id, p.is_active, c.nama_kategori 
              FROM products p 
              LEFT JOIN categories c ON p.category_id = c.id 
              ORDER BY p.created_at DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($products) > 0) {
        echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #f8f9fa;'><th>ID</th><th>Nama Produk</th><th>Category ID</th><th>Nama Kategori</th><th>Status</th></tr>";
        foreach ($products as $prod) {
            $status = $prod['is_active'] ? '✅ Aktif' : '❌ Nonaktif';
            $statusColor = $prod['is_active'] ? 'green' : 'red';
            echo "<tr>";
            echo "<td>" . $prod['id'] . "</td>";
            echo "<td>" . htmlspecialchars($prod['nama_produk']) . "</td>";
            echo "<td>" . ($prod['category_id'] ?: '❌ NULL') . "</td>";
            echo "<td>" . ($prod['nama_kategori'] ?: '❌ Tidak ada kategori') . "</td>";
            echo "<td style='color: {$statusColor};'>" . $status . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>❌ Tidak ada produk</p>";
    }
    
    // Test API get_products
    echo "<h3>🔗 Test API get_products.php:</h3>";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost' . dirname($_SERVER['REQUEST_URI']) . '/api/get_products.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($response && $httpCode == 200) {
        $data = json_decode($response, true);
        echo "<p><strong>Status:</strong> " . ($data['success'] ? '✅ Success' : '❌ Failed') . "</p>";
        echo "<p><strong>Count:</strong> " . $data['count'] . "</p>";
        echo "<h4>Sample Product Data:</h4>";
        if (!empty($data['products'])) {
            $sample = $data['products'][0];
            echo "<pre style='background: #f8f9fa; padding: 10px; border-radius: 5px;'>";
            echo "ID: " . $sample['id'] . "\n";
            echo "Nama: " . $sample['nama_produk'] . "\n";
            echo "Category ID: " . ($sample['category_id'] ?? 'MISSING') . "\n";
            echo "Nama Kategori: " . ($sample['nama_kategori'] ?? 'MISSING') . "\n";
            echo "Icon: " . ($sample['category_icon'] ?? 'MISSING') . "\n";
            echo "</pre>";
        }
    } else {
        echo "<p style='color: red;'>❌ API Error: HTTP " . $httpCode . "</p>";
    }
    
    // Test API get_categories
    echo "<h3>🔗 Test API get_categories.php:</h3>";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'http://localhost' . dirname($_SERVER['REQUEST_URI']) . '/api/get_categories.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($response && $httpCode == 200) {
        $data = json_decode($response, true);
        echo "<p><strong>Status:</strong> " . ($data['success'] ? '✅ Success' : '❌ Failed') . "</p>";
        echo "<p><strong>Count:</strong> " . $data['count'] . "</p>";
        if (!empty($data['categories'])) {
            echo "<h4>Categories:</h4>";
            foreach ($data['categories'] as $cat) {
                echo "<p>• ID: {$cat['id']}, Name: {$cat['nama_kategori']}, Products: {$cat['product_count']}</p>";
            }
        }
    } else {
        echo "<p style='color: red;'>❌ Categories API Error: HTTP " . $httpCode . "</p>";
    }
    
} catch(Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Debug Kategori</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { margin: 20px 0; }
        th, td { padding: 8px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f8f9fa; }
    </style>
</head>
<body>
    <a href="produk.html" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">← Test Filter di Produk</a>
</body>
</html>