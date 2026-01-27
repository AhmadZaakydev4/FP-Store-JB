<?php
// Script untuk membuat database dan tabel otomatis

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'toko_online';

try {
    // Koneksi ke MySQL tanpa database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Buat database jika belum ada
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Database '$database' berhasil dibuat atau sudah ada.<br>";
    
    // Pilih database
    $pdo->exec("USE `$database`");
    
    // Buat tabel products
    $sql_products = "
    CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_produk VARCHAR(255) NOT NULL,
        deskripsi TEXT NOT NULL,
        foto VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql_products);
    echo "✅ Tabel 'products' berhasil dibuat.<br>";
    
    // Buat tabel settings
    $sql_settings = "
    CREATE TABLE IF NOT EXISTS settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT NOT NULL,
        description VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql_settings);
    echo "✅ Tabel 'settings' berhasil dibuat.<br>";
    
    // Cek apakah pengaturan sudah ada
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM settings");
    $stmt->execute();
    $settings_count = $stmt->fetchColumn();
    
    if ($settings_count == 0) {
        // Insert pengaturan default
        $default_settings = [
            ['whatsapp_link', 'https://wa.me/6281234567890', 'Link WhatsApp untuk chat otomatis'],
            ['whatsapp_channel', 'https://whatsapp.com/channel/0029VaABC123', 'Link channel WhatsApp'],
            ['site_name', 'Toko Online', 'Nama website/toko'],
            ['site_email', 'info@tokoonline.com', 'Email kontak'],
            ['site_phone', '+62 812-3456-7890', 'Nomor telepon kontak'],
            ['site_address', 'Jl. Contoh No. 123, Kota Contoh, Provinsi, Kode Pos 12345', 'Alamat toko']
        ];
        
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value, description) VALUES (?, ?, ?)");
        
        foreach ($default_settings as $setting) {
            $stmt->execute($setting);
        }
        
        echo "✅ " . count($default_settings) . " pengaturan default berhasil ditambahkan.<br>";
    } else {
        echo "ℹ️ Pengaturan sudah ada ($settings_count pengaturan).<br>";
    }
    
    // Buat tabel admin
    $sql_admin = "
    CREATE TABLE IF NOT EXISTS admin (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql_admin);
    echo "✅ Tabel 'admin' berhasil dibuat.<br>";
    
    // Cek apakah admin sudah ada
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin WHERE username = 'admin'");
    $stmt->execute();
    $admin_exists = $stmt->fetchColumn();
    
    if (!$admin_exists) {
        // Insert admin default (password: admin123)
        $hashed_password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO admin (username, password) VALUES ('admin', ?)");
        $stmt->execute([$hashed_password]);
        echo "✅ Admin default berhasil dibuat (username: admin, password: admin123).<br>";
    } else {
        echo "ℹ️ Admin sudah ada.<br>";
    }
    
    // Cek apakah produk contoh sudah ada
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM products");
    $stmt->execute();
    $product_count = $stmt->fetchColumn();
    
    if ($product_count == 0) {
        // Insert produk contoh
        $sample_products = [
            [
                'nama_produk' => 'Smartphone Android Terbaru',
                'deskripsi' => 'Smartphone dengan fitur canggih dan kamera berkualitas tinggi untuk kebutuhan sehari-hari.',
                'foto' => 'assets/images/no-image.jpg'
            ],
            [
                'nama_produk' => 'Laptop Gaming Performance',
                'deskripsi' => 'Laptop gaming dengan spesifikasi tinggi untuk gaming dan produktivitas maksimal.',
                'foto' => 'assets/images/no-image.jpg'
            ],
            [
                'nama_produk' => 'Headphone Wireless Premium',
                'deskripsi' => 'Headphone nirkabel dengan kualitas suara jernih dan noise cancellation terbaik.',
                'foto' => 'assets/images/no-image.jpg'
            ],
            [
                'nama_produk' => 'Smartwatch Fitness Tracker',
                'deskripsi' => 'Jam tangan pintar dengan fitur tracking kesehatan dan notifikasi smartphone.',
                'foto' => 'assets/images/no-image.jpg'
            ],
            [
                'nama_produk' => 'Kamera DSLR Profesional',
                'deskripsi' => 'Kamera DSLR untuk fotografi profesional dengan hasil gambar berkualitas tinggi.',
                'foto' => 'assets/images/no-image.jpg'
            ],
            [
                'nama_produk' => 'Speaker Bluetooth Portable',
                'deskripsi' => 'Speaker portabel dengan suara bass yang menggelegar dan tahan air.',
                'foto' => 'assets/images/no-image.jpg'
            ]
        ];
        
        $stmt = $pdo->prepare("INSERT INTO products (nama_produk, deskripsi, foto) VALUES (?, ?, ?)");
        
        foreach ($sample_products as $product) {
            $stmt->execute([$product['nama_produk'], $product['deskripsi'], $product['foto']]);
        }
        
        echo "✅ " . count($sample_products) . " produk contoh berhasil ditambahkan.<br>";
    } else {
        echo "ℹ️ Produk sudah ada ($product_count produk).<br>";
    }
    
    echo "<br><h3>🎉 Setup Database Selesai!</h3>";
    echo "<p><strong>Informasi Login Admin:</strong></p>";
    echo "<ul>";
    echo "<li>URL: <a href='admin/login.php'>admin/login.php</a></li>";
    echo "<li>Username: <strong>admin</strong></li>";
    echo "<li>Password: <strong>admin123</strong></li>";
    echo "</ul>";
    echo "<p><a href='index.html'>🏠 Kembali ke Website</a> | <a href='admin/login.php'>🔐 Login Admin</a></p>";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "<br>";
    echo "<p>Pastikan:</p>";
    echo "<ul>";
    echo "<li>XAMPP/WAMP sudah berjalan</li>";
    echo "<li>MySQL service aktif</li>";
    echo "<li>Username dan password MySQL benar</li>";
    echo "</ul>";
}
?>