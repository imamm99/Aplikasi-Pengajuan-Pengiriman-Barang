-- Database: tugas_kelompok

SET FOREIGN_KEY_CHECKS=0;

-- 1. Users Table
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL, -- 'admin' or 'pengaju'
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Users
-- Passwords will be '123' (hashed in setup script, here strictly SQL usually doesn't bear hash logic easily without function, so we'll do it in PHP script, but table structure is key here).

-- 2. Kategori Barang (Admin manages this)
DROP TABLE IF EXISTS `kategori_barang`;
CREATE TABLE `kategori_barang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nama_kategori` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `kategori_barang` (`nama_kategori`) VALUES 
('Laptop'), 
('Smartphone'), 
('Mouse & Keyboard'),
('Monitor'),
('Audio & Speaker'),
('Aksesoris Elektronik');

-- 3. Barang (Admin manages this, or Pengaju selects)
DROP TABLE IF EXISTS `barang`;
CREATE TABLE `barang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(200) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `berat_kg` decimal(10,2) DEFAULT 0.00,
  `stok` int(11) NOT NULL DEFAULT 0,
  `deskripsi` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kategori_id` (`kategori_id`),
  CONSTRAINT `fk_barang_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_barang` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `barang` (`kode_barang`, `nama_barang`, `kategori_id`, `berat_kg`, `stok`, `deskripsi`) VALUES 
('LPT01', 'Laptop Asus', 1, 2.50, 10, 'Laptop Gaming'), 
('HP01', 'Samsung S24', 2, 0.20, 25, 'Smartphone Flagship'),
('MSE01', 'Logitech MX Master 3', 3, 0.15, 50, 'Wireless Mouse'),
('KBD01', 'Mechanical Keyboard RGB', 3, 1.20, 15, 'Gaming Keyboard'),
('MON01', 'Monitor LG 27 Inch', 4, 5.50, 8, 'Full HD Monitor');

-- 4. Pengajuan Pengiriman
-- "Pengaju mengisi data misal nama, alamat, tujuan"
DROP TABLE IF EXISTS `pengajuan_pengiriman`;
CREATE TABLE `pengajuan_pengiriman` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_resi` varchar(50) NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `user_id` int(11) NOT NULL, -- Who requested it
  
  -- Sender Info (Can be auto-filled from User profile later, but let's allow manual input as requested)
  `nama_pengirim` varchar(100) NOT NULL,
  `alamat_pengirim` text NOT NULL,
  `no_telepon_pengirim` varchar(20) NOT NULL,
  
  -- Receiver Info / Destination
  `nama_penerima` varchar(100) NOT NULL,
  `alamat_penerima` text NOT NULL, -- Destination Address
  `kota_tujuan` varchar(100) NOT NULL, -- "Tujuan"
  `no_telepon_penerima` varchar(20) NOT NULL,

  `status_pengiriman` enum('Pending','Revisi','Disetujui','Ditolak','Proses','Dikirim','Selesai') NOT NULL DEFAULT 'Pending',
  `catatan_admin` text DEFAULT NULL,
  `total_item` int(11) DEFAULT 0,
  `keterangan` text DEFAULT NULL,
  
  PRIMARY KEY (`id`),
  UNIQUE KEY `nomor_resi` (`nomor_resi`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Detail Pengajuan (Items within the shipment)
DROP TABLE IF EXISTS `detail_pengajuan`;
CREATE TABLE `detail_pengajuan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pengajuan_id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL, -- Select from Master Data
  `qty_barang` int(11) NOT NULL DEFAULT 1,
  `catatan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengajuan_id` (`pengajuan_id`),
  KEY `barang_id` (`barang_id`),
  CONSTRAINT `fk_detail_pengajuan` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_pengiriman` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_detail_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

SET FOREIGN_KEY_CHECKS=1;
