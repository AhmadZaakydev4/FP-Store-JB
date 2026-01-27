<?php
// Script untuk assign kategori ke produk yang sudah ada
require_once 'config/database.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    echo "<h2>🏷️ Assign Kategori ke Produk</h2>";
    
    // Cek kategori yang tersedia
    $query = "SELECT * FROM categories ORDER BY id";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($categories)) {
        echo "<p style='color: red;'>❌ Tidak ada kategori. Jalankan setup database dulu.</p>";
        exit;
    }
    
    echo "<h3>📂 Kategori yang tersedia:</h3>";
    foreach ($categories as $cat) {
        echo "<p>• ID: {$cat['id']} - {$cat['nama_kategori']} ({$cat['icon']})</p>";
    }
    
    // Ambil produk yang belum punya kategori
    $query = "SELECT * FROM products WHERE category_id IS NULL OR category_id = 0 ORDER BY nama_produk";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($products)) {
        echo "<p style='color: green;'>✅ Semua produk sudah memiliki kategori!</p>";
        
        // Tampilkan produk dengan kategori
        $query = "SELECT p.nama_produk, c.nama_kategori 
                  FROM products p 
                  LEFT JOIN categories c ON p.category_id = c.id 
                  WHERE p.is_active = 1
                  ORDER BY c.nama_kategori, p.nama_produk";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $assigned = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>📦 Produk dengan kategori:</h3>";
        foreach ($assigned as $item) {
            echo "<p>• {$item['nama_produk']} → {$item['nama_kategori']}</p>";
        }
    } else {
        echo "<h3>📦 Produk tanpa kategori (" . count($products) . "):</h3>";
        
        // Auto-assign berdasarkan nama produk
        $assignments = [
            // Akun Game (ID: 1)
            1 => ['mobile legends', 'ml', 'pubg', 'free fire', 'ff', 'valorant', 'genshin', 'game'],
            // Aplikasi Premium (ID: 2) 
            2 => ['netflix', 'spotify', 'youtube', 'adobe', 'canva', 'premium', 'pro'],
            // Akun Media Sosial (ID: 3)
            3 => ['instagram', 'tiktok', 'facebook', 'twitter', 'sosial', 'media'],
            // Tools & Software (ID: 4)
            4 => ['office', 'windows', 'antivirus', 'vpn', 'tools', 'software'],
            // Lainnya (ID: 5) - default
            5 => []
        ];
        
        $updated = 0;
        
        foreach ($products as $product) {
            $productName = strtolower($product['nama_produk']);
            $assignedCategory = 5; // Default ke "Lainnya"
            
            // Cari kategori yang cocok
            foreach ($assignments as $categoryId => $keywords) {
                if ($categoryId == 5) continue; // Skip default category
                
                foreach ($keywords as $keyword) {
                    if (strpos($productName, $keyword) !== false) {
                        $assignedCategory = $categoryId;
                        break 2; // Break dari kedua loop
                    }
                }
            }
            
            // Update produk
            $updateQuery = "UPDATE products SET category_id = :category_id WHERE id = :id";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->bindParam(':category_id', $assignedCategory);
            $updateStmt->bindParam(':id', $product['id']);
            
            if ($updateStmt->execute()) {
                $categoryName = '';
                foreach ($categories as $cat) {
                    if ($cat['id'] == $assignedCategory) {
                        $categoryName = $cat['nama_kategori'];
                        break;
                    }
                }
                echo "<p>✅ {$product['nama_produk']} → {$categoryName}</p>";
                $updated++;
            } else {
                echo "<p>❌ Gagal update {$product['nama_produk']}</p>";
            }
        }
        
        echo "<hr>";
        echo "<p><strong>Total produk yang diupdate: {$updated}</strong></p>";
    }
    
    // Tampilkan statistik akhir
    echo "<h3>📊 Statistik Kategori:</h3>";
    $query = "SELECT c.nama_kategori, COUNT(p.id) as jumlah_produk
              FROM categories c
              LEFT JOIN products p ON c.id = p.category_id AND p.is_active = 1
              GROUP BY c.id, c.nama_kategori
              ORDER BY jumlah_produk DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($stats as $stat) {
        echo "<p>• {$stat['nama_kategori']}: {$stat['jumlah_produk']} produk</p>";
    }
    
} catch(Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Assign Kategori</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
    </style>
</head>
<body>
    <a href="debug_categories.php" style="background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">🔍 Debug Kategori</a>
    <a href="produk.html" style="background: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-left: 10px;">📦 Test Filter</a>
</body>
</html>