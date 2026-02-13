# Aplikasi Pengajuan Pengiriman

Aplikasi manajemen pengajuan pengiriman dengan sistem role-based (Admin & Pengaju).

## 📋 Fitur

### Dashboard
- **Admin Dashboard**: Overview semua pengajuan dengan status cards dan tabel recent submissions
- **Pengaju Dashboard**: Dashboard personal dengan statistik pengajuan sendiri

### Manajemen Pengajuan
- Create, Read, Update, Delete pengajuan pengiriman
- Workflow approval (Pending → Revisi/Ditolak/Disetujui → Proses → Dikirim → Selesai)
- Master-detail entry (pengajuan + detail barang)
- Status tracking dengan color-coded badges

### Role-Based Access
- **Admin**: Verifikasi, approval, dan manajemen semua pengajuan
- **Pengaju**: Submit dan track pengajuan pribadi

## 🛠️ Tech Stack

- PHP 7.4+
- MySQL/MariaDB
- Bootstrap 5 (AdminKit template)
- Font Awesome icons
- DotEnv untuk konfigurasi

## 📦 Instalasi

### 1. Clone Repository
```bash
git clone <repository-url>
cd tugas_kelompok
```

### 2. Install Dependencies (WAJIB!)
```bash
composer install
```
⚠️ **PENTING**: Folder `vendor/` tidak ada di GitHub (diabaikan oleh .gitignore). Anda **HARUS** menjalankan `composer install` terlebih dahulu, jika tidak aplikasi akan error!

### 3. Setup Environment
```bash
# Copy file .env.example menjadi .env
cp .env.example .env

# Edit .env sesuai konfigurasi Anda
# - DB_HOST: Host database (default: localhost)
# - DB_USER: Username database (default: root)
# - DB_PASSWORD: Password database (kosongkan jika tidak ada)
# - DB_NAME: Nama database (default: tugas_kelompok)
# - DB_PORT: Port MySQL (default: 3306)
# - BASE_PATH: Path aplikasi (misal: /tugas_kelompok)
# - THEME: Theme yang digunakan (default: adminkit)
```

### 4. Setup Database
```bash
# Buat database terlebih dahulu
mysql -u root -p
CREATE DATABASE tugas_kelompok;
exit;

# Import schema database
mysql -u root -p tugas_kelompok < tugas_kelompok.sql
```

### 5. Buat User Login
Karena `tugas_kelompok.sql` tidak berisi user, Anda perlu membuat user manual:

**Via Registrasi (Cara termudah):**
1. Akses `http://localhost/tugas_kelompok/register.php`
2. Daftar sebagai admin atau pengaju
3. Login dengan kredensial yang dibuat

**Via phpMyAdmin atau MySQL CLI:**
```sql
-- Admin user (password: admin123)
INSERT INTO users (username, password, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Pengaju user (password: pengaju123)
INSERT INTO users (username, password, role) 
VALUES ('pengaju', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pengaju');
```

### 6. Akses Aplikasi
```
http://localhost/tugas_kelompok/
```

## 🔐 Default Credentials

Jika Anda menggunakan `database_with_data.sql` (jika tersedia), gunakan:
- **Admin**: username: `admin`, password: `123`
- **Pengaju**: username: `pengaju`, password: `123`

⚠️ **Ganti password default segera setelah login pertama!**

## 📁 Struktur Folder

```
tugas_kelompok/
├── admin/              # Dashboard & panel admin
├── pengaju/            # Dashboard pengaju
├── transaksi_pengiriman/  # CRUD pengajuan
├── barang/             # Master data barang
├── kategori_barang/    # Master data kategori
├── config/             # Konfigurasi (database, menu)
├── lib/                # Library (auth, functions)
├── views/              # Template & themes
│   └── adminkit/       # AdminKit theme
├── assets/             # CSS, JS, images
└── vendor/             # Composer dependencies
```

## 🎨 Themes

Aplikasi menggunakan AdminKit Bootstrap template. Untuk mengganti theme:
1. Edit `.env`
2. Ubah `THEME=adminkit` ke theme yang tersedia

## 🔧 Troubleshooting

### Error: "vendor/autoload.php not found" atau "Class not found"
**Penyebab**: Folder `vendor/` tidak ada karena diabaikan oleh `.gitignore`

**Solusi**:
```bash
# Pastikan composer terinstall
composer --version

# Install dependencies
composer install
```

### Error: "Table doesn't exist"
Pastikan Anda sudah import `tugas_kelompok.sql`:
```bash
mysql -u root -p tugas_kelompok < tugas_kelompok.sql
```

### Error: "Connection refused"
Cek apakah:
- MySQL/MariaDB sudah running
- Kredensial di `.env` sudah benar
- Database `tugas_kelompok` sudah dibuat

### Halaman blank/error 500
Cek:
- PHP version (minimum 7.4)
- File `.env` sudah ada dan terisi dengan benar
- `composer install` sudah dijalankan ← **INI SERING TERLUPAKAN!**
- Error log di `C:\xampp\apache\logs\error.log`

## 📝 Development

### Requirements
- PHP 7.4 or higher
- MySQL 5.7+ / MariaDB 10.2+
- Composer
- Apache/Nginx dengan mod_rewrite enabled

### Composer Dependencies
```bash
composer require vlucas/phpdotenv
```

## 🤝 Contributing

1. Fork repository
2. Buat branch baru (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push ke branch (`git push origin feature/AmazingFeature`)
5. Buat Pull Request

## 📄 License

MIT License - feel free to use for your projects!

## 👥 Authors

Developed as a group project for university assignment.
