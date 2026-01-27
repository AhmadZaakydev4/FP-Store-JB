<?php
require_once __DIR__ . '/../config/database.php';

// Create database connection
$database = new Database();
$pdo = $database->getConnection();

if (!$pdo) {
    die("❌ Database connection failed!\n");
}

try {
    // Check if deskripsi_singkat column exists
    $checkColumn = $pdo->query("SHOW COLUMNS FROM products LIKE 'deskripsi_singkat'");
    
    if ($checkColumn->rowCount() == 0) {
        echo "Adding deskripsi_singkat column...\n";
        
        // Add the deskripsi_singkat column
        $pdo->exec("ALTER TABLE products ADD COLUMN deskripsi_singkat TEXT NOT NULL AFTER nama_produk");
        
        // Update existing products with short descriptions (first 150 characters)
        $pdo->exec("UPDATE products SET deskripsi_singkat = LEFT(REGEXP_REPLACE(deskripsi, '<[^>]*>', ''), 150)");
        
        echo "✓ deskripsi_singkat column added successfully!\n";
    } else {
        echo "✓ deskripsi_singkat column already exists.\n";
    }
    
    // Check if is_active column exists
    $checkActive = $pdo->query("SHOW COLUMNS FROM products LIKE 'is_active'");
    
    if ($checkActive->rowCount() == 0) {
        echo "Adding is_active column...\n";
        
        // Add the is_active column
        $pdo->exec("ALTER TABLE products ADD COLUMN is_active TINYINT(1) DEFAULT 1 AFTER foto");
        
        echo "✓ is_active column added successfully!\n";
    } else {
        echo "✓ is_active column already exists.\n";
    }
    
    // Clean up any problematic base64 image data in descriptions
    echo "Cleaning up base64 image data...\n";
    
    $stmt = $pdo->prepare("SELECT id, deskripsi FROM products WHERE deskripsi LIKE '%data:image%'");
    $stmt->execute();
    $products = $stmt->fetchAll();
    
    foreach ($products as $product) {
        // Remove base64 images and clean HTML
        $cleanDescription = preg_replace('/<img[^>]*data:image[^>]*>/i', '', $product['deskripsi']);
        $cleanDescription = preg_replace('/<p><br><\/p>/', '', $cleanDescription);
        $cleanDescription = trim($cleanDescription);
        
        if (empty($cleanDescription)) {
            $cleanDescription = 'Deskripsi produk akan segera diperbarui.';
        }
        
        // Update the product
        $updateStmt = $pdo->prepare("UPDATE products SET deskripsi = ?, deskripsi_singkat = ? WHERE id = ?");
        $shortDesc = strip_tags($cleanDescription);
        $shortDesc = substr($shortDesc, 0, 150);
        $updateStmt->execute([$cleanDescription, $shortDesc, $product['id']]);
    }
    
    echo "✓ Cleaned " . count($products) . " products with base64 image data.\n";
    echo "\n🎉 Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
}
?>