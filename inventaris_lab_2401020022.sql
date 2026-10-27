-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 27, 2026 at 04:31 AM
-- Server version: 8.4.7
-- PHP Version: 8.5.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventaris_lab_2401020022`
--

-- --------------------------------------------------------

--
-- Table structure for table `inventaris`
--

DROP TABLE IF EXISTS `inventaris`;
CREATE TABLE IF NOT EXISTS `inventaris` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `kode_barang` char(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_barang` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomor_aset` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tahun_perolehan` smallint UNSIGNED NOT NULL,
  `ruang_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode_barang` (`kode_barang`),
  UNIQUE KEY `nomor_aset` (`nomor_aset`),
  KEY `fk_inventaris_ruang` (`ruang_id`)
) ;

--
-- Dumping data for table `inventaris`
--

INSERT INTO `inventaris` (`id`, `kode_barang`, `nama_barang`, `nomor_aset`, `tahun_perolehan`, `ruang_id`, `created_at`, `updated_at`) VALUES
(1, 'IV00000001', 'Komputer Praktikum', 'AST-LAB-0001', 2026, 1, '2026-10-27 03:44:54', '2026-10-27 03:58:39'),
(2, 'IV00000002', 'Proyektor', 'AST-LAB-0002', 2022, 1, '2026-10-27 03:44:54', '2026-10-27 03:44:54'),
(3, 'IV00000003', 'Router', 'AST-LAB-0003', 2024, 2, '2026-10-27 03:44:54', '2026-10-27 03:44:54');

-- --------------------------------------------------------

--
-- Table structure for table `ruang`
--

DROP TABLE IF EXISTS `ruang`;
CREATE TABLE IF NOT EXISTS `ruang` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_ruang` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nama_ruang` (`nama_ruang`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ruang`
--

INSERT INTO `ruang` (`id`, `nama_ruang`) VALUES
(2, 'Lab Jaringan'),
(1, 'Lab Pemrograman');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventaris`
--
ALTER TABLE `inventaris`
  ADD CONSTRAINT `fk_inventaris_ruang` FOREIGN KEY (`ruang_id`) REFERENCES `ruang` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
