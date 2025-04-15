-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Waktu pembuatan: 15 Apr 2025 pada 22.21
-- Versi server: 8.0.30
-- Versi PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restaurant_order_system`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `galeri`
--

CREATE TABLE `galeri` (
  `id_galeri` int NOT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `keterangan` text,
  `gambar` varchar(255) DEFAULT NULL
);

--
-- Dumping data untuk tabel `galeri`
--

INSERT INTO `galeri` (`id_galeri`, `tanggal_buat`, `keterangan`, `gambar`) VALUES
(1, '2024-09-21 19:58:58', 'Main Dish', '66eeb5021962f.jpg'),
(2, '2024-09-21 19:59:12', 'Japanese Fluffi Pancake', '66eeb5104ae58.jpg'),
(3, '2024-09-21 19:59:27', 'Fruit Tea', '66eeb51f4e0dc.jpg'),
(4, '2024-09-21 19:59:55', 'Western Menu', '66eeb53b9b7cd.jpg'),
(5, '2024-09-21 20:00:09', 'Light Bites', '66eeb54958b58.jpg'),
(6, '2024-09-21 20:00:45', 'VIP Room', '66eeb56d87e1f.jfif'),
(7, '2024-09-21 20:01:01', 'Our Restaurant', '66eeb5a3b5081.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `keranjang`
--

CREATE TABLE `keranjang` (
  `id_keranjang` int NOT NULL,
  `id_user` char(13) DEFAULT NULL,
  `id_pesanan` char(15) DEFAULT NULL,
  `id_menu` int DEFAULT NULL,
  `jumlah` int DEFAULT NULL,
  `status` enum('Belum Dipesan','Sudah Dipesan') NOT NULL
);

--
-- Dumping data untuk tabel `keranjang`
--

INSERT INTO `keranjang` (`id_keranjang`, `id_user`, `id_pesanan`, `id_menu`, `jumlah`, `status`) VALUES
(1, '67fed8b11a538', 'OR67FED9D56C528', 14, 3, 'Sudah Dipesan'),
(2, '67fed8b11a538', 'OR67FED9D56C528', 11, 3, 'Sudah Dipesan'),
(3, '67fed8b11a538', 'OR67FED9D56C528', 8, 3, 'Sudah Dipesan'),
(4, '67fed8b11a538', 'OR67FED9D56C528', 4, 3, 'Sudah Dipesan'),
(5, '67fed8665cee4', 'OR67FEDAB74F57B', 10, 4, 'Sudah Dipesan'),
(6, '67fed8665cee4', 'OR67FEDAB74F57B', 8, 4, 'Sudah Dipesan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `menu`
--

CREATE TABLE `menu` (
  `id_menu` int NOT NULL,
  `tanggal_buat` datetime DEFAULT NULL,
  `nama_menu` varchar(255) DEFAULT NULL,
  `kategori` enum('Dessert','Hot Kitchen','Drink') NOT NULL,
  `harga` bigint DEFAULT NULL,
  `deskripsi` text,
  `status` enum('Tersedia','Tidak Tersedia') NOT NULL,
  `gambar` varchar(255) DEFAULT NULL
);

--
-- Dumping data untuk tabel `menu`
--

INSERT INTO `menu` (`id_menu`, `tanggal_buat`, `nama_menu`, `kategori`, `harga`, `deskripsi`, `status`, `gambar`) VALUES
(1, '2024-09-21 19:32:15', 'Original Pancakes', 'Dessert', 48000, 'Fluffy pancakes with honey cream, maple syrup, and vanilla ice cream.', 'Tidak Tersedia', '66eeaebf4d8f0.jpg'),
(2, '2024-09-21 19:32:56', 'Chizu Berry', 'Dessert', 58000, 'Fluffy pancakes with Chizuki, mixed berries compote, and vanilla ice cream.', 'Tersedia', '66eeaee8aee08.jpg'),
(3, '2024-09-21 19:33:40', 'Strawberry Miss You', 'Dessert', 58000, 'Fluffy pancakes with honey cream, strawberries, strawberry coulis, speculoos crumbles, and vanilla ice cream.', 'Tersedia', '66eeaf14e9333.jpg'),
(4, '2024-09-21 19:35:06', 'Chizu Boba', 'Dessert', 58000, 'Fluffy pancakes with Chizuki, brown sugar boba, and vanilla ice cream.', 'Tersedia', '66eeaf6a9341b.jpg'),
(5, '2024-09-21 19:35:49', 'Choco Lava Boba', 'Dessert', 58000, 'Fluffy pancakes with milk chocolate cream, dark chocolate art, cocoa powder, brown sugar boba, and vanilla ice cream.', 'Tidak Tersedia', '66eeaf9575db7.jpg'),
(6, '2024-09-21 19:36:26', 'La Caramello', 'Dessert', 58000, 'Fluffy pancakes with Chizuki, speculoos crumbles, caramel sauce, strawberry, and vanilla ice cream.', 'Tersedia', '66eeafba31fde.jpg'),
(7, '2024-09-21 19:38:27', 'Garden Salad', 'Hot Kitchen', 38000, 'Fresh mix salad with nuts, and Japanese irigoma dressing.', 'Tersedia', '66eeb033cfced.jpg'),
(8, '2024-09-21 19:39:13', 'Beef Salad', 'Hot Kitchen', 48000, 'Fresh mix salad with beef bulgogi, nuts, and Japanese irigoma dressing.', 'Tersedia', '66eeb06130eb6.jpg'),
(9, '2024-09-21 19:39:55', 'Happy Platter', 'Hot Kitchen', 58000, 'Three-in-one parmesan platter: French fries, chicken karaage, and crispy chicken skin with chilli mayo.', 'Tersedia', '66eeb08bb50a2.png'),
(10, '2024-09-21 19:40:44', 'Beef Hamburg Chizu Rice', 'Hot Kitchen', 72000, 'Cheese baked butter rice, grilled beef hamburg, and bolognese sauce', 'Tersedia', '66eeb0bc104ae.jpg'),
(11, '2024-09-21 19:42:44', 'Fire Beef Bento', 'Hot Kitchen', 65000, 'Hot and spicy beef teppanyaki with tamago, salad, and rice.', 'Tersedia', '66eeb1346fc9f.jpg'),
(12, '2024-09-21 19:43:52', 'Cheese Blast Burger', 'Hot Kitchen', 83000, 'Premium beef burger with melted cheese on top, smoked beef, caramelized onions, housemade sauce, french fries, and salad.', 'Tersedia', '66eeb1789fdb0.jpg'),
(13, '2024-09-21 19:48:35', 'Red Scarlett', 'Drink', 68000, 'Strawberry flavoured soda with fruit bits.', 'Tersedia', '66eeb2930f173.jpg'),
(14, '2024-09-21 19:49:30', 'Purple Magic', 'Drink', 68000, 'Blueflower pea tea soda with peach, lemon and strawberry bits.', 'Tersedia', '66eeb2cacfee1.jpg'),
(15, '2024-09-21 19:50:08', 'Golden Hour', 'Drink', 34000, 'Peach and orange sparkling soda.', 'Tidak Tersedia', '66eeb61844626.jpg');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` char(15) NOT NULL,
  `id_pesanan` char(15) DEFAULT NULL,
  `id_user` char(13) DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `metode_pembayaran` enum('','Cash','Debit Card','QRIS') NOT NULL,
  `jumlah_pembayaran` bigint DEFAULT NULL,
  `status` enum('Unpaid','Paid','Rejected') NOT NULL
);

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_pesanan`, `id_user`, `tanggal`, `metode_pembayaran`, `jumlah_pembayaran`, `status`) VALUES
('ST67FED9D56C532', 'OR67FED9D56C528', '67fed8b11a538', '2025-04-16 06:12:37', 'Cash', 720000, 'Paid'),
('ST67FEDAB74F586', 'OR67FEDAB74F57B', '67fed8665cee4', '2025-04-16 06:16:23', 'Cash', 480000, 'Paid');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` char(15) NOT NULL,
  `id_user` char(13) DEFAULT NULL,
  `tanggal` datetime DEFAULT NULL,
  `tipe_pesanan` enum('Dine In','Takeaway') NOT NULL,
  `nomor_meja` char(7) DEFAULT NULL,
  `total_pembayaran` bigint DEFAULT NULL,
  `status` enum('Pending','Confirmed','In Progress','Completed','Cancelled') NOT NULL
);

