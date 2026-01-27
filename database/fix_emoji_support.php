<?php
require_once __DIR__ . '/../config/database.php';

// Create database connection
$database = new Database();
$pdo = $database->getConnection();

if (!$pdo) {
    die("❌ Database connection failed!\n");
}

try {
    echo "🔧 Fixing emoji support in database...\n\n";
    
    // 1. Update database charset to utf8mb4
    echo "1. Setting database charset to utf8mb4...\n";
    $pdo->exec("ALTER DATABASE toko_online CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci");
    echo "✅ Database charset updated!\n\n";
    
    // 2. Update products table
    echo "2. Updating products table for emoji support...\n";
    $pdo->exec("ALTER TABLE products CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("ALTER TABLE products MODIFY nama_produk VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("ALTER TABLE products MODIFY deskripsi_singkat TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("ALTER TABLE products MODIFY deskripsi TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Products table updated!\n\n";
    
    // 3. Update categories table
    echo "3. Updating categories table for emoji support...\n";
    $pdo->exec("ALTER TABLE categories CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("ALTER TABLE categories MODIFY nama_kategori VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("ALTER TABLE categories MODIFY deskripsi TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Categories table updated!\n\n";
    
    // 4. Update settings table
    echo "4. Updating settings table for emoji support...\n";
    $pdo->exec("ALTER TABLE settings CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("ALTER TABLE settings MODIFY setting_value TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("ALTER TABLE settings MODIFY description VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Settings table updated!\n\n";
    
    // 5. Update admin table
    echo "5. Updating admin table for emoji support...\n";
    $pdo->exec("ALTER TABLE admin CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("ALTER TABLE admin MODIFY username VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Admin table updated!\n\n";
    
    // 6. Test emoji insertion
    echo "6. Testing emoji support...\n";
    $testEmoji = "🎮 Test Emoji 😀 🚀 ⭐ 💎";
    $stmt = $pdo->prepare("SELECT ? as test_emoji");
    $stmt->execute([$testEmoji]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['test_emoji'] === $testEmoji) {
        echo "✅ Emoji test passed: " . $result['test_emoji'] . "\n\n";
    } else {
        echo "❌ Emoji test failed!\n";
        echo "Expected: $testEmoji\n";
        echo "Got: " . $result['test_emoji'] . "\n\n";
    }
    
    echo "🎉 Emoji support fix completed!\n";
    echo "\n📝 Summary:\n";
    echo "  • Database charset: utf8mb4\n";
    echo "  • All tables: utf8mb4_unicode_ci\n";
    echo "  • Text fields: emoji-ready\n";
    echo "\n💡 Next steps:\n";
    echo "  1. Update database connection to use utf8mb4\n";
    echo "  2. Re-enter emoji content in admin panel\n";
    echo "  3. Test emoji display on website\n";
    
} catch (PDOException $e) {
    echo "❌ Fix failed: " . $e->getMessage() . "\n";
}
?>