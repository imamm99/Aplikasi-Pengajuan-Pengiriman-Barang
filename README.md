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
git clone https://github.com/imamm99/Aplikasi-Pengajuan-Pengiriman-Barang.git
cd Aplikasi-Pengajuan-Pengiriman-Barang
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
# - BASE_PATH: Path aplikasi (sesuaikan dengan nama folder, contoh: /Aplikasi-Pengajuan-Pengiriman-Barang)
# - THEME: Theme yang digunakan (default: adminkit)
```

### 4. Setup Database
```bash
# Buat database terlebih dahulu
mysql -u root -p
CREATE DATABASE tugas_kelompok;
exit;

# Import database lengkap (sudah termasuk user dan sample data)
mysql -u root -p tugas_kelompok < tugas_kelompok.sql
```

### 5. Login dengan User yang Tersedia

File `tugas_kelompok.sql` sudah berisi beberapa user yang bisa langsung digunakan:

| Username | Password | Role |
|----------|----------|------|
| admin | 123 | admin |
| pengaju | 123 | pengaju |
| pengaju1 | 123 | pengaju |

⚠️ **Catatan Keamanan**: Ganti password default ini setelah login pertama!

**Atau buat user baru via Registrasi:**
1. Akses `http://localhost/tugas_kelompok/register.php`
2. Daftar sebagai admin atau pengaju
3. Login dengan kredensial yang dibuat

### 6. Akses Aplikasi
```
http://localhost/Aplikasi-Pengajuan-Pengiriman-Barang/
```

Atau sesuaikan dengan `BASE_PATH` yang Anda set di file `.env`.

## 🔐 Default Credentials

User yang tersedia di `tugas_kelompok.sql`:
- **Admin**: username: `admin`, password: `123`
- **Pengaju**: username: `pengaju`, password: `123`
- **Pengaju1**: username: `pengaju1`, password: `123`

⚠️ **Ganti password default segera setelah login pertama untuk keamanan!**

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

### Error: Folder Vendor Corrupt atau Tidak Lengkap
**Penyebab**: Cache composer bermasalah, download terputus, atau antivirus blocking

**Solusi - Install Ulang Vendor:**

**Di Git Bash / Linux / Mac:**
```bash
# 1. Hapus folder vendor yang corrupt
rm -rf vendor/

# 2. Clear cache composer
composer clear-cache

# 3. Install ulang
composer install --no-cache
```

**Di Windows PowerShell:**
```powershell
# 1. Hapus folder vendor yang corrupt
Remove-Item -Recurse -Force vendor

# 2. Clear cache composer
composer clear-cache

# 3. Install ulang
composer install --no-cache
```

**Tips Tambahan:**
- Jalankan sebagai **Administrator** jika ada error permission
- Tambahkan folder vendor ke **exclusion list antivirus** (Windows Defender sering blocking)
- Update composer: `composer self-update`

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
