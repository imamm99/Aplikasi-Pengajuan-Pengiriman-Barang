-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 13 Feb 2026 pada 15.50
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tugas_kelompok`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `id` int(11) NOT NULL,
  `kode_barang` varchar(50) NOT NULL,
  `nama_barang` varchar(200) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `berat_kg` decimal(10,2) DEFAULT 1.00,
  `stok` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `barang`
--

INSERT INTO `barang` (`id`, `kode_barang`, `nama_barang`, `kategori_id`, `deskripsi`, `berat_kg`, `stok`) VALUES
(1, 'LPT01', 'Laptop Asus', 1, 'Laptop Gaming', 2.50, 0),
(2, 'HP01', 'Samsung S24', 2, 'Smartphone Flagship', 0.20, 25),
(3, 'MSE01', 'Logitech MX Master 3', 3, 'Wireless Mouse', 0.15, 49),
(4, 'KBD01', 'Mechanical Keyboard RGB', 3, 'Gaming Keyboard', 1.20, 15),
(5, 'MON01', 'Monitor LG 27 Inch', 4, 'Full HD Monitor', 5.00, 8);

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pengajuan`
--

CREATE TABLE `detail_pengajuan` (
  `id` int(11) NOT NULL,
  `pengajuan_id` int(11) NOT NULL,
  `barang_id` int(11) NOT NULL,
  `qty_barang` int(11) NOT NULL DEFAULT 1,
  `catatan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `detail_pengajuan`
--

INSERT INTO `detail_pengajuan` (`id`, `pengajuan_id`, `barang_id`, `qty_barang`, `catatan`) VALUES
(3, 4, 1, 2, ''),
(4, 4, 3, 9, ''),
(5, 4, 4, 3, ''),
(6, 5, 4, 2, ''),
(7, 5, 3, 5, ''),
(8, 6, 4, 1, ''),
(11, 10, 4, 3, ''),
(12, 10, 2, 2, ''),
(13, 11, 3, 1, ''),
(14, 12, 3, 1, ''),
(15, 13, 3, 2, ''),
(16, 14, 5, 1, ''),
(18, 15, 1, 10, ''),
(19, 16, 3, 1, ''),
(20, 17, 5, 1, '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `item_pengiriman`
--

CREATE TABLE `item_pengiriman` (
  `id` int(11) NOT NULL,
  `pengiriman_id` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `berat` decimal(10,2) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_barang`
--

CREATE TABLE `kategori_barang` (
  `id` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori_barang`
--

INSERT INTO `kategori_barang` (`id`, `nama_kategori`) VALUES
(1, 'Laptop'),
(2, 'Smartphone'),
(3, 'Mouse & Keyboard'),
(4, 'Monitor'),
(5, 'Audio & Speaker'),
(6, 'Aksesoris Elektronik');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan_pengiriman`
--

CREATE TABLE `pengajuan_pengiriman` (
  `id` int(11) NOT NULL,
  `nomor_resi` varchar(50) NOT NULL,
  `tanggal_pengajuan` date NOT NULL,
  `user_id` int(11) NOT NULL,
  `nama_pengirim` varchar(100) NOT NULL,
  `alamat_pengirim` text NOT NULL,
  `no_telepon_pengirim` varchar(20) NOT NULL,
  `nama_penerima` varchar(100) NOT NULL,
  `alamat_penerima` text NOT NULL,
  `kota_tujuan` varchar(100) NOT NULL,
  `no_telepon_penerima` varchar(20) NOT NULL,
  `status_pengiriman` enum('Pending','Revisi','Disetujui','Ditolak','Proses','Dikirim','Selesai') NOT NULL DEFAULT 'Pending',
  `catatan_admin` text DEFAULT NULL,
  `total_item` int(11) DEFAULT 0,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengajuan_pengiriman`
--

INSERT INTO `pengajuan_pengiriman` (`id`, `nomor_resi`, `tanggal_pengajuan`, `user_id`, `nama_pengirim`, `alamat_pengirim`, `no_telepon_pengirim`, `nama_penerima`, `alamat_penerima`, `kota_tujuan`, `no_telepon_penerima`, `status_pengiriman`, `catatan_admin`, `total_item`, `keterangan`) VALUES
(3, 'REG20260202124534818', '2026-02-02', 3, 'awa', 'jalan pasuketan', '098888888', 'anin', 'jalan derajat', 'cirebon', '0987777777', 'Dikirim', '', 2, 'depan pom bensin'),
(4, 'REG20260202130808198', '2026-02-02', 4, 'popi', 'jalan duku semar', '0987765', 'nunik', 'jalan rajawali', 'cirebin', '09999', 'Selesai', NULL, 3, ''),
(5, 'REG20260202131256826', '2026-02-02', 4, 'rani', 'jalan sumber', '09888', 'kari', 'perumnas', 'cirebon', '09889', 'Ditolak', 'nomer telepon tidak dapat dihubungi', 2, ''),
(6, 'REG20260202132148459', '2026-02-02', 4, 'aaa', 'aaa', '2222', 'aaaa', 'xxxxx', 'aaaaa', '3131', 'Revisi', 'kkkk', 1, ''),
(10, 'REG20260202135358705', '2026-02-02', 4, 'amin', 'jalan rajawali', '0123', 'asep', 'jalan derajat', 'cirebon', '0234', 'Selesai', '', 2, ''),
(11, 'REG20260202140643242', '2026-02-02', 4, 'kkkk', 'dddd', '444', 'ggg', 'ggg', 'ggggg', '777', 'Selesai', '', 1, ''),
(12, 'REG20260202142138235', '2026-02-02', 4, 'sss', 'sss', '111', 'sssss', 'sss', 'ssss', '111', 'Selesai', '', 1, ''),
(13, 'REG20260202142600355', '2026-02-02', 4, 'aaa', 'aaa', '888', 'jjj', 'lll', 'kkkk', '000', 'Selesai', '', 1, ''),
(14, 'REG20260202142724961', '2026-02-02', 4, 'hana', 'jalan perkutut', '0988887', 'karin', 'jalan rajawali', 'cirebon', '09998', 'Selesai', '', 1, ''),
(15, 'REG20260212200045411', '2026-02-12', 7, 'ffff', 'ffff', '2222', 'rrrr', 'rrr', 'rrrr', '111', 'Dikirim', '', 2, 'rrrrr'),
(16, 'REG20260213195312195', '2026-02-13', 4, 'hana', 'srfaga', '23515', 'jtyjrt', 'hsrhsr', 'ygsedg', '73573', 'Dikirim', '', 1, ''),
(17, 'REG20260213201958900', '2026-02-13', 1, 'jfrjud', 'fdjdr', '346263', 'hfrshs', 'srhsrh', 'rjhrdshbf', '734574', 'Dikirim', '', 1, '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengiriman`
--

CREATE TABLE `pengiriman` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal_pengajuan` datetime DEFAULT current_timestamp(),
  `nama_penerima` varchar(100) NOT NULL,
  `alamat_penerima` text NOT NULL,
  `kota_penerima` varchar(50) DEFAULT NULL,
  `no_hp_penerima` varchar(20) DEFAULT NULL,
  `total_berat` decimal(10,2) DEFAULT 0.00,
  `total_item` int(11) DEFAULT 0,
  `status` enum('pending','verified','processed','rejected') DEFAULT 'pending',
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '$2y$10$PdoDwpMJILblanEkek0gc.SiW2g9ofaolI5D4eZXpidADycOGgPRm', 'admin'),
(2, 'pengaju', '$2y$10$0O2biApXyRtJ4EF/pVIVFeSOW03JVoe1A.GpZmBO45nPsKAaMKZla', 'pengaju'),
(3, 'awa', '$2y$10$wxwSqBiw5NhqYogNMTxxteVUVlM8kvR.9dyUtz6FwHKIzp4rgKl8i', 'pengaju'),
(4, 'pengaju1', '$2y$10$3dBJuVitvzjgfcFsYcWCXOfsimqzsaUt3OBL/U1Ll9ZbuHvNkXjD2', 'pengaju'),
(7, 'pengaju5', '$2y$10$pg4Y1r6G5LUa0uO/.dT6xe52LA/k4D8qGQVk7fhmj.EtmFnH.BQ4y', 'pengaju');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Indeks untuk tabel `detail_pengajuan`
--
ALTER TABLE `detail_pengajuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengajuan_id` (`pengajuan_id`),
  ADD KEY `barang_id` (`barang_id`);

--
-- Indeks untuk tabel `item_pengiriman`
--
ALTER TABLE `item_pengiriman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pengiriman_id` (`pengiriman_id`);

--
-- Indeks untuk tabel `kategori_barang`
--
ALTER TABLE `kategori_barang`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `pengajuan_pengiriman`
--
ALTER TABLE `pengajuan_pengiriman`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nomor_resi` (`nomor_resi`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `detail_pengajuan`
--
ALTER TABLE `detail_pengajuan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT untuk tabel `item_pengiriman`
--
ALTER TABLE `item_pengiriman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kategori_barang`
--
ALTER TABLE `kategori_barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `pengajuan_pengiriman`
--
ALTER TABLE `pengajuan_pengiriman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT untuk tabel `pengiriman`
--
ALTER TABLE `pengiriman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `fk_barang_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_barang` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `detail_pengajuan`
--
ALTER TABLE `detail_pengajuan`
  ADD CONSTRAINT `fk_detail_barang` FOREIGN KEY (`barang_id`) REFERENCES `barang` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_detail_pengajuan` FOREIGN KEY (`pengajuan_id`) REFERENCES `pengajuan_pengiriman` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `item_pengiriman`
--
ALTER TABLE `item_pengiriman`
  ADD CONSTRAINT `item_pengiriman_ibfk_1` FOREIGN KEY (`pengiriman_id`) REFERENCES `pengiriman` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD CONSTRAINT `pengiriman_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
