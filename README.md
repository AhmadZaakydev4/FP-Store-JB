# FP Store - Modern E-Commerce Website

🛍️ **FP Store** adalah website e-commerce modern yang dioptimalkan untuk bisnis jual-beli online dengan integrasi WhatsApp sebagai media transaksi utama. Cocok untuk toko game, aplikasi premium, dan produk digital lainnya.

## ✨ Fitur Utama

- **🎨 Frontend Modern**: HTML5, CSS3, Bootstrap 5, JavaScript ES6+
- **⚡ Backend PHP**: CRUD produk lengkap dengan upload gambar
- **🗄️ Database MySQL**: Penyimpanan data produk, kategori, dan admin
- **📱 WhatsApp Integration**: Chat otomatis untuk setiap produk
- **⚙️ Admin Panel**: Dashboard lengkap untuk kelola produk dan pengaturan
- **📱 Responsive Design**: Mobile-first, tablet, dan desktop friendly
- **🌙 Dark Mode**: Toggle tema gelap/terang
- **🚀 Performance**: Cache busting, asset optimization, SEO friendly

## 📋 Persyaratan Sistem

- PHP 7.4 atau lebih baru
- MySQL 5.7 atau lebih baru
- Web Server (Apache/Nginx)
- Browser modern

## 🛠️ Instalasi & Setup

### 1. Clone Repository

```bash
git clone https://github.com/azaki375586-blip/FP-Store-JB.git
cd FP-Store-JB
```

### 2. Setup Database

1. Buat database MySQL baru:
```sql
CREATE DATABASE fp_store;
```

2. Import struktur tabel:
```bash
mysql -u root -p fp_store < database/create_tables.sql
```

### 3. Konfigurasi Database

Edit file `config/database.php`:

```php
private $host = 'localhost';
private $db_name = 'fp_store';
private $username = 'root';
private $password = '';
```

### 4. Setup Permissions

```bash
chmod 755 assets/images/
```

### 5. Konfigurasi WhatsApp

**Melalui Admin Panel (Recommended):**
1. Akses admin panel: `admin/login.php`
2. Login dengan kredensial default
3. Masuk ke menu "Pengaturan"
4. Update nomor WhatsApp dan channel
5. Simpan pengaturan

**Manual Configuration:**
Edit `assets/js/script.js`:

```javascript
const WHATSAPP_CONFIG = {
    number: '6289507410373', // Nomor WhatsApp Anda
    channel: 'https://whatsapp.com/channel/0029VaABC123' // Channel WhatsApp
};
```

## 🔐 Admin Access

- **URL**: `/admin/login.php`
- **Username**: `admin`
- **Password**: `admin123`

> ⚠️ **Penting**: Ganti password default setelah instalasi!

## 📁 Struktur Project

```
FP-Store-JB/
├── 📄 index.php              # Homepage dengan cache busting
├── 📄 produk.php             # Katalog produk dengan filter
├── 📄 detail-produk.php      # Detail produk individual
├── 📄 tentang.php            # Halaman about us
├── 📄 kontak.php             # Halaman kontak
├── 📁 assets/
│   ├── 🎨 css/
│   │   ├── style.css         # Main stylesheet
│   │   ├── admin.css         # Admin panel styles
│   │   └── admin-sidebar-fix.css
│   ├── 📜 js/
│   │   ├── script.js         # Frontend JavaScript
│   │   ├── admin.js          # Admin panel JS
│   │   ├── product-detail.js # Product detail functionality
│   │   └── admin-sidebar-fix.js
│   └── 🖼️ images/            # Product images & assets
├── 📁 config/
│   └── database.php          # Database configuration
├── 📁 api/
│   ├── get_products.php      # Products API endpoint
│   ├── get_product.php       # Single product API
│   ├── get_categories.php    # Categories API
│   └── get_settings.php      # Settings API
├── 📁 admin/
│   ├── index.php             # Admin dashboard
│   ├── login.php             # Admin authentication
│   ├── products.php          # Product management
│   ├── categories.php        # Category management
│   ├── settings.php          # Website settings
│   ├── logout.php            # Logout handler
│   └── includes/             # Admin components
├── 📁 includes/
│   ├── cache_buster.php      # Cache management
│   └── meta_cache.php        # Meta cache utilities
├── 📁 database/
│   ├── create_tables.sql     # Database schema
│   └── add_promo_tables.sql  # Additional tables
└── 📄 minify_assets.php      # Asset optimization utility
```

## 🎨 Kustomisasi

### Theme Colors

Edit CSS variables di `assets/css/style.css`:

```css
:root {
    --primary-color: #0d6efd;
    --success-color: #25d366;
    --dark-color: #212529;
    --light-color: #f8f9fa;
}
```

### Dark Mode

Website sudah dilengkapi dengan dark mode toggle. Tema tersimpan di localStorage browser.

### Business Information

