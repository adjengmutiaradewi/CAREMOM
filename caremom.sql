-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 29, 2025 at 05:46 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `caremom`
--

-- --------------------------------------------------------

--
-- Table structure for table `aturan_forward_chaining`
--

CREATE TABLE `aturan_forward_chaining` (
  `id` int(11) NOT NULL,
  `kode_aturan` varchar(10) NOT NULL,
  `nama_aturan` varchar(100) NOT NULL,
  `kondisi` text NOT NULL,
  `aksi` text NOT NULL,
  `keterangan` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aturan_forward_chaining`
--

INSERT INTO `aturan_forward_chaining` (`id`, `kode_aturan`, `nama_aturan`, `kondisi`, `aksi`, `keterangan`, `is_active`, `created_at`) VALUES
(1, 'R001', 'Rekomendasi Asam Folat Trimester 1', 'trimester == 1', 'rekomendasi_suplemen = \"Asam Folat 400-600 mcg/hari\"', 'Wajib untuk semua ibu hamil trimester 1', 1, '2025-09-29 02:35:25'),
(2, 'R002', 'Rekomendasi Zat Besi Anemia', 'hemoglobin < 11 OR riwayat_anemia == true', 'rekomendasi_suplemen = \"Zat Besi 30-60 mg/hari\"', 'Untuk ibu dengan anemia atau Hb rendah', 1, '2025-09-29 02:35:25'),
(3, 'R003', 'Rekomendasi Kalsium', 'asupan_susu == \"tidak\" OR asupan_susu == \"jarang\" OR usia_kehamilan > 20', 'rekomendasi_suplemen = \"Kalsium 1000-1300 mg/hari\"', 'Untuk ibu dengan asupan kalsium kurang', 1, '2025-09-29 02:35:25'),
(4, 'R004', 'Rekomendasi Vitamin D', 'paparan_matahari == \"jarang\"', 'rekomendasi_suplemen = \"Vitamin D 600 IU/hari\"', 'Untuk ibu dengan paparan matahari terbatas', 1, '2025-09-29 02:35:25'),
(5, 'R005', 'Rekomendasi DHA', 'trimester >= 2', 'rekomendasi_suplemen = \"DHA 200-300 mg/hari\"', 'Penting untuk perkembangan otak bayi', 1, '2025-09-29 02:35:25'),
(6, 'R006', 'Kebutuhan Kalori Trimester 1', 'trimester == 1', 'tambahan_kalori = 180', 'Kebutuhan tambahan kalori trimester 1', 1, '2025-09-29 02:35:25'),
(7, 'R007', 'Kebutuhan Kalori Trimester 2-3', 'trimester == 2 OR trimester == 3', 'tambahan_kalori = 300', 'Kebutuhan tambahan kalori trimester 2-3', 1, '2025-09-29 02:35:25'),
(8, 'R008', 'Rekomendasi Protein', 'asupan_protein == \"kurang\"', 'rekomendasi_protein = \"Tingkatkan konsumsi protein 20-30 gram/hari\"', 'Untuk asupan protein yang kurang', 1, '2025-09-29 02:35:25'),
(9, 'R009', 'Rekomendasi Sayur Buah', 'asupan_sayur_buah == \"kurang\"', 'rekomendasi_sayur_buah = \"Konsumsi 3-5 porsi sayur dan buah per hari\"', 'Untuk asupan serat dan vitamin yang kurang', 1, '2025-09-29 02:35:25'),
(10, 'R010', 'Rekomendasi Cairan', 'asupan_cairan < 8', 'rekomendasi_cairan = \"Minum 8-10 gelas air per hari\"', 'Untuk hidrasi yang optimal', 1, '2025-09-29 02:35:25'),
(11, 'R011', 'Rekomendasi Frekuensi Makan', 'frekuensi_makan == \"1-2\"', 'rekomendasi_frekuensi = \"Tingkatkan frekuensi makan menjadi 3 kali utama + 2-3 kali snack\"', 'Untuk pola makan yang lebih teratur', 1, '2025-09-29 02:35:25'),
(12, 'R012', 'Multivitamin Lengkap', 'trimester >= 1 AND asupan_sayur_buah == \"kurang\"', 'rekomendasi_suplemen = \"Multivitamin Prenatal 1 tablet/hari\"', 'Untuk memastikan kecukupan vitamin dan mineral', 1, '2025-09-29 02:35:25');

-- --------------------------------------------------------

--
-- Table structure for table `data_diri`
--

CREATE TABLE `data_diri` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama` varchar(100) NOT NULL,
  `usia` int(11) NOT NULL,
  `usia_kehamilan` int(11) NOT NULL,
  `trimester` int(11) NOT NULL,
  `tinggi_badan` decimal(4,2) NOT NULL,
  `berat_badan_sebelum` decimal(5,2) NOT NULL,
  `berat_badan_sekarang` decimal(5,2) NOT NULL,
  `imt_sebelum` decimal(4,2) DEFAULT NULL,
  `riwayat_kehamilan` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kondisi_kesehatan`
--

CREATE TABLE `kondisi_kesehatan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `riwayat_penyakit` text DEFAULT NULL,
  `keluhan` text DEFAULT NULL,
  `alergi_makanan` text DEFAULT NULL,
  `alergi_suplemen` text DEFAULT NULL,
  `kondisi_khusus` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `master_suplemen`
--

CREATE TABLE `master_suplemen` (
  `id` int(11) NOT NULL,
  `nama_suplemen` varchar(100) NOT NULL,
  `dosis_rekomendasi` varchar(50) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `master_suplemen`
--

INSERT INTO `master_suplemen` (`id`, `nama_suplemen`, `dosis_rekomendasi`, `keterangan`, `is_active`) VALUES
(1, 'Asam Folat', '400-600 mcg/hari', 'Penting untuk perkembangan neural tube bayi', 1),
(2, 'Zat Besi', '30-60 mg/hari', 'Mencegah anemia pada ibu hamil', 1),
(3, 'Kalsium', '1000-1300 mg/hari', 'Untuk perkembangan tulang dan gigi bayi', 1),
(4, 'Vitamin D', '600 IU/hari', 'Membantu penyerapan kalsium', 1),
(5, 'DHA/Omega-3', '200-300 mg/hari', 'Untuk perkembangan otak dan mata bayi', 1),
(6, 'Multivitamin Prenatal', '1 tablet/hari', 'Suplemen lengkap untuk ibu hamil', 1);

-- --------------------------------------------------------

--
-- Table structure for table `pola_makan`
--

CREATE TABLE `pola_makan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `frekuensi_makan` enum('1-2','3','>3') NOT NULL,
  `asupan_protein` enum('kurang','cukup','baik') NOT NULL,
  `asupan_sayur_buah` enum('kurang','cukup','baik') NOT NULL,
  `asupan_susu` enum('tidak','jarang','rutin') NOT NULL,
  `asupan_cairan` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rekomendasi`
--

CREATE TABLE `rekomendasi` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `rekomendasi_gizi` text DEFAULT NULL,
  `rekomendasi_suplemen` text DEFAULT NULL,
  `catatan_khusus` text DEFAULT NULL,
  `total_kalori` int(11) DEFAULT NULL,
  `total_protein` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `standar_kebutuhan_gizi`
--

CREATE TABLE `standar_kebutuhan_gizi` (
  `id` int(11) NOT NULL,
  `trimester` int(11) NOT NULL,
  `kalori_tambahan` int(11) NOT NULL,
  `protein_tambahan` int(11) NOT NULL,
  `zat_besi` int(11) NOT NULL,
  `asam_folat` int(11) NOT NULL,
  `kalsium` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `standar_kebutuhan_gizi`
--

INSERT INTO `standar_kebutuhan_gizi` (`id`, `trimester`, `kalori_tambahan`, `protein_tambahan`, `zat_besi`, `asam_folat`, `kalsium`) VALUES
(1, 1, 180, 17, 27, 600, 1300),
(2, 2, 300, 17, 27, 600, 1300),
(3, 3, 300, 17, 27, 600, 1300);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@caremom.com', 'admin', '2025-09-29 02:35:20'),
(2, 'salma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'salma@example.com', 'user', '2025-09-29 03:16:04'),
(3, 'sari', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sari@example.com', 'user', '2025-09-29 03:24:40'),
(4, 'rina', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'rina@example.com', 'user', '2025-09-29 03:24:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `aturan_forward_chaining`
--
ALTER TABLE `aturan_forward_chaining`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_aturan` (`kode_aturan`);

--
-- Indexes for table `data_diri`
--
ALTER TABLE `data_diri`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `kondisi_kesehatan`
--
ALTER TABLE `kondisi_kesehatan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `master_suplemen`
--
ALTER TABLE `master_suplemen`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pola_makan`
--
ALTER TABLE `pola_makan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rekomendasi`
--
ALTER TABLE `rekomendasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `standar_kebutuhan_gizi`
--
ALTER TABLE `standar_kebutuhan_gizi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `aturan_forward_chaining`
--
ALTER TABLE `aturan_forward_chaining`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `data_diri`
--
ALTER TABLE `data_diri`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kondisi_kesehatan`
--
ALTER TABLE `kondisi_kesehatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `master_suplemen`
--
ALTER TABLE `master_suplemen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pola_makan`
--
ALTER TABLE `pola_makan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rekomendasi`
--
ALTER TABLE `rekomendasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `standar_kebutuhan_gizi`
--
ALTER TABLE `standar_kebutuhan_gizi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `data_diri`
--
ALTER TABLE `data_diri`
  ADD CONSTRAINT `data_diri_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `kondisi_kesehatan`
--
ALTER TABLE `kondisi_kesehatan`
  ADD CONSTRAINT `kondisi_kesehatan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pola_makan`
--
ALTER TABLE `pola_makan`
  ADD CONSTRAINT `pola_makan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rekomendasi`
--
ALTER TABLE `rekomendasi`
  ADD CONSTRAINT `rekomendasi_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
