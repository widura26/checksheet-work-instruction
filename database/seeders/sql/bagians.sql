-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 27, 2026 at 01:25 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `central_tools`
--

-- --------------------------------------------------------

--
-- Table structure for table `bagians`
--

CREATE TABLE `bagians` (
  `id` bigint UNSIGNED NOT NULL,
  `departemen_id` bigint UNSIGNED NOT NULL,
  `nama_bagian` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bagians`
--

INSERT INTO `bagians` (`id`, `departemen_id`, `nama_bagian`, `created_at`, `updated_at`, `deleted_at`, `is_active`) VALUES
(1, 1, 'Pengendalian Fabrikasi Banyuwangi', '2026-02-04 15:17:03', '2026-03-06 09:48:18', NULL, 1),
(2, 1, 'Pengendalian Finishing Banyuwangi', '2026-02-04 15:17:03', '2026-03-06 09:48:18', NULL, 1),
(3, 1, 'Pengendalian Pemeliharaan Banyuwangi', '2026-02-04 15:17:03', '2026-03-06 09:48:18', NULL, 1),
(4, 2, 'Metal Working, Machining And Minor Assembling Banyuwangi', '2026-02-04 15:17:03', '2026-02-04 15:17:03', NULL, 1),
(5, 2, 'Sub Assembling Banyuwangi', '2026-02-04 15:17:03', '2026-02-04 15:17:03', NULL, 1),
(6, 2, 'Carbody Banyuwangi', '2026-02-04 15:17:03', '2026-02-04 15:17:03', NULL, 1),
(7, 3, 'Pengecatan Dan Interior Banyuwangi', '2026-02-04 15:17:03', '2026-02-04 15:17:03', NULL, 1),
(8, 3, 'Instalasi Mekanik Banyuwangi', '2026-02-04 15:17:03', '2026-02-04 15:17:03', NULL, 1),
(9, 3, 'Instalasi Elektrik Banyuwangi', '2026-02-04 15:17:03', '2026-03-06 08:18:57', NULL, 1),
(10, 4, 'Distribusi Banyuwangi Banyuwangi', '2026-02-04 15:18:38', '2026-02-04 15:18:38', NULL, 1),
(11, 4, 'Gudang Banyuwangi Banyuwangi', '2026-02-04 15:18:51', '2026-02-04 15:18:51', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bagians`
--
ALTER TABLE `bagians`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bagians_departemen_id_foreign` (`departemen_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bagians`
--
ALTER TABLE `bagians`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bagians`
--
ALTER TABLE `bagians`
  ADD CONSTRAINT `bagians_departemen_id_foreign` FOREIGN KEY (`departemen_id`) REFERENCES `departemens` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
