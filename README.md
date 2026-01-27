# Website Katalog Produk - Toko Online

Website promosi bisnis jual-beli modern dengan WhatsApp sebagai media transaksi utama.

## 🚀 Fitur Utama

- **Frontend Modern**: HTML5, CSS3, Bootstrap 5, JavaScript
- **Backend PHP**: CRUD produk dengan upload gambar
- **Database MySQL**: Penyimpanan data produk dan admin
- **WhatsApp Integration**: Chat otomatis untuk setiap produk
- **Admin Settings**: Kelola nomor WhatsApp dan pengaturan website
- **Responsive Design**: Mobile, tablet, dan desktop friendly
- **FP Store <br> Admin Panel**: Kelola produk dan pengaturan dengan mudah

## 📋 Persyaratan Sistem

- PHP 7.4 atau lebih baru
- MySQL 5.7 atau lebih baru
- Web Server (Apache/Nginx)
- Browser modern

## 🛠️ Instalasi

### 1. Setup Database

1. Buat database MySQL baru:
```sql
CREATE DATABASE toko_online;
```

2. Import struktur tabel:
```bash
mysql -u root -p toko_online < database/create_tables.sql
```

### 2. Konfigurasi Database

Edit file `config/database.php` sesuai dengan pengaturan MySQL Anda:

```php
private $host = 'localhost';
private $db_name = 'toko_online';
private $username = 'root';
private $password = '';
```

### 3. Setup Folder Upload

Pastikan folder `assets/images/` memiliki permission write:

```bash
chmod 755 assets/images/
```

### 4. Konfigurasi WhatsApp

**Cara Mudah (Melalui FP Store <br> Admin Panel):**
1. Login ke FP Store <br> Admin Panel (`admin/login.php`)
2. Klik menu "Pengaturan"
3. Ubah nomor WhatsApp dan channel sesuai kebutuhan
4. Klik "Simpan Pengaturan"

**Cara Manual (Edit File):**
Edit file `assets/js/script.js` untuk mengatur nomor WhatsApp:

```javascript
const WHATSAPP_CONFIG = {
    number: '6281234567890', // Ganti dengan nomor Anda
    channel: 'https://whatsapp.com/channel/0029VaABC123' // Ganti dengan channel Anda
};
```

## 🔐 Login Admin

- **URL**: `admin/login.php`
- **Username**: `admin`
- **Password**: `admin123`

## 📁 Struktur Project

```
├── index.html              # Halaman utama
├── produk.html            # Halaman katalog produk
├── tentang.html           # Halaman tentang kami
├── kontak.html            # Halaman kontak
├── assets/
│   ├── css/
│   │   └── style.css      # Custom CSS
│   ├── js/
│   │   └── script.js      # JavaScript utama
│   └── images/            # Folder upload gambar
├── config/
│   └── database.php       # Konfigurasi database
├── api/
│   └── get_products.php   # API untuk mengambil produk
├── admin/
│   ├── index.php          # Dashboard admin
│   ├── login.php          # Login admin
│   ├── products.php       # Kelola produk
│   ├── settings.php       # Pengaturan website
│   └── logout.php         # Logout admin
└── database/
    └── create_tables.sql  # Script database
```

## 🎨 Kustomisasi

### Mengubah Warna Theme

Edit variabel CSS di `assets/css/style.css`:

```css
:root {
    --primary-color: #0d6efd;
    --success-color: #25d366;
    --dark-color: #212529;
}
```

### Mengubah Informasi Bisnis

1. **Nama Toko**: Edit di semua file HTML
2. **Nomor WhatsApp**: Edit di `assets/js/script.js`
3. **Alamat**: Edit di `kontak.html`

## 📱 Fitur WhatsApp

### Chat Otomatis
Setiap produk memiliki tombol WhatsApp yang akan membuka chat dengan pesan:
```
Halo admin, saya tertarik dengan produk [nama_produk]. Apakah masih tersedia?
```

### Channel WhatsApp
Tombol "Follow Channel" mengarah ke channel WhatsApp bisnis Anda.

## 🔧 Penggunaan FP Store <br> Admin Panel

### Login Admin
- URL: `admin/login.php`
- Username: `admin`
- Password: `admin123`

### Mengedit Nomor WhatsApp
1. Login ke FP Store <br> Admin Panel
2. Klik menu "Pengaturan"
3. Ubah nomor WhatsApp di form pengaturan
4. Klik "Simpan Pengaturan"
5. Website akan otomatis menggunakan nomor baru

### Menambah Produk
1. Login ke FP Store <br> Admin Panel
2. Klik "Kelola Produk"
3. Klik "Tambah Produk"
4. Isi form dan upload gambar
5. Klik "Simpan"

### Mengedit Produk
1. Di halaman produk, klik tombol edit (ikon pensil)
2. Ubah data yang diperlukan
3. Upload gambar baru (opsional)
4. Klik "Update"

### Menghapus Produk
1. Klik tombol hapus (ikon tempat sampah)
2. Konfirmasi penghapusan
3. Produk dan gambarnya akan terhapus

## 🖼️ Upload Gambar

- **Format**: JPG, PNG, WEBP
- **Ukuran maksimal**: 5MB
- **Lokasi**: `assets/images/`
- **Penamaan**: Otomatis dengan timestamp

## 🌐 Deployment

### Shared Hosting
1. Upload semua file ke folder public_html
2. Buat database MySQL via cPanel
3. Import file SQL
4. Edit konfigurasi database

### VPS/Dedicated Server
1. Setup web server (Apache/Nginx)
2. Setup PHP dan MySQL
3. Clone/upload project
4. Set permission folder images
5. Konfigurasi virtual host

## 🔒 Keamanan

- Password admin di-hash menggunakan PHP `password_hash()`
- Validasi file upload (tipe dan ukuran)
- Prepared statements untuk mencegah SQL injection
- Session management untuk admin

## 📞 Support

Jika ada pertanyaan atau butuh bantuan:
- Email: support@tokoonline.com
- WhatsApp: +62 812-3456-7890

## 📄 Lisensi

Project ini bebas digunakan untuk keperluan komersial dan non-komersial.

---

**Dibuat dengan ❤️ untuk kemudahan bisnis online Anda**