--
-- Dumping data untuk tabel `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `id_user`, `tanggal`, `tipe_pesanan`, `nomor_meja`, `total_pembayaran`, `status`) VALUES
('OR67FED9D56C528', '67fed8b11a538', '2025-04-16 06:12:37', 'Dine In', '1', 717000, 'Completed'),
('OR67FEDAB74F57B', '67fed8665cee4', '2025-04-16 06:16:23', 'Dine In', '1', 480000, 'Completed');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` char(13) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `level` enum('Admin','Cashier','User') NOT NULL
);

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `email`, `password`, `level`) VALUES
('67fed8665cee4', 'Administrator', 'admin@gmail.com', '$2y$10$ZFY6rQWQaF/aGDRW5cawTuNMBHzLhJgh/PYDfk/2KlCQF8tVlsyky', 'Admin'),
('67fed8b11a538', 'Novi', 'user@gmail.com', '$2y$10$AYU8rH1qdBlMPJauhHsYZOhc/u8Hsivuw2rh4hg8KeT3u7IRPiy9K', 'User'),
('67feda46f3158', 'Asdianti', 'cashier@gmail.com', '$2y$10$Hmoeu1bCxszraMogPOgDT.T2TvTl9jRWr4ARvjDuXvGgNxBXXklca', 'Cashier');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `galeri`
--
ALTER TABLE `galeri`
  ADD PRIMARY KEY (`id_galeri`);

--
-- Indeks untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  ADD PRIMARY KEY (`id_keranjang`),
  ADD KEY `id_user` (`id_user`,`id_pesanan`,`id_menu`),
  ADD KEY `id_menu` (`id_menu`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indeks untuk tabel `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- Indeks untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD KEY `id_user` (`id_user`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `galeri`
--
ALTER TABLE `galeri`
  MODIFY `id_galeri` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  MODIFY `id_keranjang` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `keranjang`
--
ALTER TABLE `keranjang`
  ADD CONSTRAINT `keranjang_ibfk_1` FOREIGN KEY (`id_menu`) REFERENCES `menu` (`id_menu`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `keranjang_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `keranjang_ibfk_3` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
