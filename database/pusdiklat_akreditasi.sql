-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 27, 2024 at 09:10 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pusdiklat_akreditasi`
--

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `mt_items`
--

CREATE TABLE `mt_items` (
  `id` int(2) NOT NULL,
  `id_unsur` int(1) NOT NULL,
  `id_subunsur` int(2) NOT NULL,
  `kode_item` varchar(5) NOT NULL,
  `nama_item` varchar(255) NOT NULL,
  `bobot_item` int(2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mt_items`
--

INSERT INTO `mt_items` (`id`, `id_unsur`, `id_subunsur`, `kode_item`, `nama_item`, `bobot_item`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '1.1.1', 'Kelembagaan', 100, NULL, NULL),
(2, 1, 2, '1.2.1', 'Fasilitator', 35, NULL, NULL),
(3, 1, 2, '1.2.2', 'Pengelola Pelatihan', 20, NULL, NULL),
(4, 1, 2, '1.2.3', 'Pengelola Kelas', 25, NULL, NULL),
(5, 1, 2, '1.2.4', 'Pengelola Sistem Informasi', 15, NULL, NULL),
(6, 1, 2, '1.2.5', 'Analis Kebutuhan Diklat', 5, NULL, NULL),
(7, 1, 3, '1.3.1', 'Sarana Prasarana', 100, NULL, NULL),
(8, 1, 4, '1.4.1', 'Program Pelatihan', 100, NULL, NULL),
(9, 1, 5, '1.5.1', 'Standar Biaya Pelatihan', 100, NULL, NULL),
(10, 1, 6, '1.6.1', 'Standar Mutu', 100, NULL, NULL),
(11, 2, 7, '2.1.1', 'Program Pelatihan dan Pengembangan Kurikulum Pelatihan', 100, NULL, NULL),
(12, 2, 8, '2.2.1', 'Perencanaan Penyelenggaraan Pelatihan', 10, NULL, NULL),
(13, 2, 8, '2.2.2', 'Penyelenggaraan Pelatihan Lainnya', 10, NULL, NULL),
(14, 2, 8, '2.2.3', 'Evaluasi Penyelenggaraan Pelatihan', 80, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mt_jenis_pengajuans`
--

CREATE TABLE `mt_jenis_pengajuans` (
  `id` int(1) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mt_jenis_pengajuans`
--

INSERT INTO `mt_jenis_pengajuans` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'Pranata Komputer', NULL, NULL),
(2, 'Statistisi', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mt_pangkat`
--

CREATE TABLE `mt_pangkat` (
  `id` int(2) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mt_pangkat`
--

INSERT INTO `mt_pangkat` (`id`, `nama`, `created_at`, `updated_at`) VALUES
(1, 'Pengatur Muda/IIa', NULL, NULL),
(2, 'Pengatur Muda Tingkat I/IIb', NULL, NULL),
(3, 'Pengatur/IIc', NULL, NULL),
(4, 'Pengatur Tingkat I/IId', NULL, NULL),
(5, 'Penata Muda/IIIa', NULL, NULL),
(6, 'Penata Muda Tingkat I/IIIb', NULL, NULL),
(7, 'Penata/IIIc', NULL, NULL),
(8, 'Penata Tingkat I/IIId', NULL, NULL),
(9, 'Pembina/IVa', NULL, NULL),
(10, 'Pembina Tingkat I/IVb', NULL, NULL),
(11, 'Pembina Muda/IVc', NULL, NULL),
(12, 'Pembina Madya/IVd', NULL, NULL),
(13, 'Pembina Utama/IVe', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mt_program_dokumens`
--

CREATE TABLE `mt_program_dokumens` (
  `id` int(11) NOT NULL,
  `step` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mt_program_dokumens`
--

INSERT INTO `mt_program_dokumens` (`id`, `step`, `nama`, `created_at`, `updated_at`) VALUES
(1, 1, 'Jadwal Pelatihan SPK', '2024-05-20 06:21:14', '2024-05-20 06:21:14'),
(2, 1, 'Jenis Pengembangan Kurikulum', '2024-05-20 06:21:14', '2024-05-20 06:21:23'),
(3, 1, 'Kurikulum Pelatihan', '2024-05-20 06:22:02', '2024-05-20 06:22:02'),
(4, 2, 'Jadwal Kegiatan', '2024-05-20 06:22:02', '2024-05-20 06:22:02'),
(7, 2, 'Laporan Penyelenggaraan', '2024-05-20 06:24:27', '2024-05-20 06:24:27'),
(8, 2, 'Sertifikat Pelatihan', '2024-05-20 06:24:27', '2024-05-20 06:24:27'),
(9, 2, 'Rekapitulasi Pre & Post Tes', '2024-05-20 06:25:04', '2024-05-20 06:25:04'),
(10, 2, 'Rekapitulasi Tes Materi', '2024-05-20 06:25:04', '2024-05-20 06:25:04'),
(11, 2, 'Dokumentasi Kegiatan', '2024-05-20 06:25:24', '2024-05-20 06:25:24'),
(12, 3, 'Kalender Diklat', '2024-05-20 06:25:44', '2024-05-20 06:25:44'),
(13, 3, 'Materi/Bahan Ajar', '2024-05-20 06:25:44', '2024-05-20 06:25:44'),
(14, 3, 'Jadwal Pelatihan', '2024-05-20 06:25:58', '2024-05-20 06:25:58'),
(15, 3, 'Daftar Program', '2024-05-20 06:25:58', '2024-05-20 06:25:58'),
(16, 4, 'Hasil IKP', '2024-05-20 06:26:33', '2024-05-20 06:26:33'),
(17, 4, 'Berita Acara Ujian Sertifikasi', '2024-05-20 06:26:33', '2024-05-20 06:26:33');

-- --------------------------------------------------------

--
-- Table structure for table `mt_subunsurs`
--

CREATE TABLE `mt_subunsurs` (
  `id` int(11) NOT NULL,
  `id_unsur` int(1) NOT NULL,
  `kode_subunsur` varchar(3) NOT NULL,
  `nama_subunsur` varchar(255) NOT NULL,
  `bobot_subunsur` int(2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mt_subunsurs`
--

INSERT INTO `mt_subunsurs` (`id`, `id_unsur`, `kode_subunsur`, `nama_subunsur`, `bobot_subunsur`, `created_at`, `updated_at`) VALUES
(1, 1, '1.1', 'Sub Unsur Kelembagaan', 5, NULL, NULL),
(2, 1, '1.2', 'Tenaga Kediklatan', 40, NULL, NULL),
(3, 1, '1.3', 'Fasilitas Pelatihan', 25, NULL, NULL),
(4, 1, '1.4', 'Program Kerja', 10, NULL, NULL),
(5, 1, '1.5', 'Pembiayaan', 10, NULL, NULL),
(6, 1, '1.6', 'Penjaminan Mutu', 10, NULL, NULL),
(7, 2, '2.1', 'Kurikulum', 25, NULL, NULL),
(8, 2, '2.2', 'Program dan Pengelolaan', 75, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mt_unsurs`
--

CREATE TABLE `mt_unsurs` (
  `id` int(1) NOT NULL,
  `nama_unsur` varchar(255) NOT NULL,
  `bobot_unsur` int(2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mt_unsurs`
--

INSERT INTO `mt_unsurs` (`id`, `nama_unsur`, `bobot_unsur`, `created_at`, `updated_at`) VALUES
(1, 'Organisasi', 75, NULL, NULL),
(2, 'Program dan Pengelolaan', 25, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mt_wilayah`
--

CREATE TABLE `mt_wilayah` (
  `id` int(5) NOT NULL,
  `kode` varchar(13) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `level` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mt_wilayah`
--

INSERT INTO `mt_wilayah` (`id`, `kode`, `nama`, `level`) VALUES
(1, '11', 'ACEH', 1),
(2, '11.01', 'KAB. ACEH SELATAN', 2),
(3, '11.02', 'KAB. ACEH TENGGARA', 2),
(4, '11.03', 'KAB. ACEH TIMUR', 2),
(5, '11.04', 'KAB. ACEH TENGAH', 2),
(6, '11.05', 'KAB. ACEH BARAT', 2),
(7, '11.06', 'KAB. ACEH BESAR', 2),
(8, '11.07', 'KAB. PIDIE', 2),
(9, '11.08', 'KAB. ACEH UTARA', 2),
(10, '11.09', 'KAB. SIMEULUE', 2),
(11, '11.10', 'KAB. ACEH SINGKIL', 2),
(12, '11.11', 'KAB. BIREUEN', 2),
(13, '11.12', 'KAB. ACEH BARAT DAYA', 2),
(14, '11.13', 'KAB. GAYO LUES', 2),
(15, '11.14', 'KAB. ACEH JAYA', 2),
(16, '11.15', 'KAB. NAGAN RAYA', 2),
(17, '11.16', 'KAB. ACEH TAMIANG', 2),
(18, '11.17', 'KAB. BENER MERIAH', 2),
(19, '11.18', 'KAB. PIDIE JAYA', 2),
(20, '11.71', 'KOTA BANDA ACEH', 2),
(21, '11.72', 'KOTA SABANG', 2),
(22, '11.73', 'KOTA LHOKSEUMAWE', 2),
(23, '11.74', 'KOTA LANGSA', 2),
(24, '11.75', 'KOTA SUBULUSSALAM', 2),
(25, '12', 'SUMATERA UTARA', 1),
(26, '12.01', 'KAB. TAPANULI TENGAH', 2),
(27, '12.02', 'KAB. TAPANULI UTARA', 2),
(28, '12.03', 'KAB. TAPANULI SELATAN', 2),
(29, '12.04', 'KAB. NIAS', 2),
(30, '12.05', 'KAB. LANGKAT', 2),
(31, '12.06', 'KAB. KARO', 2),
(32, '12.07', 'KAB. DELI SERDANG', 2),
(33, '12.08', 'KAB. SIMALUNGUN', 2),
(34, '12.09', 'KAB. ASAHAN', 2),
(35, '12.10', 'KAB. LABUHANBATU', 2),
(36, '12.11', 'KAB. DAIRI', 2),
(37, '12.12', 'KAB. TOBA', 2),
(38, '12.13', 'KAB. MANDAILING NATAL', 2),
(39, '12.14', 'KAB. NIAS SELATAN', 2),
(40, '12.15', 'KAB. PAKPAK BHARAT', 2),
(41, '12.16', 'KAB. HUMBANG HASUNDUTAN', 2),
(42, '12.17', 'KAB. SAMOSIR', 2),
(43, '12.18', 'KAB. SERDANG BEDAGAI', 2),
(44, '12.19', 'KAB. BATU BARA', 2),
(45, '12.20', 'KAB. PADANG LAWAS UTARA', 2),
(46, '12.21', 'KAB. PADANG LAWAS', 2),
(47, '12.22', 'KAB. LABUHANBATU SELATAN', 2),
(48, '12.23', 'KAB. LABUHANBATU UTARA', 2),
(49, '12.24', 'KAB. NIAS UTARA', 2),
(50, '12.25', 'KAB. NIAS BARAT', 2),
(51, '12.71', 'KOTA MEDAN', 2),
(52, '12.72', 'KOTA PEMATANGSIANTAR', 2),
(53, '12.73', 'KOTA SIBOLGA', 2),
(54, '12.74', 'KOTA TANJUNG BALAI', 2),
(55, '12.75', 'KOTA BINJAI', 2),
(56, '12.76', 'KOTA TEBING TINGGI', 2),
(57, '12.77', 'KOTA PADANGSIDIMPUAN', 2),
(58, '12.78', 'KOTA GUNUNGSITOLI', 2),
(59, '13', 'SUMATERA BARAT', 1),
(60, '13.01', 'KAB. PESISIR SELATAN', 2),
(61, '13.02', 'KAB. SOLOK', 2),
(62, '13.03', 'KAB. SIJUNJUNG', 2),
(63, '13.04', 'KAB. TANAH DATAR', 2),
(64, '13.05', 'KAB. PADANG PARIAMAN', 2),
(65, '13.06', 'KAB. AGAM', 2),
(66, '13.07', 'KAB. LIMA PULUH KOTA', 2),
(67, '13.08', 'KAB. PASAMAN', 2),
(68, '13.09', 'KAB. KEPULAUAN MENTAWAI', 2),
(69, '13.10', 'KAB. DHARMASRAYA', 2),
(70, '13.11', 'KAB. SOLOK SELATAN', 2),
(71, '13.12', 'KAB. PASAMAN BARAT', 2),
(72, '13.71', 'KOTA PADANG', 2),
(73, '13.72', 'KOTA SOLOK', 2),
(74, '13.73', 'KOTA SAWAHLUNTO', 2),
(75, '13.74', 'KOTA PADANG PANJANG', 2),
(76, '13.75', 'KOTA BUKITTINGGI', 2),
(77, '13.76', 'KOTA PAYAKUMBUH', 2),
(78, '13.77', 'KOTA PARIAMAN', 2),
(79, '14', 'RIAU', 1),
(80, '14.01', 'KAB. KAMPAR', 2),
(81, '14.02', 'KAB. INDRAGIRI HULU', 2),
(82, '14.03', 'KAB. BENGKALIS', 2),
(83, '14.04', 'KAB. INDRAGIRI HILIR', 2),
(84, '14.05', 'KAB. PELALAWAN', 2),
(85, '14.06', 'KAB. ROKAN HULU', 2),
(86, '14.07', 'KAB. ROKAN HILIR', 2),
(87, '14.08', 'KAB. SIAK', 2),
(88, '14.09', 'KAB. KUANTAN SINGINGI', 2),
(89, '14.10', 'KAB. KEPULAUAN MERANTI', 2),
(90, '14.71', 'KOTA PEKANBARU', 2),
(91, '14.72', 'KOTA DUMAI', 2),
(92, '15', 'JAMBI', 1),
(93, '15.01', 'KAB. KERINCI', 2),
(94, '15.02', 'KAB. MERANGIN', 2),
(95, '15.03', 'KAB. SAROLANGUN', 2),
(96, '15.04', 'KAB. BATANGHARI', 2),
(97, '15.05', 'KAB. MUARO JAMBI', 2),
(98, '15.06', 'KAB. TANJUNG JABUNG BARAT', 2),
(99, '15.07', 'KAB. TANJUNG JABUNG TIMUR', 2),
(100, '15.08', 'KAB. BUNGO', 2),
(101, '15.09', 'KAB. TEBO', 2),
(102, '15.71', 'KOTA JAMBI', 2),
(103, '15.72', 'KOTA SUNGAI PENUH', 2),
(104, '16', 'SUMATERA SELATAN', 1),
(105, '16.01', 'KAB. OGAN KOMERING ULU', 2),
(106, '16.02', 'KAB. OGAN KOMERING ILIR', 2),
(107, '16.03', 'KAB. MUARA ENIM', 2),
(108, '16.04', 'KAB. LAHAT', 2),
(109, '16.05', 'KAB. MUSI RAWAS', 2),
(110, '16.06', 'KAB. MUSI BANYUASIN', 2),
(111, '16.07', 'KAB. BANYUASIN', 2),
(112, '16.08', 'KAB. OGAN KOMERING ULU TIMUR', 2),
(113, '16.09', 'KAB. OGAN KOMERING ULU SELATAN', 2),
(114, '16.10', 'KAB. OGAN ILIR', 2),
(115, '16.11', 'KAB. EMPAT LAWANG', 2),
(116, '16.12', 'KAB. PENUKAL ABAB LEMATANG ILIR', 2),
(117, '16.13', 'KAB. MUSI RAWAS UTARA', 2),
(118, '16.71', 'KOTA PALEMBANG', 2),
(119, '16.72', 'KOTA PAGAR ALAM', 2),
(120, '16.73', 'KOTA LUBUK LINGGAU', 2),
(121, '16.74', 'KOTA PRABUMULIH', 2),
(122, '17', 'BENGKULU', 1),
(123, '17.01', 'KAB. BENGKULU SELATAN', 2),
(124, '17.02', 'KAB. REJANG LEBONG', 2),
(125, '17.03', 'KAB. BENGKULU UTARA', 2),
(126, '17.04', 'KAB. KAUR', 2),
(127, '17.05', 'KAB. SELUMA', 2),
(128, '17.06', 'KAB. MUKO MUKO', 2),
(129, '17.07', 'KAB. LEBONG', 2),
(130, '17.08', 'KAB. KEPAHIANG', 2),
(131, '17.09', 'KAB. BENGKULU TENGAH', 2),
(132, '17.71', 'KOTA BENGKULU', 2),
(133, '18', 'LAMPUNG', 1),
(134, '18.01', 'KAB. LAMPUNG SELATAN', 2),
(135, '18.02', 'KAB. LAMPUNG TENGAH', 2),
(136, '18.03', 'KAB. LAMPUNG UTARA', 2),
(137, '18.04', 'KAB. LAMPUNG BARAT', 2),
(138, '18.05', 'KAB. TULANG BAWANG', 2),
(139, '18.06', 'KAB. TANGGAMUS', 2),
(140, '18.07', 'KAB. LAMPUNG TIMUR', 2),
(141, '18.08', 'KAB. WAY KANAN', 2),
(142, '18.09', 'KAB. PESAWARAN', 2),
(143, '18.10', 'KAB. PRINGSEWU', 2),
(144, '18.11', 'KAB. MESUJI', 2),
(145, '18.12', 'KAB. TULANG BAWANG BARAT', 2),
(146, '18.13', 'KAB. PESISIR BARAT', 2),
(147, '18.71', 'KOTA BANDAR LAMPUNG', 2),
(148, '18.72', 'KOTA METRO', 2),
(149, '19', 'KEPULAUAN BANGKA BELITUNG', 1),
(150, '19.01', 'KAB. BANGKA', 2),
(151, '19.02', 'KAB. BELITUNG', 2),
(152, '19.03', 'KAB. BANGKA SELATAN', 2),
(153, '19.04', 'KAB. BANGKA TENGAH', 2),
(154, '19.05', 'KAB. BANGKA BARAT', 2),
(155, '19.06', 'KAB. BELITUNG TIMUR', 2),
(156, '19.71', 'KOTA PANGKAL PINANG', 2),
(157, '21', 'KEPULAUAN RIAU', 1),
(158, '21.01', 'KAB. BINTAN', 2),
(159, '21.02', 'KAB. KARIMUN', 2),
(160, '21.03', 'KAB. NATUNA', 2),
(161, '21.04', 'KAB. LINGGA', 2),
(162, '21.05', 'KAB. KEPULAUAN ANAMBAS', 2),
(163, '21.71', 'KOTA BATAM', 2),
(164, '21.72', 'KOTA TANJUNG PINANG', 2),
(165, '31', 'DKI JAKARTA', 1),
(166, '31.01', 'KAB. ADM. KEP. SERIBU', 2),
(167, '31.71', 'KOTA ADM. JAKARTA PUSAT', 2),
(168, '31.72', 'KOTA ADM. JAKARTA UTARA', 2),
(169, '31.73', 'KOTA ADM. JAKARTA BARAT', 2),
(170, '31.74', 'KOTA ADM. JAKARTA SELATAN', 2),
(171, '31.75', 'KOTA ADM. JAKARTA TIMUR', 2),
(172, '32', 'JAWA BARAT', 1),
(173, '32.01', 'KAB. BOGOR', 2),
(174, '32.02', 'KAB. SUKABUMI', 2),
(175, '32.03', 'KAB. CIANJUR', 2),
(176, '32.04', 'KAB. BANDUNG', 2),
(177, '32.05', 'KAB. GARUT', 2),
(178, '32.06', 'KAB. TASIKMALAYA', 2),
(179, '32.07', 'KAB. CIAMIS', 2),
(180, '32.08', 'KAB. KUNINGAN', 2),
(181, '32.09', 'KAB. CIREBON', 2),
(182, '32.10', 'KAB. MAJALENGKA', 2),
(183, '32.11', 'KAB. SUMEDANG', 2),
(184, '32.12', 'KAB. INDRAMAYU', 2),
(185, '32.13', 'KAB. SUBANG', 2),
(186, '32.14', 'KAB. PURWAKARTA', 2),
(187, '32.15', 'KAB. KARAWANG', 2),
(188, '32.16', 'KAB. BEKASI', 2),
(189, '32.17', 'KAB. BANDUNG BARAT', 2),
(190, '32.18', 'KAB. PANGANDARAN', 2),
(191, '32.71', 'KOTA BOGOR', 2),
(192, '32.72', 'KOTA SUKABUMI', 2),
(193, '32.73', 'KOTA BANDUNG', 2),
(194, '32.74', 'KOTA CIREBON', 2),
(195, '32.75', 'KOTA BEKASI', 2),
(196, '32.76', 'KOTA DEPOK', 2),
(197, '32.77', 'KOTA CIMAHI', 2),
(198, '32.78', 'KOTA TASIKMALAYA', 2),
(199, '32.79', 'KOTA BANJAR', 2),
(200, '33', 'JAWA TENGAH', 1),
(201, '33.01', 'KAB. CILACAP', 2),
(202, '33.02', 'KAB. BANYUMAS', 2),
(203, '33.03', 'KAB. PURBALINGGA', 2),
(204, '33.04', 'KAB. BANJARNEGARA', 2),
(205, '33.05', 'KAB. KEBUMEN', 2),
(206, '33.06', 'KAB. PURWOREJO', 2),
(207, '33.07', 'KAB. WONOSOBO', 2),
(208, '33.08', 'KAB. MAGELANG', 2),
(209, '33.09', 'KAB. BOYOLALI', 2),
(210, '33.10', 'KAB. KLATEN', 2),
(211, '33.11', 'KAB. SUKOHARJO', 2),
(212, '33.12', 'KAB. WONOGIRI', 2),
(213, '33.13', 'KAB. KARANGANYAR', 2),
(214, '33.14', 'KAB. SRAGEN', 2),
(215, '33.15', 'KAB. GROBOGAN', 2),
(216, '33.16', 'KAB. BLORA', 2),
(217, '33.17', 'KAB. REMBANG', 2),
(218, '33.18', 'KAB. PATI', 2),
(219, '33.19', 'KAB. KUDUS', 2),
(220, '33.20', 'KAB. JEPARA', 2),
(221, '33.21', 'KAB. DEMAK', 2),
(222, '33.22', 'KAB. SEMARANG', 2),
(223, '33.23', 'KAB. TEMANGGUNG', 2),
(224, '33.24', 'KAB. KENDAL', 2),
(225, '33.25', 'KAB. BATANG', 2),
(226, '33.26', 'KAB. PEKALONGAN', 2),
(227, '33.27', 'KAB. PEMALANG', 2),
(228, '33.28', 'KAB. TEGAL', 2),
(229, '33.29', 'KAB. BREBES', 2),
(230, '33.71', 'KOTA MAGELANG', 2),
(231, '33.72', 'KOTA SURAKARTA', 2),
(232, '33.73', 'KOTA SALATIGA', 2),
(233, '33.74', 'KOTA SEMARANG', 2),
(234, '33.75', 'KOTA PEKALONGAN', 2),
(235, '33.76', 'KOTA TEGAL', 2),
(236, '34', 'DAERAH ISTIMEWA YOGYAKARTA', 1),
(237, '34.01', 'KAB. KULON PROGO', 2),
(238, '34.02', 'KAB. BANTUL', 2),
(239, '34.03', 'KAB. GUNUNGKIDUL', 2),
(240, '34.04', 'KAB. SLEMAN', 2),
(241, '34.71', 'KOTA YOGYAKARTA', 2),
(242, '35', 'JAWA TIMUR', 1),
(243, '35.01', 'KAB. PACITAN', 2),
(244, '35.02', 'KAB. PONOROGO', 2),
(245, '35.03', 'KAB. TRENGGALEK', 2),
(246, '35.04', 'KAB. TULUNGAGUNG', 2),
(247, '35.05', 'KAB. BLITAR', 2),
(248, '35.06', 'KAB. KEDIRI', 2),
(249, '35.07', 'KAB. MALANG', 2),
(250, '35.08', 'KAB. LUMAJANG', 2),
(251, '35.09', 'KAB. JEMBER', 2),
(252, '35.10', 'KAB. BANYUWANGI', 2),
(253, '35.11', 'KAB. BONDOWOSO', 2),
(254, '35.12', 'KAB. SITUBONDO', 2),
(255, '35.13', 'KAB. PROBOLINGGO', 2),
(256, '35.14', 'KAB. PASURUAN', 2),
(257, '35.15', 'KAB. SIDOARJO', 2),
(258, '35.16', 'KAB. MOJOKERTO', 2),
(259, '35.17', 'KAB. JOMBANG', 2),
(260, '35.18', 'KAB. NGANJUK', 2),
(261, '35.19', 'KAB. MADIUN', 2),
(262, '35.20', 'KAB. MAGETAN', 2),
(263, '35.21', 'KAB. NGAWI', 2),
(264, '35.22', 'KAB. BOJONEGORO', 2),
(265, '35.23', 'KAB. TUBAN', 2),
(266, '35.24', 'KAB. LAMONGAN', 2),
(267, '35.25', 'KAB. GRESIK', 2),
(268, '35.26', 'KAB. BANGKALAN', 2),
(269, '35.27', 'KAB. SAMPANG', 2),
(270, '35.28', 'KAB. PAMEKASAN', 2),
(271, '35.29', 'KAB. SUMENEP', 2),
(272, '35.71', 'KOTA KEDIRI', 2),
(273, '35.72', 'KOTA BLITAR', 2),
(274, '35.73', 'KOTA MALANG', 2),
(275, '35.74', 'KOTA PROBOLINGGO', 2),
(276, '35.75', 'KOTA PASURUAN', 2),
(277, '35.76', 'KOTA MOJOKERTO', 2),
(278, '35.77', 'KOTA MADIUN', 2),
(279, '35.78', 'KOTA SURABAYA', 2),
(280, '35.79', 'KOTA BATU', 2),
(281, '36', 'BANTEN', 1),
(282, '36.01', 'KAB. PANDEGLANG', 2),
(283, '36.02', 'KAB. LEBAK', 2),
(284, '36.03', 'KAB. TANGERANG', 2),
(285, '36.04', 'KAB. SERANG', 2),
(286, '36.71', 'KOTA TANGERANG', 2),
(287, '36.72', 'KOTA CILEGON', 2),
(288, '36.73', 'KOTA SERANG', 2),
(289, '36.74', 'KOTA TANGERANG SELATAN', 2),
(290, '51', 'BALI', 1),
(291, '51.01', 'KAB. JEMBRANA', 2),
(292, '51.02', 'KAB. TABANAN', 2),
(293, '51.03', 'KAB. BADUNG', 2),
(294, '51.04', 'KAB. GIANYAR', 2),
(295, '51.05', 'KAB. KLUNGKUNG', 2),
(296, '51.06', 'KAB. BANGLI', 2),
(297, '51.07', 'KAB. KARANGASEM', 2),
(298, '51.08', 'KAB. BULELENG', 2),
(299, '51.71', 'KOTA DENPASAR', 2),
(300, '52', 'NUSA TENGGARA BARAT', 1),
(301, '52.01', 'KAB. LOMBOK BARAT', 2),
(302, '52.02', 'KAB. LOMBOK TENGAH', 2),
(303, '52.03', 'KAB. LOMBOK TIMUR', 2),
(304, '52.04', 'KAB. SUMBAWA', 2),
(305, '52.05', 'KAB. DOMPU', 2),
(306, '52.06', 'KAB. BIMA', 2),
(307, '52.07', 'KAB. SUMBAWA BARAT', 2),
(308, '52.08', 'KAB. LOMBOK UTARA', 2),
(309, '52.71', 'KOTA MATARAM', 2),
(310, '52.72', 'KOTA BIMA', 2),
(311, '53', 'NUSA TENGGARA TIMUR', 1),
(312, '53.01', 'KAB. KUPANG', 2),
(313, '53.02', 'KAB TIMOR TENGAH SELATAN', 2),
(314, '53.03', 'KAB. TIMOR TENGAH UTARA', 2),
(315, '53.04', 'KAB. BELU', 2),
(316, '53.05', 'KAB. ALOR', 2),
(317, '53.06', 'KAB. FLORES TIMUR', 2),
(318, '53.07', 'KAB. SIKKA', 2),
(319, '53.08', 'KAB. ENDE', 2),
(320, '53.09', 'KAB. NGADA', 2),
(321, '53.10', 'KAB. MANGGARAI', 2),
(322, '53.11', 'KAB. SUMBA TIMUR', 2),
(323, '53.12', 'KAB. SUMBA BARAT', 2),
(324, '53.13', 'KAB. LEMBATA', 2),
(325, '53.14', 'KAB. ROTE NDAO', 2),
(326, '53.15', 'KAB. MANGGARAI BARAT', 2),
(327, '53.16', 'KAB. NAGEKEO', 2),
(328, '53.17', 'KAB. SUMBA TENGAH', 2),
(329, '53.18', 'KAB. SUMBA BARAT DAYA', 2),
(330, '53.19', 'KAB. MANGGARAI TIMUR', 2),
(331, '53.20', 'KAB. SABU RAIJUA', 2),
(332, '53.21', 'KAB. MALAKA', 2),
(333, '53.71', 'KOTA KUPANG', 2),
(334, '61', 'KALIMANTAN BARAT', 1),
(335, '61.01', 'KAB. SAMBAS', 2),
(336, '61.02', 'KAB. MEMPAWAH', 2),
(337, '61.03', 'KAB. SANGGAU', 2),
(338, '61.04', 'KAB. KETAPANG', 2),
(339, '61.05', 'KAB. SINTANG', 2),
(340, '61.06', 'KAB. KAPUAS HULU', 2),
(341, '61.07', 'KAB. BENGKAYANG', 2),
(342, '61.08', 'KAB. LANDAK', 2),
(343, '61.09', 'KAB. SEKADAU', 2),
(344, '61.10', 'KAB. MELAWI', 2),
(345, '61.11', 'KAB. KAYONG UTARA', 2),
(346, '61.12', 'KAB. KUBU RAYA', 2),
(347, '61.71', 'KOTA PONTIANAK', 2),
(348, '61.72', 'KOTA SINGKAWANG', 2),
(349, '62', 'KALIMANTAN TENGAH', 1),
(350, '62.01', 'KAB. KOTAWARINGIN BARAT', 2),
(351, '62.02', 'KAB. KOTAWARINGIN TIMUR', 2),
(352, '62.03', 'KAB. KAPUAS', 2),
(353, '62.04', 'KAB. BARITO SELATAN', 2),
(354, '62.05', 'KAB. BARITO UTARA', 2),
(355, '62.06', 'KAB. KATINGAN', 2),
(356, '62.07', 'KAB. SERUYAN', 2),
(357, '62.08', 'KAB. SUKAMARA', 2),
(358, '62.09', 'KAB. LAMANDAU', 2),
(359, '62.10', 'KAB. GUNUNG MAS', 2),
(360, '62.11', 'KAB. PULANG PISAU', 2),
(361, '62.12', 'KAB. MURUNG RAYA', 2),
(362, '62.13', 'KAB. BARITO TIMUR', 2),
(363, '62.71', 'KOTA PALANGKARAYA', 2),
(364, '63', 'KALIMANTAN SELATAN', 1),
(365, '63.01', 'KAB. TANAH LAUT', 2),
(366, '63.02', 'KAB. KOTABARU', 2),
(367, '63.03', 'KAB. BANJAR', 2),
(368, '63.04', 'KAB. BARITO KUALA', 2),
(369, '63.05', 'KAB. TAPIN', 2),
(370, '63.06', 'KAB. HULU SUNGAI SELATAN', 2),
(371, '63.07', 'KAB. HULU SUNGAI TENGAH', 2),
(372, '63.08', 'KAB. HULU SUNGAI UTARA', 2),
(373, '63.09', 'KAB. TABALONG', 2),
(374, '63.10', 'KAB. TANAH BUMBU', 2),
(375, '63.11', 'KAB. BALANGAN', 2),
(376, '63.71', 'KOTA BANJARMASIN', 2),
(377, '63.72', 'KOTA BANJARBARU', 2),
(378, '64', 'KALIMANTAN TIMUR', 1),
(379, '64.01', 'KAB. PASER', 2),
(380, '64.02', 'KAB. KUTAI KARTANEGARA', 2),
(381, '64.03', 'KAB. BERAU', 2),
(382, '64.07', 'KAB. KUTAI BARAT', 2),
(383, '64.08', 'KAB. KUTAI TIMUR', 2),
(384, '64.09', 'KAB. PENAJAM PASER UTARA', 2),
(385, '64.11', 'KAB. MAHAKAM ULU', 2),
(386, '64.71', 'KOTA BALIKPAPAN', 2),
(387, '64.72', 'KOTA SAMARINDA', 2),
(388, '64.74', 'KOTA BONTANG', 2),
(389, '65', 'KALIMANTAN UTARA', 1),
(390, '65.01', 'KAB. BULUNGAN', 2),
(391, '65.02', 'KAB. MALINAU', 2),
(392, '65.03', 'KAB. NUNUKAN', 2),
(393, '65.04', 'KAB. TANA TIDUNG', 2),
(394, '65.71', 'KOTA TARAKAN', 2),
(395, '71', 'SULAWESI UTARA', 1),
(396, '71.01', 'KAB. BOLAANG MONGONDOW', 2),
(397, '71.02', 'KAB. MINAHASA', 2),
(398, '71.03', 'KAB. KEPULAUAN SANGIHE', 2),
(399, '71.04', 'KAB. KEPULAUAN TALAUD', 2),
(400, '71.05', 'KAB. MINAHASA SELATAN', 2),
(401, '71.06', 'KAB. MINAHASA UTARA', 2),
(402, '71.07', 'KAB. MINAHASA TENGGARA', 2),
(403, '71.08', 'KAB. BOLAANG MONGONDOW UTARA', 2),
(404, '71.09', 'KAB. KEP. SIAU TAGULANDANG BIARO', 2),
(405, '71.10', 'KAB. BOLAANG MONGONDOW TIMUR', 2),
(406, '71.11', 'KAB. BOLAANG MONGONDOW SELATAN', 2),
(407, '71.71', 'KOTA MANADO', 2),
(408, '71.72', 'KOTA BITUNG', 2),
(409, '71.73', 'KOTA TOMOHON', 2),
(410, '71.74', 'KOTA KOTAMOBAGU', 2),
(411, '72', 'SULAWESI TENGAH', 1),
(412, '72.01', 'KAB. BANGGAI', 2),
(413, '72.02', 'KAB. POSO', 2),
(414, '72.03', 'KAB. DONGGALA', 2),
(415, '72.04', 'KAB. TOLI TOLI', 2),
(416, '72.05', 'KAB. BUOL', 2),
(417, '72.06', 'KAB. MOROWALI', 2),
(418, '72.07', 'KAB. BANGGAI KEPULAUAN', 2),
(419, '72.08', 'KAB. PARIGI MOUTONG', 2),
(420, '72.09', 'KAB. TOJO UNA UNA', 2),
(421, '72.10', 'KAB. SIGI', 2),
(422, '72.11', 'KAB. BANGGAI LAUT', 2),
(423, '72.12', 'KAB. MOROWALI UTARA', 2),
(424, '72.71', 'KOTA PALU', 2),
(425, '73', 'SULAWESI SELATAN', 1),
(426, '73.01', 'KAB. KEPULAUAN SELAYAR', 2),
(427, '73.02', 'KAB. BULUKUMBA', 2),
(428, '73.03', 'KAB. BANTAENG', 2),
(429, '73.04', 'KAB. JENEPONTO', 2),
(430, '73.05', 'KAB. TAKALAR', 2),
(431, '73.06', 'KAB. GOWA', 2),
(432, '73.07', 'KAB. SINJAI', 2),
(433, '73.08', 'KAB. BONE', 2),
(434, '73.09', 'KAB. MAROS', 2),
(435, '73.10', 'KAB. PANGKAJENE KEPULAUAN', 2),
(436, '73.11', 'KAB. BARRU', 2),
(437, '73.12', 'KAB. SOPPENG', 2),
(438, '73.13', 'KAB. WAJO', 2),
(439, '73.14', 'KAB. SIDENRENG RAPPANG', 2),
(440, '73.15', 'KAB. PINRANG', 2),
(441, '73.16', 'KAB. ENREKANG', 2),
(442, '73.17', 'KAB. LUWU', 2),
(443, '73.18', 'KAB. TANA TORAJA', 2),
(444, '73.22', 'KAB. LUWU UTARA', 2),
(445, '73.24', 'KAB. LUWU TIMUR', 2),
(446, '73.26', 'KAB. TORAJA UTARA', 2),
(447, '73.71', 'KOTA MAKASSAR', 2),
(448, '73.72', 'KOTA PARE PARE', 2),
(449, '73.73', 'KOTA PALOPO', 2),
(450, '74', 'SULAWESI TENGGARA', 1),
(451, '74.01', 'KAB. KOLAKA', 2),
(452, '74.02', 'KAB. KONAWE', 2),
(453, '74.03', 'KAB. MUNA', 2),
(454, '74.04', 'KAB. BUTON', 2),
(455, '74.05', 'KAB. KONAWE SELATAN', 2),
(456, '74.06', 'KAB. BOMBANA', 2),
(457, '74.07', 'KAB. WAKATOBI', 2),
(458, '74.08', 'KAB. KOLAKA UTARA', 2),
(459, '74.09', 'KAB. KONAWE UTARA', 2),
(460, '74.10', 'KAB. BUTON UTARA', 2),
(461, '74.11', 'KAB. KOLAKA TIMUR', 2),
(462, '74.12', 'KAB. KONAWE KEPULAUAN', 2),
(463, '74.13', 'KAB. MUNA BARAT', 2),
(464, '74.14', 'KAB. BUTON TENGAH', 2),
(465, '74.15', 'KAB. BUTON SELATAN', 2),
(466, '74.71', 'KOTA KENDARI', 2),
(467, '74.72', 'KOTA BAU BAU', 2),
(468, '75', 'GORONTALO', 1),
(469, '75.01', 'KAB. GORONTALO', 2),
(470, '75.02', 'KAB. BOALEMO', 2),
(471, '75.03', 'KAB. BONE BOLANGO', 2),
(472, '75.04', 'KAB. PAHUWATO', 2),
(473, '75.05', 'KAB. GORONTALO UTARA', 2),
(474, '75.71', 'KOTA GORONTALO', 2),
(475, '76', 'SULAWESI BARAT', 1),
(476, '76.01', 'KAB. PASANGKAYU', 2),
(477, '76.02', 'KAB. MAMUJU', 2),
(478, '76.03', 'KAB. MAMASA', 2),
(479, '76.04', 'KAB. POLEWALI MANDAR', 2),
(480, '76.05', 'KAB. MAJENE', 2),
(481, '76.06', 'KAB. MAMUJU TENGAH', 2),
(482, '81', 'MALUKU', 1),
(483, '81.01', 'KAB. MALUKU TENGAH', 2),
(484, '81.02', 'KAB. MALUKU TENGGARA', 2),
(485, '81.03', 'KAB. KEPULAUAN TANIMBAR', 2),
(486, '81.04', 'KAB. BURU', 2),
(487, '81.05', 'KAB. SERAM BAGIAN TIMUR', 2),
(488, '81.06', 'KAB. SERAM BAGIAN BARAT', 2),
(489, '81.07', 'KAB. KEPULAUAN ARU', 2),
(490, '81.08', 'KAB. MALUKU BARAT DAYA', 2),
(491, '81.09', 'KAB. BURU SELATAN', 2),
(492, '81.71', 'KOTA AMBON', 2),
(493, '81.72', 'KOTA TUAL', 2),
(494, '82', 'MALUKU UTARA', 1),
(495, '82.01', 'KAB. HALMAHERA BARAT', 2),
(496, '82.02', 'KAB. HALMAHERA TENGAH', 2),
(497, '82.03', 'KAB. HALMAHERA UTARA', 2),
(498, '82.04', 'KAB. HALMAHERA SELATAN', 2),
(499, '82.05', 'KAB. KEPULAUAN SULA', 2),
(500, '82.06', 'KAB. HALMAHERA TIMUR', 2),
(501, '82.07', 'KAB. PULAU MOROTAI', 2),
(502, '82.08', 'KAB. PULAU TALIABU', 2),
(503, '82.71', 'KOTA TERNATE', 2),
(504, '82.72', 'KOTA TIDORE KEPULAUAN', 2),
(505, '91', 'PAPUA', 1),
(506, '91.03', 'KAB. JAYAPURA', 2),
(507, '91.05', 'KAB. KEPULAUAN YAPEN', 2),
(508, '91.06', 'KAB. BIAK NUMFOR', 2),
(509, '91.10', 'KAB. SARMI', 2),
(510, '91.11', 'KAB. KEEROM', 2),
(511, '91.15', 'KAB. WAROPEN', 2),
(512, '91.19', 'KAB. SUPIORI', 2),
(513, '91.20', 'KAB. MAMBERAMO RAYA', 2),
(514, '91.71', 'KOTA JAYAPURA', 2),
(515, '92', 'PAPUA BARAT', 1),
(516, '92.01', 'KAB. SORONG', 2),
(517, '92.02', 'KAB. MANOKWARI', 2),
(518, '92.03', 'KAB. FAK FAK', 2),
(519, '92.04', 'KAB. SORONG SELATAN', 2),
(520, '92.05', 'KAB. RAJA AMPAT', 2),
(521, '92.06', 'KAB. TELUK BINTUNI', 2),
(522, '92.07', 'KAB. TELUK WONDAMA', 2),
(523, '92.08', 'KAB. KAIMANA', 2),
(524, '92.09', 'KAB. TAMBRAUW', 2),
(525, '92.10', 'KAB. MAYBRAT', 2),
(526, '92.11', 'KAB. MANOKWARI SELATAN', 2),
(527, '92.12', 'KAB. PEGUNUNGAN ARFAK', 2),
(528, '92.71', 'KOTA SORONG', 2),
(529, '93', 'PAPUA SELATAN', 1),
(530, '93.01', 'KAB. MERAUKE', 2),
(531, '93.02', 'KAB. BOVEN DIGOEL', 2),
(532, '93.03', 'KAB. MAPPI', 2),
(533, '93.04', 'KAB. ASMAT', 2),
(534, '94', 'PAPUA TENGAH', 1),
(535, '94.01', 'KAB. NABIRE', 2),
(536, '94.02', 'KAB. PUNCAK JAYA', 2),
(537, '94.03', 'KAB. PANIAI', 2),
(538, '94.04', 'KAB. MIMIKA', 2),
(539, '94.05', 'KAB. PUNCAK', 2),
(540, '94.06', 'KAB. DOGIYAI', 2),
(541, '94.07', 'KAB. INTAN JAYA', 2),
(542, '94.08', 'KAB. DEIYAI', 2),
(543, '95', 'PAPUA PEGUNUNGAN', 1),
(544, '95.01', 'KAB. JAYAWIJAYA', 2),
(545, '95.02', 'KAB. PEGUNUNGAN BINTANG', 2),
(546, '95.03', 'KAB. YAHUKIMO', 2),
(547, '95.04', 'KAB. TOLIKARA', 2),
(548, '95.05', 'KAB. MAMBERAMO TENGAH', 2),
(549, '95.06', 'KAB. YALIMO', 2),
(550, '95.07', 'KAB. LANNY JAYA', 2),
(551, '95.08', 'KAB. NDUGA', 2);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tb_fasilitas`
--

CREATE TABLE `tb_fasilitas` (
  `id` int(11) NOT NULL,
  `id_profile` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `status` int(1) NOT NULL,
  `tipe` int(1) NOT NULL,
  `keterangan` varchar(255) NOT NULL,
  `path_foto` varchar(255) NOT NULL,
  `path_dokumen` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_fasilitas`
--

INSERT INTO `tb_fasilitas` (`id`, `id_profile`, `nama`, `jumlah`, `status`, `tipe`, `keterangan`, `path_foto`, `path_dokumen`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 'Test Fasilitas', 2, 1, 1, 'Keterangan Test', 'dokumen_fasilitas/3-1716540226png', 'dokumen_profile/3-1716540514pdf', '2024-05-24 01:43:46', '2024-05-25 12:40:59', '2024-05-24 01:51:09'),
(2, 2, 'Test', 2, 1, 1, 'Asdasd', 'dokumen_fasilitas/3-1716541111jpg', 'dokumen_profile/3-1716541111pdf', '2024-05-24 01:58:31', '2024-05-25 12:41:01', NULL),
(3, 2, 'Test Fasilitas 3', 2, 1, 1, 'ASdasdasdasdas', 'dokumen_fasilitas/3-1716641089png', 'dokumen_profile/3-1716641103pdf', '2024-05-25 05:44:49', '2024-05-25 05:45:03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_pelatihans`
--

CREATE TABLE `tb_pelatihans` (
  `id` int(11) NOT NULL,
  `id_pengajuan` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `angkatan` varchar(10) NOT NULL,
  `tahun` varchar(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pelatihans`
--

INSERT INTO `tb_pelatihans` (`id`, `id_pengajuan`, `nama`, `angkatan`, `tahun`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 16, 'Nama Pelatihan Prakom', '1', '2023', NULL, NULL, NULL),
(2, 16, 'Nama Pelatihan Prakom', '2', '2023', NULL, NULL, NULL),
(3, 14, 'Nama Pelatihan Statistisi', '1', '2023', NULL, NULL, NULL),
(4, 17, 'Diklat Prakom', '1', '2024', NULL, NULL, NULL),
(5, 18, 'Pelatihan Coba', '4', '2023', '2024-05-23 08:34:15', '2024-05-23 08:34:15', NULL),
(6, 19, 'Pelatihan Dua Test', '12', '2024', '2024-05-25 06:26:29', '2024-05-25 06:26:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_pelatihan_dokumens`
--

CREATE TABLE `tb_pelatihan_dokumens` (
  `id` int(11) NOT NULL,
  `id_pelatihan` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `tipe` varchar(10) NOT NULL,
  `path_dokumen` varchar(255) NOT NULL,
  `dokumen_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pelatihan_dokumens`
--

INSERT INTO `tb_pelatihan_dokumens` (`id`, `id_pelatihan`, `nama`, `tipe`, `path_dokumen`, `dokumen_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 18, 'Test Dokumen', 'pdf', 'dokumen_pelatihan/3-1716446448pdf', 1, '2024-05-22 23:40:48', '2024-05-25 13:54:29', NULL),
(2, 6, 'Test Dokumen 2', 'pdf', 'dokumen_pelatihan/3-1716645731pdf', 2, '2024-05-25 07:02:11', '2024-05-25 07:05:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_pelatihan_tenagas`
--

CREATE TABLE `tb_pelatihan_tenagas` (
  `id` int(11) NOT NULL,
  `id_pelatihan` int(11) NOT NULL,
  `id_tenaga` int(11) NOT NULL,
  `jenis_tenaga` int(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pelatihan_tenagas`
--

INSERT INTO `tb_pelatihan_tenagas` (`id`, `id_pelatihan`, `id_tenaga`, `jenis_tenaga`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 6, 2, 2, '2024-05-25 07:26:01', '2024-05-25 07:43:57', '2024-05-25 07:43:57');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pengajuans`
--

CREATE TABLE `tb_pengajuans` (
  `id` int(11) NOT NULL,
  `ttd_token` varchar(64) DEFAULT NULL,
  `id_profile` int(11) NOT NULL,
  `surat_permohonan` varchar(255) NOT NULL,
  `surat_akreditasi_lembaga` varchar(255) DEFAULT NULL,
  `verifikasi_permohonan` int(1) NOT NULL,
  `id_jenis` int(1) NOT NULL,
  `id_asesor1` int(11) DEFAULT NULL,
  `id_asesor2` int(11) DEFAULT NULL,
  `id_asesor3` int(11) DEFAULT NULL,
  `pra_visit_asesor1` int(1) DEFAULT NULL,
  `pra_visit_asesor2` int(1) DEFAULT NULL,
  `pra_visit_asesor3` int(1) DEFAULT NULL,
  `paska_visit_asesor1` int(1) DEFAULT NULL,
  `paska_visit_asesor2` int(1) DEFAULT NULL,
  `paska_visit_asesor3` int(1) DEFAULT NULL,
  `predikat` varchar(1) DEFAULT NULL,
  `nilai` int(11) DEFAULT NULL,
  `graded_at` timestamp NULL DEFAULT NULL,
  `expired_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pengajuans`
--

INSERT INTO `tb_pengajuans` (`id`, `id_profile`, `surat_permohonan`, `surat_akreditasi_lembaga`, `verifikasi_permohonan`, `id_jenis`, `id_asesor1`, `id_asesor2`, `id_asesor3`, `pra_visit_asesor1`, `pra_visit_asesor2`, `pra_visit_asesor3`, `paska_visit_asesor1`, `paska_visit_asesor2`, `paska_visit_asesor3`, `predikat`, `nilai`, `graded_at`, `expired_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(13, 1, 'surat_permohonan/1-1708592125pdf', 'surat_permohonan/1-1708592125pdf', 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-02-22 01:55:25', '2024-02-22 03:13:27', NULL),
(14, 1, 'surat_permohonan/1-1708594914pdf', 'surat_permohonan/1-1708594914pdf', 1, 2, NULL, 2, NULL, NULL, 1, NULL, NULL, 1, NULL, 'A', 4, NULL, NULL, '2024-02-22 02:41:54', '2024-02-28 14:37:40', NULL),
(15, 1, 'surat_permohonan/1-1709124465pdf', NULL, 2, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-02-28 05:47:45', '2024-02-28 05:47:52', NULL),
(16, 1, 'surat_permohonan/1-1709124520pdf', 'surat_akreditasi_lembaga/1-1709124528pdf', 1, 1, 2, NULL, NULL, 1, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, '2024-02-28 05:48:40', '2024-02-28 20:30:50', NULL),
(17, 3, 'surat_permohonan/3-1716202176pdf', NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-05-20 03:49:36', '2024-05-20 03:49:36', NULL),
(18, 3, 'surat_permohonan/3-1716458959pdf', NULL, 1, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-05-23 03:09:19', '2024-05-23 08:26:27', NULL),
(19, 2, 'surat_permohonan/3-1716642986pdf', 'surat_akreditasi_lembaga/3-1716642986pdf', 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2024-05-25 06:16:26', '2024-05-25 07:50:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_penilaians`
--

CREATE TABLE `tb_penilaians` (
  `id` int(11) NOT NULL,
  `id_asesor` int(11) NOT NULL,
  `id_pengajuan` int(11) NOT NULL,
  `id_item_penilaian` varchar(5) NOT NULL,
  `pra_paska` varchar(5) NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `nilai` int(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_penilaians`
--

INSERT INTO `tb_penilaians` (`id`, `id_asesor`, `id_pengajuan`, `id_item_penilaian`, `pra_paska`, `keterangan`, `nilai`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 2, 14, '1', 'pra', NULL, 3, '2024-02-28 12:45:17', '2024-02-28 13:44:55', NULL),
(4, 2, 14, '1', 'paska', 'asdassdad', 4, '2024-02-28 12:45:17', '2024-02-28 13:45:00', NULL),
(5, 2, 14, '2', 'pra', NULL, 3, '2024-02-28 13:10:11', '2024-02-28 13:10:11', NULL),
(6, 2, 14, '2', 'paska', NULL, 3, '2024-02-28 13:10:11', '2024-02-28 13:10:11', NULL),
(7, 2, 14, '3', 'pra', NULL, 4, '2024-02-28 13:10:16', '2024-02-28 13:10:16', NULL),
(8, 2, 14, '3', 'paska', NULL, 4, '2024-02-28 13:10:16', '2024-02-28 13:10:16', NULL),
(9, 2, 14, '4', 'pra', NULL, 2, '2024-02-28 13:10:29', '2024-02-28 13:10:29', NULL),
(10, 2, 14, '4', 'paska', NULL, 2, '2024-02-28 13:10:29', '2024-02-28 13:10:29', NULL),
(11, 2, 14, '5', 'pra', NULL, 4, '2024-02-28 13:10:35', '2024-02-28 13:10:35', NULL),
(12, 2, 14, '5', 'paska', NULL, 4, '2024-02-28 13:10:35', '2024-02-28 13:10:35', NULL),
(13, 2, 14, '6', 'pra', NULL, 3, '2024-02-28 13:10:41', '2024-02-28 13:10:41', NULL),
(14, 2, 14, '6', 'paska', NULL, 3, '2024-02-28 13:10:41', '2024-02-28 13:10:41', NULL),
(15, 2, 14, '7', 'pra', NULL, 2, '2024-02-28 13:10:46', '2024-02-28 13:10:46', NULL),
(16, 2, 14, '7', 'paska', NULL, 2, '2024-02-28 13:10:46', '2024-02-28 13:10:46', NULL),
(17, 2, 14, '8', 'pra', NULL, 4, '2024-02-28 13:10:51', '2024-02-28 13:10:51', NULL),
(18, 2, 14, '8', 'paska', NULL, 4, '2024-02-28 13:10:51', '2024-02-28 13:10:51', NULL),
(19, 2, 14, '9', 'pra', NULL, 3, '2024-02-28 13:10:56', '2024-02-28 13:10:56', NULL),
(20, 2, 14, '9', 'paska', NULL, 3, '2024-02-28 13:10:56', '2024-02-28 13:10:56', NULL),
(21, 2, 14, '10', 'pra', NULL, 4, '2024-02-28 13:11:01', '2024-02-28 13:11:01', NULL),
(22, 2, 14, '10', 'paska', NULL, 4, '2024-02-28 13:11:01', '2024-02-28 13:11:01', NULL),
(23, 2, 14, '11', 'pra', NULL, 3, '2024-02-28 13:11:06', '2024-02-28 13:11:06', NULL),
(24, 2, 14, '11', 'paska', NULL, 3, '2024-02-28 13:11:06', '2024-02-28 13:11:06', NULL),
(25, 2, 14, '12', 'pra', NULL, 4, '2024-02-28 13:11:11', '2024-02-28 13:11:11', NULL),
(26, 2, 14, '12', 'paska', NULL, 4, '2024-02-28 13:11:11', '2024-02-28 13:11:11', NULL),
(27, 2, 14, '13', 'pra', NULL, 3, '2024-02-28 13:11:16', '2024-02-28 13:11:16', NULL),
(28, 2, 14, '13', 'paska', NULL, 3, '2024-02-28 13:11:16', '2024-02-28 13:11:16', NULL),
(29, 2, 14, '14', 'pra', NULL, 1, '2024-02-28 13:21:20', '2024-02-28 13:21:20', NULL),
(30, 2, 14, '14', 'paska', NULL, 1, '2024-02-28 13:21:20', '2024-02-28 13:21:20', NULL),
(31, 2, 16, '1', 'pra', NULL, 4, '2024-02-28 20:29:20', '2024-02-28 20:29:20', NULL),
(32, 2, 16, '1', 'paska', NULL, 4, '2024-02-28 20:29:20', '2024-02-28 20:29:20', NULL),
(33, 2, 16, '2', 'pra', NULL, 3, '2024-02-28 20:29:25', '2024-02-28 20:29:25', NULL),
(34, 2, 16, '2', 'paska', NULL, 3, '2024-02-28 20:29:25', '2024-02-28 20:29:25', NULL),
(35, 2, 16, '3', 'pra', NULL, 4, '2024-02-28 20:29:29', '2024-02-28 20:29:29', NULL),
(36, 2, 16, '3', 'paska', NULL, 4, '2024-02-28 20:29:29', '2024-02-28 20:29:29', NULL),
(37, 2, 16, '4', 'pra', NULL, 3, '2024-02-28 20:29:38', '2024-02-28 20:29:38', NULL),
(38, 2, 16, '4', 'paska', NULL, 3, '2024-02-28 20:29:38', '2024-02-28 20:29:38', NULL),
(39, 2, 16, '5', 'pra', NULL, 4, '2024-02-28 20:29:43', '2024-02-28 20:29:43', NULL),
(40, 2, 16, '5', 'paska', NULL, 4, '2024-02-28 20:29:43', '2024-02-28 20:29:43', NULL),
(41, 2, 16, '6', 'pra', NULL, 4, '2024-02-28 20:29:47', '2024-02-28 20:29:47', NULL),
(42, 2, 16, '6', 'paska', NULL, 4, '2024-02-28 20:29:47', '2024-02-28 20:29:47', NULL),
(43, 2, 16, '7', 'pra', NULL, 3, '2024-02-28 20:29:52', '2024-02-28 20:29:52', NULL),
(44, 2, 16, '7', 'paska', NULL, 3, '2024-02-28 20:29:52', '2024-02-28 20:29:52', NULL),
(45, 2, 16, '8', 'pra', NULL, 4, '2024-02-28 20:29:57', '2024-02-28 20:29:57', NULL),
(46, 2, 16, '8', 'paska', NULL, 4, '2024-02-28 20:29:57', '2024-02-28 20:29:57', NULL),
(47, 2, 16, '9', 'pra', NULL, 3, '2024-02-28 20:30:02', '2024-02-28 20:30:02', NULL),
(48, 2, 16, '9', 'paska', NULL, 3, '2024-02-28 20:30:02', '2024-02-28 20:30:02', NULL),
(49, 2, 16, '10', 'pra', NULL, 4, '2024-02-28 20:30:07', '2024-02-28 20:30:07', NULL),
(50, 2, 16, '10', 'paska', NULL, 4, '2024-02-28 20:30:07', '2024-02-28 20:30:07', NULL),
(51, 2, 16, '11', 'pra', NULL, 3, '2024-02-28 20:30:11', '2024-02-28 20:30:11', NULL),
(52, 2, 16, '11', 'paska', NULL, 3, '2024-02-28 20:30:11', '2024-02-28 20:30:11', NULL),
(53, 2, 16, '12', 'pra', NULL, 3, '2024-02-28 20:30:17', '2024-02-28 20:30:17', NULL),
(54, 2, 16, '12', 'paska', NULL, 3, '2024-02-28 20:30:17', '2024-02-28 20:30:17', NULL),
(55, 2, 16, '13', 'pra', NULL, 2, '2024-02-28 20:30:24', '2024-02-28 20:30:24', NULL),
(56, 2, 16, '13', 'paska', NULL, 2, '2024-02-28 20:30:24', '2024-02-28 20:30:24', NULL),
(57, 2, 16, '14', 'pra', NULL, 4, '2024-02-28 20:30:29', '2024-02-28 20:30:29', NULL),
(58, 2, 16, '14', 'paska', NULL, 4, '2024-02-28 20:30:29', '2024-02-28 20:30:29', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tb_profile_lembagas`
--

CREATE TABLE `tb_profile_lembagas` (
  `id` int(11) NOT NULL,
  `nama_pimpinan` varchar(100) DEFAULT NULL,
  `nip_pimpinan` varchar(20) DEFAULT NULL,
  `jabatan_pimpinan` varchar(100) DEFAULT NULL,
  `unit_kerja` varchar(100) DEFAULT NULL,
  `alamat_unit_kerja` varchar(255) DEFAULT NULL,
  `path_surat_pernyataan_pimpinan` varchar(255) DEFAULT NULL,
  `nama_lembaga` varchar(100) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `faksimili` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(100) DEFAULT NULL,
  `provinsi` int(11) DEFAULT NULL,
  `kabupaten_kota` int(11) DEFAULT NULL,
  `alamat_lembaga` varchar(255) DEFAULT NULL,
  `nomor_sk_pemerintah` varchar(100) DEFAULT NULL,
  `tanggal_sk_pemerintah` date DEFAULT NULL,
  `tentang_sk_pemerintah` varchar(100) DEFAULT NULL,
  `path_sk_pemerintah` varchar(255) DEFAULT NULL,
  `no_surat_izin_operasional` varchar(100) DEFAULT NULL,
  `tanggal_surat_izin_operasional` date DEFAULT NULL,
  `penerbit_surat_izin_operasional` varchar(100) DEFAULT NULL,
  `path_surat_izin_operasional` varchar(255) DEFAULT NULL,
  `nomor_akte_pendirian` varchar(100) DEFAULT NULL,
  `tanggal_akte_pendirian` date DEFAULT NULL,
  `ttd_akte_pendirian` varchar(100) DEFAULT NULL,
  `path_akte_pendirian` varchar(255) DEFAULT NULL,
  `path_rencana_keiatan` varchar(255) DEFAULT NULL,
  `path_kegiatan_diklat` varchar(255) DEFAULT NULL,
  `path_pembiayaan` varchar(255) DEFAULT NULL,
  `path_sop_perencanaan` varchar(255) DEFAULT NULL,
  `path_sop_pelaksanaan` varchar(255) DEFAULT NULL,
  `path_sop_evalap` varchar(255) DEFAULT NULL,
  `is_lock` int(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_profile_lembagas`
--

INSERT INTO `tb_profile_lembagas` (`id`, `nama_pimpinan`, `nip_pimpinan`, `jabatan_pimpinan`, `unit_kerja`, `alamat_unit_kerja`, `path_surat_pernyataan_pimpinan`, `nama_lembaga`, `telepon`, `faksimili`, `email`, `website`, `provinsi`, `kabupaten_kota`, `alamat_lembaga`, `nomor_sk_pemerintah`, `tanggal_sk_pemerintah`, `tentang_sk_pemerintah`, `path_sk_pemerintah`, `no_surat_izin_operasional`, `tanggal_surat_izin_operasional`, `penerbit_surat_izin_operasional`, `path_surat_izin_operasional`, `nomor_akte_pendirian`, `tanggal_akte_pendirian`, `ttd_akte_pendirian`, `path_akte_pendirian`, `path_rencana_keiatan`, `path_kegiatan_diklat`, `path_pembiayaan`, `path_sop_perencanaan`, `path_sop_pelaksanaan`, `path_sop_evalap`, `is_lock`, `created_at`, `updated_at`) VALUES
(1, NULL, NULL, NULL, NULL, NULL, NULL, 'Lembaga 1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-05-23 02:33:44', '2024-05-25 11:49:05'),
(2, 'asda', '123', NULL, NULL, NULL, 'dokumen_profile/3-1716532184pdf', 'Lembaga 2', NULL, NULL, NULL, NULL, 59, 72, NULL, '123', NULL, NULL, NULL, NULL, '2024-05-24', NULL, NULL, NULL, '2024-05-24', NULL, NULL, 'dokumen_profile/3-1716641306pdf', NULL, 'dokumen_profile/3-1716541090pdf', NULL, NULL, NULL, 1, '2024-05-23 02:33:44', '2024-05-27 00:06:57'),
(3, NULL, NULL, NULL, NULL, NULL, NULL, 'Lembaga 3', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-05-25 04:51:30', '2024-05-25 04:51:30'),
(4, NULL, NULL, NULL, NULL, NULL, NULL, 'Lembaga 4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2024-05-25 04:52:31', '2024-05-25 04:52:31');

-- --------------------------------------------------------

--
-- Table structure for table `tb_riwayat_jabatan`
--

CREATE TABLE `tb_riwayat_jabatan` (
  `id` int(11) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `tugas` text NOT NULL,
  `periode` varchar(100) NOT NULL,
  `instansi` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `tenaga_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_riwayat_jabatan`
--

INSERT INTO `tb_riwayat_jabatan` (`id`, `jabatan`, `tugas`, `periode`, `instansi`, `created_at`, `updated_at`, `deleted_at`, `tenaga_id`) VALUES
(1, 'prakom mada 12', 'kortim 12', '2010-2024', 'pusdiklat 12', '2024-05-15 15:30:51', '2024-05-19 11:28:02', '2024-05-19 11:28:02', 2),
(2, 'prakom mada baru 1', 'kortim 11', '2010-2024', 'pusdiklat 11', '2024-05-15 15:30:51', '2024-05-19 21:56:56', NULL, 2),
(3, 'prakom mada 3', 'kortim', '2010-2024', 'pusdiklat', '2024-05-15 15:30:51', '2024-05-17 11:39:47', NULL, 2),
(4, 'prakom mada 4', 'kortim', '2010-2024', 'pusdiklat', '2024-05-15 15:30:51', '2024-05-17 11:39:51', NULL, 2),
(5, 'prakom mada 5', 'kortim', '2010-2024', 'pusdiklat', '2024-05-15 15:30:51', '2024-05-17 11:39:54', NULL, 3),
(6, 'prakom mada 6', 'kortim', '2010-2024', 'pusdiklat', '2024-05-15 15:30:51', '2024-05-17 11:39:58', NULL, 3),
(7, 'prakom mada 7', 'kortim', '2010-2024', 'pusdiklat', '2024-05-15 15:30:51', '2024-05-17 11:40:01', NULL, 4),
(8, 'prakom mada 8', 'kortim', '2010-2024', 'pusdiklat', '2024-05-15 15:30:51', '2024-05-17 11:40:04', NULL, 4),
(9, 'prakom mada 9', 'kortim', '2010-2024', 'pusdiklat', '2024-05-15 15:30:51', '2024-05-17 11:40:08', NULL, 5),
(10, 'prakom mada 10', 'kortim', '2010-2024', 'pusdiklat', '2024-05-15 15:30:51', '2024-05-17 11:40:11', NULL, 5),
(11, 'prakom mada 11', 'kortim', '2010-2024', 'pusdiklat', '2024-05-15 15:30:51', '2024-05-17 11:40:14', NULL, 6),
(12, 'prakom mada 12', 'kortim', '2010-2024', 'pusdiklat', '2024-05-15 15:30:51', '2024-05-17 11:40:17', NULL, 6),
(13, 'owner', 'nasi goreng', '2021-2022', 'umkm', '2024-05-19 09:06:45', '2024-05-19 09:06:45', NULL, 1),
(14, 'owner2', 'nasi goreng2', '2021-2022', 'umkm2', '2024-05-19 09:07:11', '2024-05-19 09:07:11', NULL, 1),
(15, 'prakom mada 123', 'kortim 123', '2021-2023', 'pusdiklat 123', '2024-05-19 11:29:18', '2024-05-19 11:29:18', NULL, 2),
(16, 'prakom mada 22', 'nasi goreng', '2021-2022', 'pusdiklat 12', '2024-05-19 13:22:02', '2024-05-19 13:22:15', '2024-05-19 13:22:15', 10),
(17, 'jabatan', 'tugas', '2024', 'pusdiklat', '2024-05-19 21:01:01', '2024-05-19 21:01:01', NULL, 2),
(18, 'madyaaa auww2', 'nyetrika', '2021-2024', 'bps ri', '2024-05-19 21:50:27', '2024-05-19 21:50:51', '2024-05-19 21:50:51', 2),
(19, 'jabatan 2', 'tugas 2', '2023-2024', 'bps ri', '2024-05-19 21:54:29', '2024-05-19 21:54:29', NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tb_riwayat_kerja`
--

CREATE TABLE `tb_riwayat_kerja` (
  `id` int(11) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `tugas` text NOT NULL,
  `tahun` varchar(10) NOT NULL,
  `instansi` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `tenaga_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_riwayat_kerja`
--

INSERT INTO `tb_riwayat_kerja` (`id`, `jabatan`, `tugas`, `tahun`, `instansi`, `created_at`, `updated_at`, `deleted_at`, `tenaga_id`) VALUES
(1, 'prakom mada', 'kortim', '2023', 'pusdiklat', '2024-05-15 15:30:51', '2024-05-19 18:11:48', NULL, 2),
(2, 'prakom mada', 'kortim', '2023', 'pusdiklat', '2024-05-15 15:30:51', '2024-05-19 18:11:53', NULL, 2),
(3, 'prakom mada', 'kortim', '2024', 'pusdiklat', '2024-05-15 15:30:51', '2024-05-19 18:11:56', NULL, 3),
(4, 'prakom mada', 'kortim', '2024', 'pusdiklat', '2024-05-15 15:30:51', '2024-05-19 18:12:00', NULL, 3),
(5, 'prakom mada kerja', 'kerja kerja', '2023', 'kerja kerja', '2024-05-19 11:33:58', '2024-05-19 18:35:17', NULL, 2),
(6, 'kerja 2', 'kerja 2', '2024', 'kerja 2', '2024-05-19 11:35:38', '2024-05-19 11:36:56', '2024-05-19 11:36:56', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tb_riwayat_pelatihan`
--

CREATE TABLE `tb_riwayat_pelatihan` (
  `id` int(11) NOT NULL,
  `pelatihan` varchar(100) NOT NULL,
  `penyelenggara` varchar(100) NOT NULL,
  `tahun` varchar(10) NOT NULL,
  `sertifikasi` varchar(100) NOT NULL,
  `keterangan` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `tenaga_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_riwayat_pelatihan`
--

INSERT INTO `tb_riwayat_pelatihan` (`id`, `pelatihan`, `penyelenggara`, `tahun`, `sertifikasi`, `keterangan`, `created_at`, `updated_at`, `deleted_at`, `tenaga_id`) VALUES
(1, 'Digitalent', 'Kominfo', '2021', 'Sertifikat Kelulusan Pelatihan', '', '2024-05-15 15:39:50', '2024-05-15 15:39:50', NULL, 1),
(2, 'Digitalent', 'Kominfo', '2021', 'Sertifikat Kelulusan Pelatihan', 'oke punya', '2024-05-15 15:39:58', '2024-05-19 12:21:11', NULL, 2),
(3, 'Digitalent', 'Kominfo', '2021', 'Sertifikat Kelulusan Pelatihan', '', '2024-05-15 15:39:50', '2024-05-15 15:39:50', NULL, 3),
(4, 'Digitalent', 'Kominfo', '2021', 'Sertifikat Kelulusan Pelatihan', '', '2024-05-15 15:39:58', '2024-05-15 15:39:58', NULL, 4),
(5, 'Digitalent', 'Kominfo', '2021', 'Sertifikat Kelulusan Pelatihan', '', '2024-05-15 15:39:50', '2024-05-15 15:39:50', NULL, 5),
(6, 'Digitalent', 'Kominfo', '2021', 'Sertifikat Kelulusan Pelatihan', '', '2024-05-15 15:39:58', '2024-05-15 15:39:58', NULL, 6),
(7, 'Digitalent', 'Kominfo', '2021', 'Sertifikat Kelulusan Pelatihan', '', '2024-05-15 15:39:50', '2024-05-15 15:39:50', NULL, 7),
(8, 'Digitalent', 'Kominfo', '2021', 'Sertifikat Kelulusan Pelatihan', '', '2024-05-15 15:39:58', '2024-05-15 15:39:58', NULL, 8),
(9, 'Digitalent', 'Kominfo', '2021', 'Sertifikat Kelulusan Pelatihan', '', '2024-05-15 15:39:50', '2024-05-15 15:39:50', NULL, 9),
(10, 'Digitalent', 'Kominfo', '2021', 'Sertifikat Kelulusan Pelatihan', '', '2024-05-15 15:39:58', '2024-05-15 15:39:58', NULL, 10),
(11, 'Digitalent', 'Kominfo', '2021', 'Sertifikat Kelulusan Pelatihan', '', '2024-05-15 15:39:50', '2024-05-15 15:39:50', NULL, 11),
(12, 'Digitalent', 'Kominfo', '2021', 'Sertifikat Kelulusan Pelatihan', '', '2024-05-15 15:39:58', '2024-05-15 15:39:58', NULL, 12),
(15, 'Pelatihan Prakom', 'Pusdiklat BPS RI', '2023', 'sertifikas paten', 'Cumlaude', '2024-05-19 12:20:54', '2024-05-19 12:21:02', '2024-05-19 12:21:02', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tb_riwayat_pendidikan`
--

CREATE TABLE `tb_riwayat_pendidikan` (
  `id` int(11) NOT NULL,
  `jenjang` varchar(100) NOT NULL,
  `sekolah` varchar(100) NOT NULL,
  `tahun` varchar(10) NOT NULL,
  `kota_negara` varchar(100) NOT NULL,
  `keterangan` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `tenaga_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_riwayat_pendidikan`
--

INSERT INTO `tb_riwayat_pendidikan` (`id`, `jenjang`, `sekolah`, `tahun`, `kota_negara`, `keterangan`, `created_at`, `updated_at`, `deleted_at`, `tenaga_id`) VALUES
(1, 'Perguruan Tinggi', 'STIS', '2017', 'Jakarta', '', '2024-05-15 15:36:51', '2024-05-15 15:36:51', NULL, 1),
(2, 'DIV', 'STIS - Komputasi Statistik', '2017', 'Jakarta', 'oke', '2024-05-15 15:37:16', '2024-05-19 12:07:07', NULL, 2),
(3, 'Perguruan Tinggi', 'STIS', '2017', 'Jakarta', '', '2024-05-15 15:37:26', '2024-05-15 15:37:26', NULL, 3),
(4, 'Perguruan Tinggi', 'STIS', '2017', 'Jakarta', '', '2024-05-15 15:37:36', '2024-05-15 15:37:36', NULL, 4),
(5, 'Perguruan Tinggi', 'STIS', '2017', 'Jakarta', '', '2024-05-15 15:37:46', '2024-05-15 15:37:46', NULL, 5),
(6, 'Perguruan Tinggi', 'STIS', '2017', 'Jakarta', '', '2024-05-15 15:37:56', '2024-05-15 15:37:56', NULL, 6),
(7, 'Perguruan Tinggi', 'STIS', '2017', 'Jakarta', '', '2024-05-15 15:36:51', '2024-05-15 15:36:51', NULL, 7),
(8, 'Perguruan Tinggi', 'STIS', '2017', 'Jakarta', '', '2024-05-15 15:37:16', '2024-05-15 15:37:16', NULL, 8),
(9, 'Perguruan Tinggi', 'STIS', '2017', 'Jakarta', '', '2024-05-15 15:37:26', '2024-05-15 15:37:26', NULL, 9),
(10, 'Perguruan Tinggi', 'STIS', '2017', 'Jakarta', '', '2024-05-15 15:37:36', '2024-05-15 15:37:36', NULL, 10),
(11, 'Perguruan Tinggi', 'STIS', '2017', 'Jakarta', '', '2024-05-15 15:37:46', '2024-05-15 15:37:46', NULL, 11),
(12, 'Perguruan Tinggi', 'STIS', '2017', 'Jakarta', '', '2024-05-15 15:37:56', '2024-05-15 15:37:56', NULL, 12),
(13, 'S1', 'Universitas Indonesia - Ilmu Komputer', '2023', 'Jakarta', 'Cumlaude', '2024-05-19 11:59:16', '2024-05-19 11:59:16', NULL, 2),
(14, 'S2', 'Universitas Indonesia - Ilmu Komputer', '2023', 'Jakarta', 'Cumlaude', '2024-05-19 12:07:23', '2024-05-19 12:07:28', '2024-05-19 12:07:28', 2),
(15, 'DIV', 'Universitas Indonesia - Ilmu Komputer', '2024', 'Jakarta', 'asdasdasd', '2024-05-19 21:57:57', '2024-05-19 21:57:57', NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `tb_tenaga`
--

CREATE TABLE `tb_tenaga` (
  `id` int(11) NOT NULL,
  `id_profile` int(11) NOT NULL,
  `jenis_tenaga` int(1) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nik` varchar(20) NOT NULL,
  `nip` varchar(20) NOT NULL,
  `tempat_lahir` varchar(100) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `id_pangkat` int(11) NOT NULL,
  `jabatan` varchar(150) NOT NULL,
  `alamat_kantor` text NOT NULL,
  `telp_kantor` varchar(50) NOT NULL,
  `alamat_rumah` text NOT NULL,
  `telp_rumah` varchar(50) NOT NULL,
  `hp` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `npwp` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_tenaga`
--

INSERT INTO `tb_tenaga` (`id`, `id_profile`, `jenis_tenaga`, `nama`, `nik`, `nip`, `tempat_lahir`, `tanggal_lahir`, `id_pangkat`, `jabatan`, `alamat_kantor`, `telp_kantor`, `alamat_rumah`, `telp_rumah`, `hp`, `email`, `npwp`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 2, 1, 'Test Nama', '1234567812345678', '123456781234567812', 'Salatiga', '2024-05-25', 1, 'Jabatan', 'Raya Jagakarsa', '0812313', 'Raya Jagakarsa', '081231231', '08123123123', 'email@mail.com', '1234567812345678', '2024-05-25 06:09:27', '2024-05-25 06:09:27', NULL),
(2, 2, 2, 'Test Nama F', '1234567812345678', '123456781234567812', 'Salatiga', '2024-05-25', 1, 'Jabatan', 'Raya Jagakarsa', '0812313', 'Raya Jagakarsa', '081231231', '08123123123', 'email@mail.com', '1234567812345678', '2024-05-25 06:09:27', '2024-05-25 06:09:27', NULL),
(3, 2, 3, 'Test Nama P', '1234567812345678', '123456781234567812', 'Salatiga', '2024-05-25', 1, 'Jabatan', 'Raya Jagakarsa', '0812313', 'Raya Jagakarsa', '081231231', '08123123123', 'email@mail.com', '1234567812345678', '2024-05-25 06:09:27', '2024-05-25 06:09:27', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` int(1) NOT NULL,
  `id_profile` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `role`, `id_profile`, `created_at`, `updated_at`) VALUES
(1, 'lembaga', 'lembaga@mail.com', NULL, '$2y$10$KIZhvWQWKkard7SNM8ggLe39.5RIi3vDfuecHwX0RGdQ3vlrFKeNa', NULL, 4, 1, '2023-05-26 19:18:19', '2024-05-25 05:15:08'),
(2, 'asesor', 'asesor@mail.com', NULL, '$2y$10$KIZhvWQWKkard7SNM8ggLe39.5RIi3vDfuecHwX0RGdQ3vlrFKeNa', NULL, 3, NULL, '2023-05-26 19:18:19', '2023-05-26 19:18:19'),
(3, 'lembaga dua', 'lembaga2@mail.com', NULL, '$2y$10$KIZhvWQWKkard7SNM8ggLe39.5RIi3vDfuecHwX0RGdQ3vlrFKeNa', NULL, 4, 2, '2023-05-26 19:18:19', '2024-05-25 05:25:21'),
(4, 'sekretariat', 'sekretariat@mail.com', NULL, '$2y$10$KIZhvWQWKkard7SNM8ggLe39.5RIi3vDfuecHwX0RGdQ3vlrFKeNa', NULL, 2, NULL, NULL, NULL),
(9, 'test', 'test@mail.com', NULL, '$2y$10$RsT0UP/O0M1xI9qHqI2S5.EU0UUVFe.w/7Ng6bSTv6MBWEtoi6Nwq', NULL, 3, NULL, '2024-05-23 02:30:13', '2024-05-23 02:30:13'),
(15, 'Test Lembaga', 'lembaga.test@mail.com', NULL, '$2y$10$oGBMGdDAt81Gqc28WqvrDeDfualx0WFwlR8JWgWqRZTt1PVV/caha', NULL, 4, 1, '2024-05-23 02:33:44', '2024-05-25 05:26:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_items`
--
ALTER TABLE `mt_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_pangkat`
--
ALTER TABLE `mt_pangkat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_program_dokumens`
--
ALTER TABLE `mt_program_dokumens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_subunsurs`
--
ALTER TABLE `mt_subunsurs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_unsurs`
--
ALTER TABLE `mt_unsurs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mt_wilayah`
--
ALTER TABLE `mt_wilayah`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `tb_fasilitas`
--
ALTER TABLE `tb_fasilitas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_pelatihans`
--
ALTER TABLE `tb_pelatihans`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_pelatihan_dokumens`
--
ALTER TABLE `tb_pelatihan_dokumens`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_pelatihan_tenagas`
--
ALTER TABLE `tb_pelatihan_tenagas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_pengajuans`
--
ALTER TABLE `tb_pengajuans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tb_pengajuans_ttd_token_unique` (`ttd_token`);

--
-- Indexes for table `tb_penilaians`
--
ALTER TABLE `tb_penilaians`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_profile_lembagas`
--
ALTER TABLE `tb_profile_lembagas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_riwayat_jabatan`
--
ALTER TABLE `tb_riwayat_jabatan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenaga_id` (`tenaga_id`);

--
-- Indexes for table `tb_riwayat_kerja`
--
ALTER TABLE `tb_riwayat_kerja`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenaga_id` (`tenaga_id`);

--
-- Indexes for table `tb_riwayat_pelatihan`
--
ALTER TABLE `tb_riwayat_pelatihan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenaga_id` (`tenaga_id`);

--
-- Indexes for table `tb_riwayat_pendidikan`
--
ALTER TABLE `tb_riwayat_pendidikan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tenaga_id` (`tenaga_id`);

--
-- Indexes for table `tb_tenaga`
--
ALTER TABLE `tb_tenaga`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `mt_items`
--
ALTER TABLE `mt_items`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `mt_pangkat`
--
ALTER TABLE `mt_pangkat`
  MODIFY `id` int(2) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `mt_program_dokumens`
--
ALTER TABLE `mt_program_dokumens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `mt_subunsurs`
--
ALTER TABLE `mt_subunsurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `mt_unsurs`
--
ALTER TABLE `mt_unsurs`
  MODIFY `id` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `mt_wilayah`
--
ALTER TABLE `mt_wilayah`
  MODIFY `id` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=552;

--
-- AUTO_INCREMENT for table `tb_fasilitas`
--
ALTER TABLE `tb_fasilitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_pelatihans`
--
ALTER TABLE `tb_pelatihans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_pelatihan_dokumens`
--
ALTER TABLE `tb_pelatihan_dokumens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_pelatihan_tenagas`
--
ALTER TABLE `tb_pelatihan_tenagas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_pengajuans`
--
ALTER TABLE `tb_pengajuans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tb_penilaians`
--
ALTER TABLE `tb_penilaians`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `tb_profile_lembagas`
--
ALTER TABLE `tb_profile_lembagas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_riwayat_jabatan`
--
ALTER TABLE `tb_riwayat_jabatan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tb_riwayat_kerja`
--
ALTER TABLE `tb_riwayat_kerja`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tb_riwayat_pelatihan`
--
ALTER TABLE `tb_riwayat_pelatihan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tb_riwayat_pendidikan`
--
ALTER TABLE `tb_riwayat_pendidikan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tb_tenaga`
--
ALTER TABLE `tb_tenaga`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_riwayat_kerja`
--
ALTER TABLE `tb_riwayat_kerja`
  ADD CONSTRAINT `tb_riwayat_kerja_ibfk_1` FOREIGN KEY (`tenaga_id`) REFERENCES `tb_tenaga` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tb_riwayat_pelatihan`
--
ALTER TABLE `tb_riwayat_pelatihan`
  ADD CONSTRAINT `tb_riwayat_pelatihan_ibfk_1` FOREIGN KEY (`tenaga_id`) REFERENCES `tb_tenaga` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `tb_riwayat_pendidikan`
--
ALTER TABLE `tb_riwayat_pendidikan`
  ADD CONSTRAINT `tb_riwayat_pendidikan_ibfk_1` FOREIGN KEY (`tenaga_id`) REFERENCES `tb_tenaga` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
