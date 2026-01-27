-- Database: toko_online
-- Buat database terlebih dahulu: CREATE DATABASE toko_online;

-- Tabel untuk kategori produk
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_kategori VARCHAR(100) NOT NULL,
    deskripsi TEXT,
    icon VARCHAR(50) DEFAULT 'fas fa-folder',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel untuk menyimpan data produk
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_produk VARCHAR(255) NOT NULL,
    deskripsi_singkat TEXT NOT NULL,
    deskripsi TEXT NOT NULL,
    foto VARCHAR(255) NOT NULL,
    category_id INT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Tabel untuk admin
CREATE TABLE IF NOT EXISTS admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert admin default (username: admin, password: admin123)
INSERT INTO admin (username, password) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Tabel untuk pengaturan website
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT NOT NULL,
    description VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert pengaturan default
INSERT INTO settings (setting_key, setting_value, description) VALUES 
('whatsapp_number', '6281234567890', 'Nomor WhatsApp untuk chat otomatis'),
('whatsapp_channel', 'https://whatsapp.com/channel/0029VaABC123', 'Link channel WhatsApp'),
('site_name', 'Toko Online', 'Nama website/toko'),
('site_email', 'info@tokoonline.com', 'Email kontak'),
('site_phone', '+62 812-3456-7890', 'Nomor telepon kontak'),
('site_address', 'Jl. Contoh No. 123, Kota Contoh, Provinsi, Kode Pos 12345', 'Alamat toko');

-- Insert kategori default untuk FP Store
INSERT INTO categories (nama_kategori, deskripsi, icon) VALUES 
('Akun Game', 'Akun game premium dengan level tinggi dan item lengkap', 'fas fa-gamepad'),
('Aplikasi Premium', 'Aplikasi berbayar dan premium untuk Android/iOS', 'fas fa-mobile-alt'),
('Akun Sosial Media', 'Akun media sosial dengan follower tinggi', 'fas fa-share-alt'),
('Software & Tools', 'Software premium dan tools untuk produktivitas', 'fas fa-laptop-code'),
('Streaming & Entertainment', 'Akun streaming premium seperti Netflix, Spotify', 'fas fa-play-circle');

-- Insert contoh produk dengan kategori
INSERT INTO products (nama_produk, deskripsi_singkat, deskripsi, foto, category_id) VALUES 
('Akun Mobile Legends Mythic', '🎮 Akun ML rank Mythic dengan 100+ skin epic', '<p><strong>🎮 Akun Mobile Legends Premium</strong></p><p>✨ <strong>Rank Mythic Glory</strong></p><p>🔥 <strong>100+ Skin Epic & Legend</strong></p><p>💎 <strong>Hero Lengkap (120+)</strong></p><p>⚔️ <strong>Emblem Max Level</strong></p><p>🏆 <strong>Winrate 70%+</strong></p><p><em>Garansi aman dan tidak akan di-recover! 🛡️</em></p>', 'assets/images/smartphone.jpg', 1),
('Akun Free Fire Sultan', '🔥 Akun FF dengan diamond unlimited dan skin rare', '<p><strong>🔥 Akun Free Fire Sultan</strong></p><p>💎 <strong>Diamond Unlimited</strong></p><p>👑 <strong>Skin Rare & Limited</strong></p><p>🎯 <strong>Level Max</strong></p><p>🏆 <strong>Badge Lengkap</strong></p><p><em>Akun aman dengan garansi! 🛡️</em></p>', 'assets/images/laptop.jpg', 1),
('Spotify Premium Lifetime', '🎵 Akun Spotify Premium tanpa iklan selamanya', '<p><strong>🎵 Spotify Premium Lifetime</strong></p><p>🚫 <strong>Tanpa Iklan</strong></p><p>📱 <strong>Download Offline</strong></p><p>🎧 <strong>Kualitas Audio HD</strong></p><p>🌍 <strong>Akses Global</strong></p><p><em>Garansi lifetime! ♾️</em></p>', 'assets/images/headphone.jpg', 5),
('Canva Pro Premium', '🎨 Akun Canva Pro dengan template premium unlimited', '<p><strong>🎨 Canva Pro Premium</strong></p><p>✨ <strong>Template Premium Unlimited</strong></p><p>📸 <strong>Stock Photo & Video</strong></p><p>🎭 <strong>Background Remover</strong></p><p>💾 <strong>Storage 1TB</strong></p><p><em>Perfect untuk designer! 🚀</em></p>', 'assets/images/smartwatch.jpg', 2),
('Instagram Verified Account', '✅ Akun Instagram terverifikasi dengan 100K+ followers', '<p><strong>✅ Instagram Verified Account</strong></p><p>👥 <strong>100K+ Real Followers</strong></p><p>📈 <strong>High Engagement Rate</strong></p><p>🎯 <strong>Niche: Lifestyle</strong></p><p>📊 <strong>Analytics Access</strong></p><p><em>Perfect untuk influencer! 🌟</em></p>', 'assets/images/camera.jpg', 3),
('Adobe Creative Suite', '🎨 Paket lengkap Adobe CC dengan semua aplikasi premium', '<p><strong>🎨 Adobe Creative Suite Complete</strong></p><p>📐 <strong>Photoshop, Illustrator, Premiere</strong></p><p>🎬 <strong>After Effects, InDesign</strong></p><p>☁️ <strong>Cloud Storage 100GB</strong></p><p>🔄 <strong>Update Otomatis</strong></p><p><em>Untuk professional designer! 💼</em></p>', 'assets/images/speaker.jpg', 4);