1. **Store Name**: Edit di semua file PHP
2. **WhatsApp Number**: Via admin panel atau edit `assets/js/script.js`
3. **Contact Info**: Edit di `kontak.php`

## 📱 WhatsApp Features

### Auto Chat Messages
Setiap produk memiliki tombol WhatsApp dengan template pesan:
```
Halo admin, saya tertarik dengan produk [nama_produk]. Apakah masih tersedia?
```

### WhatsApp Channel
Tombol "Follow Channel" untuk subscribe ke channel bisnis Anda.

## 🔧 Admin Panel Guide

### Product Management
- ➕ **Add Product**: Form lengkap dengan upload gambar
- ✏️ **Edit Product**: Update info dan ganti gambar
- 🗑️ **Delete Product**: Hapus produk dan gambarnya
- 📂 **Categories**: Kelola kategori produk

### Settings Management
- 📱 **WhatsApp Config**: Update nomor dan channel
- 🏪 **Store Info**: Nama toko dan informasi bisnis
- 🎨 **Display Settings**: Pengaturan tampilan website

### Image Upload
- **Supported**: JPG, PNG, WEBP
- **Max Size**: 5MB
- **Auto Resize**: Otomatis resize untuk performa
- **Naming**: Timestamp-based naming

## 🚀 Deployment

### Shared Hosting (cPanel)
1. Upload files ke `public_html/`
2. Create MySQL database via cPanel
3. Import `database/create_tables.sql`
4. Update `config/database.php`
5. Set folder permissions: `chmod 755 assets/images/`

### VPS/Cloud Server
```bash
# Clone repository
git clone https://github.com/azaki375586-blip/FP-Store-JB.git
cd FP-Store-JB

# Set permissions
chmod 755 assets/images/
chown -R www-data:www-data assets/images/

# Setup virtual host (Apache/Nginx)
# Import database
mysql -u root -p your_database < database/create_tables.sql
```

### Docker (Optional)
```dockerfile
FROM php:8.1-apache
COPY . /var/www/html/
RUN chmod 755 /var/www/html/assets/images/
EXPOSE 80
```

## 🔒 Security Features

- 🔐 **Password Hashing**: PHP `password_hash()` dengan BCRYPT
- 🛡️ **SQL Injection Protection**: Prepared statements
- 📁 **File Upload Validation**: Type, size, dan extension checking
- 🔑 **Session Management**: Secure admin sessions
- 🚫 **XSS Protection**: Input sanitization
- 🔒 **CSRF Protection**: Token-based form protection

## 🎯 Performance Optimization

- ⚡ **Cache Busting**: Automatic asset versioning
- 🗜️ **Asset Minification**: CSS/JS compression
- 🖼️ **Image Optimization**: Auto-resize uploaded images
- 📱 **Mobile-First**: Optimized for mobile devices
- 🚀 **Lazy Loading**: Images loaded on demand

## 🛠️ Development

### Local Development
```bash
# Using PHP built-in server
php -S localhost:8000

# Using XAMPP/WAMP
# Place in htdocs folder and access via localhost
```

### Contributing
1. Fork the repository
2. Create feature branch: `git checkout -b feature-name`
3. Commit changes: `git commit -am 'Add feature'`
4. Push to branch: `git push origin feature-name`
5. Submit pull request

## 📊 Tech Stack

- **Frontend**: HTML5, CSS3, Bootstrap 5, Vanilla JavaScript
- **Backend**: PHP 7.4+, MySQL 5.7+
- **Libraries**: Font Awesome, Google Fonts
- **Tools**: Git, Composer (optional)

## 🐛 Troubleshooting

### Common Issues

**Database Connection Error:**
```php
// Check config/database.php settings
// Verify MySQL service is running
// Check database credentials
```

**Image Upload Failed:**
```bash
# Check folder permissions
chmod 755 assets/images/
# Check PHP upload settings in php.ini
upload_max_filesize = 5M
post_max_size = 5M
```

**WhatsApp Links Not Working:**
```javascript
// Verify phone number format in script.js
// Should be: 6281234567890 (country code + number)
```

## 📞 Support & Contact

- 🐛 **Issues**: [GitHub Issues](https://github.com/azaki375586-blip/FP-Store-JB/issues)
- 💬 **Discussions**: [GitHub Discussions](https://github.com/azaki375586-blip/FP-Store-JB/discussions)
- 📧 **Email**: [Contact Developer](mailto:azaki375586@gmail.com)

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 🙏 Acknowledgments

- Bootstrap team for the amazing CSS framework
- Font Awesome for the beautiful icons
- PHP community for excellent documentation
- All contributors and users of this project

---

<div align="center">

**⭐ Star this repo if you find it helpful!**

Made with ❤️ for Indonesian e-commerce businesses

[🚀 Live Demo](https://your-demo-url.com) • [📖 Documentation](https://github.com/azaki375586-blip/FP-Store-JB/wiki) • [🐛 Report Bug](https://github.com/azaki375586-blip/FP-Store-JB/issues)

</div>