<?php
require_once __DIR__ . '/../config/database.php';

// Create database connection
$database = new Database();
$pdo = $database->getConnection();

if (!$pdo) {
    die("❌ Database connection failed!\n");
}

try {
    echo "🚀 Starting category migration...\n\n";
    
    // Create categories table
    echo "1. Creating categories table...\n";
    $createCategoriesTable = "
        CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nama_kategori VARCHAR(100) NOT NULL,
            deskripsi TEXT,
            icon VARCHAR(50) DEFAULT 'fas fa-folder',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )
    ";
    $pdo->exec($createCategoriesTable);
    echo "✅ Categories table created successfully!\n\n";
    
    // Check if category_id column exists in products table
    echo "2. Checking products table structure...\n";
    $checkColumn = $pdo->query("SHOW COLUMNS FROM products LIKE 'category_id'");
    
    if ($checkColumn->rowCount() == 0) {
        echo "Adding category_id column to products table...\n";
        $pdo->exec("ALTER TABLE products ADD COLUMN category_id INT AFTER foto");
        $pdo->exec("ALTER TABLE products ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL");
        echo "✅ category_id column added successfully!\n\n";
    } else {
        echo "✅ category_id column already exists.\n\n";
    }
    
    // Insert default categories
    echo "3. Inserting default categories...\n";
    $checkCategories = $pdo->query("SELECT COUNT(*) FROM categories");
    $categoryCount = $checkCategories->fetchColumn();
    
    if ($categoryCount == 0) {
        $categories = [
            ['Akun Game', 'Akun game premium dengan level tinggi dan item lengkap', 'fas fa-gamepad'],
            ['Aplikasi Premium', 'Aplikasi berbayar dan premium untuk Android/iOS', 'fas fa-mobile-alt'],
            ['Akun Sosial Media', 'Akun media sosial dengan follower tinggi', 'fas fa-share-alt'],
            ['Software & Tools', 'Software premium dan tools untuk produktivitas', 'fas fa-laptop-code'],
            ['Streaming & Entertainment', 'Akun streaming premium seperti Netflix, Spotify', 'fas fa-play-circle']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO categories (nama_kategori, deskripsi, icon) VALUES (?, ?, ?)");
        
        foreach ($categories as $category) {
            $stmt->execute($category);
            echo "  ✓ Added category: {$category[0]}\n";
        }
        echo "✅ Default categories inserted successfully!\n\n";
    } else {
        echo "✅ Categories already exist ($categoryCount categories found).\n\n";
    }
    
    // Update existing products with random categories (for demo)
    echo "4. Updating existing products with categories...\n";
    $products = $pdo->query("SELECT id, nama_produk FROM products WHERE category_id IS NULL")->fetchAll();
    
    if (count($products) > 0) {
        $categories = $pdo->query("SELECT id FROM categories")->fetchAll(PDO::FETCH_COLUMN);
        $updateStmt = $pdo->prepare("UPDATE products SET category_id = ? WHERE id = ?");
        
        foreach ($products as $product) {
            $randomCategoryId = $categories[array_rand($categories)];
            $updateStmt->execute([$randomCategoryId, $product['id']]);
            echo "  ✓ Updated product: {$product['nama_produk']}\n";
        }
        echo "✅ Existing products updated with categories!\n\n";
    } else {
        echo "✅ All products already have categories assigned.\n\n";
    }
    
    echo "🎉 Category migration completed successfully!\n";
    echo "\n📊 Summary:\n";
    
    $categoryCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    $productCount = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $categorizedProducts = $pdo->query("SELECT COUNT(*) FROM products WHERE category_id IS NOT NULL")->fetchColumn();
    
    echo "  • Total categories: $categoryCount\n";
    echo "  • Total products: $productCount\n";
    echo "  • Categorized products: $categorizedProducts\n";
    
} catch (PDOException $e) {
    echo "❌ Migration failed: " . $e->getMessage() . "\n";
}
?>