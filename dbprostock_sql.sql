-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 30 Jun 2025 pada 14.27
-- Versi server: 10.4.27-MariaDB
-- Versi PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dbprostock_sql`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `catatan_penjualan`
--

CREATE TABLE `catatan_penjualan` (
  `id` int(11) NOT NULL,
  `nama_barang` varchar(255) NOT NULL,
  `jumlah_terjual` int(11) NOT NULL,
  `tanggal_penjualan` date NOT NULL,
  `total_harga` int(11) NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `catatan_penjualan`
--

INSERT INTO `catatan_penjualan` (`id`, `nama_barang`, `jumlah_terjual`, `tanggal_penjualan`, `total_harga`, `keterangan`) VALUES
(1, 'Pelampung', 100, '2024-05-27', 10000000, 'PT Maju Terus mantap'),
(2, 'Tali Kapal Kecil', 121, '2024-05-27', 13165374, 'PT Berkah Jaya'),
(3, 'Jangkar', 2, '2025-01-13', 7000000, 'km. natuna jaya'),
(4, 'Tali rumpon', 100, '2025-02-15', 7800, ''),
(5, 'Tali Kapal Kecil', 1, '2025-06-18', 170750, ''),
(6, 'Tali Kapal Kecil', 1, '2025-06-10', 170750, '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `stok_barang`
--

CREATE TABLE `stok_barang` (
  `id` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `asal_barang` varchar(100) NOT NULL,
  `jumlah_barang` int(11) NOT NULL,
  `tanggal_masuk` date NOT NULL,
  `harga_barang` decimal(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `stok_barang`
--

INSERT INTO `stok_barang` (`id`, `nama_barang`, `asal_barang`, `jumlah_barang`, `tanggal_masuk`, `harga_barang`) VALUES
(2, 'Tali Kapal Kecil', 'Semarang', 61, '2011-07-25', '170750.00'),
(3, 'Jangkar', 'Jakarta', 66, '2009-01-12', '86000.00'),
(4, 'Katrol', 'Serang', 22, '2012-03-29', '433060.00'),
(5, 'Pelampung', 'Sidoharjo', 33, '2008-11-28', '162700.00'),
(6, 'Sarung Tangan', 'Jakarta', 100, '2024-05-16', '100000.00'),
(12, 'Pelampung', 'Jakarta', 50, '2024-05-30', '15500000.00'),
(13, 'Tiang Kapal', 'Cilegon', 20, '2024-05-30', '1500000.00'),
(14, 'Tali rumpon', 'Jakarta', 105, '2025-02-11', '7800.00'),
(15, 'tali rafia', 'pekalongan', 105, '2025-02-16', '7800.00'),
(16, 'kapal', 'indonseisa', 3, '2025-06-26', '28000000.00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `nama`, `role`) VALUES
(6, 'dewinurmala886@gmail.com', '$2y$10$7xF0YRtfHZrAJ3Tg2KUoN.RkJ5JJlp673x10TtKBmTChH8cIGWgPu', 'DEWI NURMALA SARI', 'Administrator'),
(8, 'hafidza@gmail.com', '$2y$10$Re1NQce7B34uyfj7c1LaS.sUqcS/cQyLnAmZA5GDMrTrPx09eqgqm', 'hafidza', 'User'),
(9, 'Yuni886@gmail.com', '$2y$10$vO6XlV95LnAOSM2URyN0G.LQW9Zxqi1Ct9cMfvkEQnGtNetQMrm9S', 'YUNITA', 'User'),
(11, 'pengguna@gmail.com', '$2y$10$RbufW8095kzLVhwVU7SlDulv70Kqw0LpgJMH5vb7GUh148qI/bRz2', 'pengguna', 'User');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `catatan_penjualan`
--
ALTER TABLE `catatan_penjualan`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `stok_barang`
--
ALTER TABLE `stok_barang`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `catatan_penjualan`
--
ALTER TABLE `catatan_penjualan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `stok_barang`
--
ALTER TABLE `stok_barang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
