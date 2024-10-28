-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2024 at 07:48 PM
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
-- Database: `cinema_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `blogs`
--

CREATE TABLE `blogs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loaibaiviet_id` bigint(20) UNSIGNED NOT NULL,
  `tieu_de` varchar(255) NOT NULL,
  `anh_bai_viet` varchar(255) NOT NULL,
  `noi_dung` varchar(255) NOT NULL,
  `ngay_viet` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `so_luong` int(11) DEFAULT NULL,
  `ghi_chu` varchar(255) DEFAULT NULL,
  `ma_giam_gia` varchar(255) DEFAULT NULL,
  `doan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tong_tien` decimal(12,3) NOT NULL,
  `tong_tien_thanh_toan` decimal(12,3) NOT NULL,
  `ngay_mua` date NOT NULL,
  `trang_thai` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `thongtinchieu_id` bigint(20) UNSIGNED NOT NULL,
  `ghe_ngoi` varchar(255) DEFAULT NULL,
  `so_luong_do_an` int(11) DEFAULT NULL,
  `do_an` varchar(255) DEFAULT NULL,
  `magiamgia_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `so_luong`, `ghi_chu`, `ma_giam_gia`, `doan_id`, `tong_tien`, `tong_tien_thanh_toan`, `ngay_mua`, `trang_thai`, `created_at`, `updated_at`, `deleted_at`, `thongtinchieu_id`, `ghe_ngoi`, `so_luong_do_an`, `do_an`, `magiamgia_id`) VALUES
(60, 13, 1, NULL, 'Giam', 2, 123.500, 123.500, '2024-10-22', 1, '2024-10-22 09:17:36', '2024-10-22 09:18:20', NULL, 12, 'A5', 1, '2 Coca', NULL),
(61, 14, 1, NULL, 'Giam', 2, 123.500, 123.500, '2024-10-22', 1, '2024-10-22 09:21:25', '2024-10-22 09:21:53', NULL, 12, 'A5', 1, '2 Coca', NULL),
(62, 13, 3, NULL, 'Giam', 2, 370.500, 370.500, '2024-10-23', 1, '2024-10-22 20:41:02', '2024-10-22 20:41:43', NULL, 12, 'A5, A6, A7', 3, '2 Coca', NULL),
(64, 13, 1, NULL, 'Giam', 2, 123.500, 123.500, '2024-10-23', 0, '2024-10-23 07:45:27', '2024-10-23 07:45:27', NULL, 20, 'A5', 1, '2 Coca', NULL),
(65, 13, 1, NULL, 'Giam', 2, 123.500, 123.500, '2024-10-23', 0, '2024-10-23 08:34:19', '2024-10-23 08:34:19', NULL, 20, 'A5', 1, '2 Coca', NULL),
(66, 13, 3, NULL, 'Giam', 2, 332.500, 332.500, '2024-10-23', 0, '2024-10-23 08:38:25', '2024-10-23 08:38:25', NULL, 20, 'A5, A6, A7', 1, '2 Coca', NULL),
(67, 13, 3, NULL, 'Giam', 15, 323.000, 323.000, '2024-10-24', 0, '2024-10-24 09:53:08', '2024-10-24 09:53:08', NULL, 25, 'A5, A6, A7', 1, 'Sting Đỏ', NULL),
(68, 13, 3, NULL, 'Giam', 15, 323.000, 323.000, '2024-10-24', 1, '2024-10-24 10:08:00', '2024-10-24 10:23:52', NULL, 25, 'A5, A6, A7', 1, 'Sting Đỏ', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `booking_details`
--

CREATE TABLE `booking_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `trang_thai` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `thanhtoan_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_details`
--

INSERT INTO `booking_details` (`id`, `booking_id`, `trang_thai`, `created_at`, `updated_at`, `deleted_at`, `thanhtoan_id`) VALUES
(2, 60, 1, '2024-10-22 09:18:20', '2024-10-22 09:18:20', NULL, 2),
(3, 61, 1, '2024-10-22 09:21:53', '2024-10-22 09:21:53', NULL, 3),
(4, 62, 1, '2024-10-22 20:41:43', '2024-10-22 20:41:43', NULL, 4),
(5, 68, 1, '2024-10-24 10:23:52', '2024-10-24 10:23:52', NULL, 5);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `phim_id` bigint(20) UNSIGNED NOT NULL,
  `khoangkhac_id` bigint(20) UNSIGNED NOT NULL,
  `noi_dung` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `noidung` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countdown_vouchers`
--

CREATE TABLE `countdown_vouchers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `magiamgia_id` bigint(20) UNSIGNED NOT NULL,
  `ngay` date NOT NULL,
  `thoi_gian_bat_dau` time NOT NULL,
  `thoi_gian_ket_thuc` time NOT NULL,
  `so_luong` int(11) NOT NULL,
  `trang_thai` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Table structure for table `foods`
--

CREATE TABLE `foods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_do_an` varchar(255) NOT NULL,
  `gia` decimal(12,3) NOT NULL,
  `ghi_chu` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `trang_thai` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `foods`
--

INSERT INTO `foods` (`id`, `ten_do_an`, `gia`, `ghi_chu`, `created_at`, `updated_at`, `deleted_at`, `trang_thai`) VALUES
(2, '2 Coca', 20.000, '2 coca', '2024-10-07 15:49:19', '2024-10-07 16:01:59', NULL, 0),
(10, 'Bỏng bơ', 15.000, 'Bỏng bơ', '2024-10-24 03:25:40', '2024-10-24 03:25:40', NULL, 0),
(11, 'Bỏng vị nấm', 15.000, 'Bỏng vị nấm', '2024-10-24 03:25:58', '2024-10-24 03:25:58', NULL, 0),
(12, 'Bỏng không vị', 15.000, 'Bỏng không vị', '2024-10-24 03:26:22', '2024-10-24 03:26:22', NULL, 0),
(13, 'Coca', 10.000, 'Coca', '2024-10-24 03:26:39', '2024-10-24 03:26:39', NULL, 0),
(14, 'Pessi', 10.000, 'Pessi', '2024-10-24 03:28:08', '2024-10-24 03:28:08', NULL, 0),
(15, 'Sting Đỏ', 10.000, 'Sting Đỏ', '2024-10-24 03:28:37', '2024-10-24 03:28:37', NULL, 0),
(16, 'Sting Vàng', 10.000, 'Sting Vàng', '2024-10-24 10:26:45', '2024-10-24 10:26:45', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `history_rotations`
--

CREATE TABLE `history_rotations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `vongquay_id` bigint(20) UNSIGNED NOT NULL,
  `ket_qua` varchar(255) NOT NULL,
  `ngay_quay` date NOT NULL,
  `trang_thai` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE `members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `loai_hoi_vien` varchar(255) NOT NULL,
  `uu_dai` double(8,2) NOT NULL,
  `thoi_gian` double(8,2) NOT NULL,
  `ghi_chu` varchar(255) NOT NULL,
  `gia` decimal(12,3) NOT NULL,
  `trang_thai` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `memberships`
--

CREATE TABLE `memberships` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dangkyhoivien_id` bigint(20) UNSIGNED NOT NULL,
  `ngay_dang_ky` date NOT NULL,
  `trang_thai` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
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
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2024_09_30_131038_create_contacts_table', 1),
(6, '2024_09_30_140223_create_movie_genres_table', 1),
(7, '2024_09_30_140500_create_movies_table', 1),
(8, '2024_09_30_142059_create_theaters_table', 1),
(9, '2024_09_30_145940_create_rooms_table', 1),
(10, '2024_09_30_150032_create_showtimes_table', 1),
(11, '2024_09_30_150151_create_seats_table', 1),
(12, '2024_09_30_150744_create_comments_table', 1),
(13, '2024_10_02_152342_create_rotations_table', 2),
(14, '2024_10_02_153835_create_rotations_table', 3),
(15, '2024_10_02_154140_create_history_rotations_table', 4),
(16, '2024_10_02_154937_create_moments_table', 5),
(17, '2024_10_02_155559_create_playlist_movies_table', 6),
(18, '2024_10_02_155926_create_type_blogs_table', 7),
(19, '2024_10_02_160023_create_blogs_table', 8),
(20, '2024_10_02_160447_create_members_table', 9),
(21, '2024_10_04_152016_create_foods_table', 10),
(22, '2024_10_04_152202_create_bookings_table', 11),
(23, '2024_10_04_152533_create_payments_table', 12),
(24, '2024_10_04_153657_create_booking_details_table', 13),
(25, '2024_10_04_153938_create_register_members_table', 14),
(26, '2024_10_04_154919_add_foregin_key_to_payments_table', 15),
(27, '2024_10_04_155305_create_memberships_table', 16),
(28, '2024_10_04_160414_create_vouchers_table', 17),
(29, '2024_10_04_160754_create_countdown_vouchers_table', 18),
(30, '2024_10_07_092456_add_deleted_at_to_multiple_tables', 19),
(31, '2024_10_07_100047_add_new_column_to_users_table', 20),
(32, '2024_10_07_160454_add_column_to_seats_table', 21),
(33, '2024_10_07_175154_modify_column_in_showtimes_table', 22),
(34, '2024_10_07_175352_add_column_to_showtimes_table', 23),
(35, '2024_10_07_175654_add_column_to_showtimes_table', 24),
(36, '2024_10_07_183718_modify_column_in_movies_table', 25),
(37, '2024_10_07_185809_modify_column_in_movies_table', 26),
(38, '2024_10_07_191714_create_storage_moviegenres_table', 27),
(39, '2024_10_07_192554_remove_column_movies_table', 28),
(40, '2024_10_07_195820_create_movie_movie_genre_table', 29),
(41, '2024_10_07_200702_rename_movie_genres_to_moviegenres', 30),
(42, '2024_10_07_224054_add_column_to_foods_table', 31),
(43, '2024_10_08_060206_modify_column_in_bookings_table', 32),
(44, '2024_10_09_152012_add_column_to_seats_table', 33),
(45, '2024_10_09_161509_modify_column_in_rooms_table', 34),
(46, '2024_10_09_161941_modify_column_in_seats_table', 35),
(47, '2024_10_10_131632_modify_column_in_users_table', 36),
(48, '2024_10_10_133533_modify_column_in_users_table', 37),
(49, '2024_10_10_133700_add_column_to_users_table', 38),
(50, '2024_10_10_135448_modify_column_in_showtimes_table', 39),
(51, '2024_10_10_141635_modify_column_in_vouchers_table', 40),
(52, '2024_10_10_144343_modify_column_in_booking_details_table', 41),
(53, '2024_10_10_202538_modify_column_in_bookings_table', 42),
(54, '2024_10_11_113445_modify_column_in_payments_table', 43),
(55, '2024_10_12_064726_create_rotations_table', 44),
(56, '2024_10_12_073048_drop_history_rotations_table', 45),
(57, '2024_10_12_073145_drop_rotations_table', 46),
(61, '2024_10_12_073342_create_rotations_table', 47),
(62, '2024_10_12_074551_create_history_rotations_table', 47),
(63, '2024_10_13_100028_drop_comments_table', 47),
(64, '2024_10_13_100057_create_comments_table', 47),
(65, '2024_10_13_102434_drop_foreign_keys_from_storage_moviegenres_table', 47),
(66, '2024_10_13_103311_drop_storage_moviegenres_table', 48),
(67, '2024_10_14_132734_modify_column_in_booking_details_table', 49),
(68, '2024_10_14_133621_add_column_to_booking_details_table', 50),
(69, '2024_10_14_153150_drop_bookings_table', 51),
(70, '2024_10_14_153422_drop_bookings_table', 52),
(71, '2024_10_14_154548_drop_to_bookings_table', 53),
(72, '2024_10_14_160238_add_column_to2_movies_table', 54),
(73, '2024_10_18_131541_modify_column_in_bookings_table', 55),
(74, '2024_10_18_141742_add_column_to_bookings_table', 56),
(75, '2024_10_18_154942_add_column_to2_bookings_table', 57),
(76, '2024_10_18_155609_add_column_to3_bookings_table', 58),
(77, '2024_10_18_155918_add_column_to3_bookings_table', 59),
(78, '2024_10_18_160304_add_ghengoi_id_to_bookings_table', 60),
(79, '2024_10_15_094946_add_ngay_het_han_to_registermembers_table', 61),
(80, '2024_10_19_093508_drop_foreign_key_from_bookings', 61),
(81, '2024_10_19_093902_drop_foreign_key_from_booking_details', 62),
(82, '2024_10_19_094146_drop_column_from_booking_details', 63),
(83, '2024_10_19_145605_add_so_luong_do_an_to_bookings_table', 64),
(84, '2024_10_20_070554_add_do_an_to_bookings_table', 65),
(85, '2024_10_20_111508_drop_ghengoi_id_from_bookings', 66),
(86, '2024_10_20_151407_add_thanhtoan_id_to_booking_details_table', 67),
(87, '2024_10_20_152447_add_thanhtoan_id_to_booking_details_table', 68),
(88, '2024_10_21_114059_modify_column_in_vouchers_table', 69),
(89, '2024_10_21_144631_drop_foreign_key_from_bookings', 70),
(90, '2024_10_21_144737_drop_magiamgia_id_from_bookings', 71),
(91, '2024_10_21_150430_add_magiamgia_id_to_bookings_table', 72),
(92, '2024_10_21_151428_add_magiamgia_id_to_bookings_table', 73),
(93, '2024_10_21_153139_add_magiamgia_id_to_bookings_table', 74),
(94, '2024_10_23_055237_add_thoi_gian_phim_to_movies_table', 75),
(95, '2024_10_23_105639_create_seat_showtimes_table', 76),
(96, '2024_10_23_113009_rename_seat_showtimes_to_seat_showtime_status', 77),
(97, '2024_10_23_113156_rename_seat_showtimes_to_seat_showtime_status', 78);

-- --------------------------------------------------------

--
-- Table structure for table `moments`
--

CREATE TABLE `moments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `phim_id` bigint(20) UNSIGNED NOT NULL,
  `anh_khoang_khac` varchar(255) NOT NULL,
  `noi_dung` varchar(255) NOT NULL,
  `like` double(8,2) NOT NULL,
  `dislike` double(8,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `moviegenres`
--

CREATE TABLE `moviegenres` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_loai_phim` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `moviegenres`
--

INSERT INTO `moviegenres` (`id`, `ten_loai_phim`, `created_at`, `updated_at`, `deleted_at`) VALUES
(9, 'Hành Động', '2024-10-07 13:40:14', '2024-10-07 13:40:14', NULL),
(10, 'Chiến Tranh', '2024-10-07 13:40:22', '2024-10-07 13:40:22', NULL),
(13, 'Tình Cảm', '2024-10-14 09:00:12', '2024-10-14 09:00:12', NULL),
(14, 'Ma Quỷ', '2024-10-17 21:06:10', '2024-10-17 21:15:17', '2024-10-17 21:15:17'),
(15, 'Cảm Cảm', '2024-10-23 05:53:50', '2024-10-23 05:56:25', '2024-10-23 05:56:25'),
(16, 'fggg', '2024-10-23 09:04:02', '2024-10-23 09:17:20', NULL),
(17, 'DDDD', '2024-10-24 03:03:42', '2024-10-24 03:04:26', '2024-10-24 03:04:26');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_phim` varchar(255) NOT NULL,
  `anh_phim` varchar(255) DEFAULT NULL,
  `dao_dien` varchar(255) DEFAULT NULL,
  `dien_vien` varchar(255) DEFAULT NULL,
  `noi_dung` varchar(255) NOT NULL,
  `trailer` varchar(255) DEFAULT NULL,
  `gia_ve` decimal(12,3) NOT NULL,
  `danh_gia` double(3,1) NOT NULL DEFAULT 0.0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `hinh_thuc_phim` varchar(255) NOT NULL,
  `thoi_gian_phim` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `ten_phim`, `anh_phim`, `dao_dien`, `dien_vien`, `noi_dung`, `trailer`, `gia_ve`, `danh_gia`, `created_at`, `updated_at`, `deleted_at`, `hinh_thuc_phim`, `thoi_gian_phim`) VALUES
(24, 'Chuyện Tình Tôi', '/storage/uploads/anh_phim/1729662996_anh8.jpg', 'Ko sung he , Park', 'Ko sung he , Rore', 'Phim tình cảm', 'https://youtu.be/Dc8-rujjVxM?si=qIIrql5deMSBWyae', 100.000, 0.0, '2024-10-22 22:56:37', '2024-10-22 22:56:37', NULL, 'Đang chiếu', 120);

-- --------------------------------------------------------

--
-- Table structure for table `movie_movie_genre`
--

CREATE TABLE `movie_movie_genre` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `movie_id` bigint(20) UNSIGNED NOT NULL,
  `movie_genre_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `movie_movie_genre`
--

INSERT INTO `movie_movie_genre` (`id`, `movie_id`, `movie_genre_id`, `created_at`, `updated_at`) VALUES
(54, 24, 9, NULL, NULL),
(55, 24, 13, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED DEFAULT NULL,
  `tong_tien` decimal(12,3) NOT NULL,
  `phuong_thuc_thanh_toan` enum('credit_card','paypal','cash','bank_transfer') NOT NULL,
  `ma_thanh_toan` varchar(255) NOT NULL,
  `ngay_thanh_toan` datetime NOT NULL,
  `trang_thai` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `registermember_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `booking_id`, `tong_tien`, `phuong_thuc_thanh_toan`, `ma_thanh_toan`, `ngay_thanh_toan`, `trang_thai`, `created_at`, `updated_at`, `registermember_id`, `deleted_at`) VALUES
(2, 60, 123.500, 'cash', 'PAY_6717D04CC9A4E', '2024-10-22 16:18:20', 1, '2024-10-22 09:18:20', '2024-10-22 09:18:20', NULL, NULL),
(3, 61, 123.500, 'cash', 'PAY_6717D1216D5A7', '2024-10-22 16:21:53', 1, '2024-10-22 09:21:53', '2024-10-22 09:21:53', NULL, NULL),
(4, 62, 370.500, 'cash', 'PAY_67187077BE9E2', '2024-10-23 03:41:43', 1, '2024-10-22 20:41:43', '2024-10-22 20:41:43', NULL, NULL),
(5, 68, 323.000, 'bank_transfer', 'PAY_671A82A8AF58D', '2024-10-24 17:23:52', 1, '2024-10-24 10:23:52', '2024-10-24 10:23:52', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `playlist_movies`
--

CREATE TABLE `playlist_movies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `phim_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `register_members`
--

CREATE TABLE `register_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `hoivien_id` bigint(20) UNSIGNED NOT NULL,
  `tong_tien` decimal(12,3) NOT NULL,
  `ngay_dang_ky` date NOT NULL,
  `ngay_het_han` date DEFAULT NULL,
  `trang_thai` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_phong_chieu` varchar(255) NOT NULL,
  `tong_ghe_phong` bigint(20) NOT NULL,
  `rapphim_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `ten_phong_chieu`, `tong_ghe_phong`, `rapphim_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(8, 'Phòng số 1', 150, 8, '2024-10-14 06:22:37', '2024-10-14 06:22:37', NULL),
(9, 'Phòng 898989', 140, 9, '2024-10-17 06:12:45', '2024-10-17 06:19:41', NULL),
(10, 'Phòng 88', 150, 12, '2024-10-17 06:20:32', '2024-10-17 06:20:57', '2024-10-17 06:20:57'),
(11, 'p fgg12', 110, 8, '2024-10-23 06:29:24', '2024-10-23 06:32:32', '2024-10-23 06:32:32');

-- --------------------------------------------------------

--
-- Table structure for table `rotations`
--

CREATE TABLE `rotations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_phan_thuong` varchar(255) NOT NULL,
  `muc_giam_gia` decimal(12,3) DEFAULT NULL,
  `mo_ta` varchar(255) NOT NULL,
  `xac_xuat` int(11) NOT NULL,
  `so_luong` int(11) NOT NULL,
  `so_luong_con_lai` int(11) DEFAULT NULL,
  `trang_thai` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `so_ghe_ngoi` varchar(255) NOT NULL,
  `loai_ghe_ngoi` varchar(255) NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `trang_thai` tinyint(4) DEFAULT 0,
  `gia_ghe` decimal(12,3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`id`, `so_ghe_ngoi`, `loai_ghe_ngoi`, `room_id`, `created_at`, `updated_at`, `deleted_at`, `trang_thai`, `gia_ghe`) VALUES
(521, 'A1', 'Thường', 8, '2024-10-19 06:35:46', '2024-10-23 06:35:28', NULL, 0, 10.000),
(522, 'A2', 'Thường', 8, '2024-10-19 06:35:46', '2024-10-19 06:35:46', NULL, 0, 10.000),
(523, 'A3', 'Thường', 8, '2024-10-19 06:35:46', '2024-10-19 06:35:46', NULL, 0, 10.000),
(524, 'A4', 'Thường', 8, '2024-10-19 06:35:46', '2024-10-19 06:35:46', NULL, 0, 10.000),
(525, 'A5', 'Thường', 8, '2024-10-19 06:35:46', '2024-10-19 06:35:46', NULL, 0, 10.000),
(526, 'A6', 'Thường', 8, '2024-10-19 06:35:46', '2024-10-19 06:35:46', NULL, 0, 10.000),
(527, 'A7', 'Thường', 8, '2024-10-19 06:35:46', '2024-10-19 06:35:46', NULL, 0, 10.000),
(528, 'A8', 'Thường', 8, '2024-10-19 06:35:46', '2024-10-19 06:35:46', NULL, 0, 10.000),
(529, 'A9', 'Thường', 8, '2024-10-19 06:35:46', '2024-10-19 06:35:46', NULL, 0, 10.000),
(530, 'A10', 'Thường', 8, '2024-10-19 06:35:46', '2024-10-19 06:35:46', NULL, 0, 10.000),
(531, 'B1', 'Thường', 8, '2024-10-19 06:36:10', '2024-10-19 06:36:10', NULL, 0, 10.000),
(532, 'B2', 'Thường', 8, '2024-10-19 06:36:10', '2024-10-19 06:36:10', NULL, 0, 10.000),
(533, 'B3', 'Thường', 8, '2024-10-19 06:36:10', '2024-10-19 06:36:10', NULL, 0, 10.000),
(534, 'B4', 'Thường', 8, '2024-10-19 06:36:10', '2024-10-19 06:36:10', NULL, 0, 10.000),
(535, 'B5', 'Thường', 8, '2024-10-19 06:36:10', '2024-10-19 06:36:10', NULL, 0, 10.000),
(536, 'B6', 'Thường', 8, '2024-10-19 06:36:10', '2024-10-19 06:36:10', NULL, 0, 10.000),
(537, 'B7', 'Thường', 8, '2024-10-19 06:36:10', '2024-10-19 06:36:10', NULL, 0, 10.000),
(538, 'B8', 'Thường', 8, '2024-10-19 06:36:10', '2024-10-19 06:36:10', NULL, 0, 10.000),
(539, 'B9', 'Thường', 8, '2024-10-19 06:36:10', '2024-10-19 06:36:10', NULL, 0, 10.000),
(540, 'B10', 'Thường', 8, '2024-10-19 06:36:10', '2024-10-19 06:36:10', NULL, 0, 10.000),
(541, 'C1', 'Thường', 8, '2024-10-19 06:36:44', '2024-10-19 06:36:44', NULL, 0, 10.000),
(542, 'C2', 'Thường', 8, '2024-10-19 06:36:44', '2024-10-19 06:36:44', NULL, 0, 10.000),
(543, 'C3', 'Thường', 8, '2024-10-19 06:36:44', '2024-10-19 06:36:44', NULL, 0, 10.000),
(544, 'C4', 'Thường', 8, '2024-10-19 06:36:44', '2024-10-19 06:36:44', NULL, 0, 10.000),
(545, 'C5', 'Thường', 8, '2024-10-19 06:36:44', '2024-10-19 06:36:44', NULL, 0, 10.000),
(546, 'C6', 'Thường', 8, '2024-10-19 06:36:44', '2024-10-19 06:36:44', NULL, 0, 10.000),
(547, 'C7', 'Thường', 8, '2024-10-19 06:36:44', '2024-10-19 06:36:44', NULL, 0, 10.000),
(548, 'C8', 'Thường', 8, '2024-10-19 06:36:44', '2024-10-19 06:36:44', NULL, 0, 10.000),
(549, 'C9', 'Thường', 8, '2024-10-19 06:36:44', '2024-10-19 06:36:44', NULL, 0, 10.000),
(550, 'C10', 'Thường', 8, '2024-10-19 06:36:44', '2024-10-19 06:36:44', NULL, 0, 10.000),
(551, 'VVV', 'VVV', 8, '2024-10-19 06:37:05', '2024-10-23 06:59:35', NULL, 0, 33.000),
(552, 'VIP2', 'VIP', 8, '2024-10-19 06:37:05', '2024-10-19 06:37:05', NULL, 0, 30.000),
(553, 'VIP3', 'VIP', 8, '2024-10-19 06:37:05', '2024-10-19 06:37:05', NULL, 0, 30.000),
(554, 'VIP4', 'VIP', 8, '2024-10-19 06:37:05', '2024-10-19 06:37:05', NULL, 0, 30.000),
(555, 'VIP5', 'VIP', 8, '2024-10-19 06:37:05', '2024-10-19 06:37:05', NULL, 0, 30.000),
(556, 'VIP6', 'VIP', 8, '2024-10-19 06:37:05', '2024-10-19 06:37:05', NULL, 0, 30.000),
(557, 'VIP7', 'VIP', 8, '2024-10-19 06:37:05', '2024-10-19 06:37:05', NULL, 0, 30.000),
(558, 'VIP8', 'VIP', 8, '2024-10-19 06:37:05', '2024-10-19 06:37:05', NULL, 0, 30.000),
(559, 'VIP9', 'VIP', 8, '2024-10-19 06:37:05', '2024-10-19 06:37:05', NULL, 0, 30.000),
(560, 'VIP10', 'VIP', 8, '2024-10-19 06:37:05', '2024-10-19 06:37:05', NULL, 0, 30.000),
(561, 'A1', 'Thường', 9, '2024-10-23 00:00:17', '2024-10-23 00:00:17', NULL, 0, 5.000),
(562, 'A2', 'Thường', 9, '2024-10-23 00:00:17', '2024-10-23 00:00:17', NULL, 0, 5.000),
(563, 'A3', 'Thường', 9, '2024-10-23 00:00:17', '2024-10-23 00:00:17', NULL, 0, 5.000),
(564, 'A4', 'Thường', 9, '2024-10-23 00:00:17', '2024-10-23 00:00:17', NULL, 0, 5.000),
(565, 'A5', 'Thường', 9, '2024-10-23 00:00:17', '2024-10-23 00:00:17', NULL, 0, 5.000),
(566, 'A6', 'Thường', 9, '2024-10-23 00:00:17', '2024-10-23 00:00:17', NULL, 0, 5.000),
(567, 'A7', 'Thường', 9, '2024-10-23 00:00:17', '2024-10-23 00:00:17', NULL, 0, 5.000),
(568, 'A8', 'Thường', 9, '2024-10-23 00:00:17', '2024-10-23 00:00:17', NULL, 0, 5.000),
(569, 'A9', 'Thường', 9, '2024-10-23 00:00:17', '2024-10-23 00:00:17', NULL, 0, 5.000),
(570, 'A10', 'Thường', 9, '2024-10-23 00:00:17', '2024-10-23 00:00:17', NULL, 0, 5.000),
(571, 'A11', 'Thường', 9, '2024-10-23 00:00:17', '2024-10-23 00:00:17', NULL, 0, 5.000),
(572, 'A12', 'Thường', 9, '2024-10-23 00:00:17', '2024-10-23 00:00:17', NULL, 0, 5.000),
(573, 'A13', 'Thường', 9, '2024-10-23 00:00:17', '2024-10-23 00:00:17', NULL, 0, 5.000),
(574, 'A14', 'Thường', 9, '2024-10-23 00:00:17', '2024-10-23 00:00:17', NULL, 0, 5.000),
(575, 'A15', 'Thường', 9, '2024-10-23 00:00:17', '2024-10-23 00:00:17', NULL, 0, 5.000),
(576, 'D1', 'Thường', 8, '2024-10-23 06:54:44', '2024-10-23 06:54:44', NULL, 0, 10.000),
(577, 'D2', 'Thường', 8, '2024-10-23 06:54:44', '2024-10-23 06:54:44', NULL, 0, 10.000),
(578, 'D3', 'Thường', 8, '2024-10-23 06:54:44', '2024-10-23 06:54:44', NULL, 0, 10.000),
(579, 'D4', 'Thường', 8, '2024-10-23 06:54:44', '2024-10-23 06:54:44', NULL, 0, 10.000),
(580, 'D5', 'Thường', 8, '2024-10-23 06:54:44', '2024-10-23 06:54:44', NULL, 0, 10.000),
(581, 'D6', 'Thường', 8, '2024-10-23 06:54:44', '2024-10-23 06:54:44', NULL, 0, 10.000),
(582, 'D7', 'Thường', 8, '2024-10-23 06:54:44', '2024-10-23 06:54:44', NULL, 0, 10.000),
(583, 'D8', 'Thường', 8, '2024-10-23 06:54:44', '2024-10-23 06:54:44', NULL, 0, 10.000),
(584, 'D9', 'Thường', 8, '2024-10-23 06:54:44', '2024-10-23 06:54:44', NULL, 0, 10.000),
(585, 'D10', 'Thường', 8, '2024-10-23 06:54:44', '2024-10-23 06:54:44', NULL, 0, 10.000),
(586, 'D11', 'Thường', 8, '2024-10-23 06:54:44', '2024-10-23 06:54:44', NULL, 0, 10.000),
(587, 'D12', 'Thường', 8, '2024-10-23 06:54:44', '2024-10-23 06:54:44', NULL, 0, 10.000),
(588, 'D13', 'Thường', 8, '2024-10-23 06:54:44', '2024-10-23 06:54:44', NULL, 0, 10.000),
(589, 'D14', 'Thường', 8, '2024-10-23 06:54:44', '2024-10-23 06:54:44', NULL, 0, 10.000),
(590, 'D15', 'Thường', 8, '2024-10-23 06:54:44', '2024-10-23 07:00:13', '2024-10-23 07:00:13', 0, 10.000);

-- --------------------------------------------------------

--
-- Table structure for table `seat_showtime_status`
--

CREATE TABLE `seat_showtime_status` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ghengoi_id` bigint(20) UNSIGNED NOT NULL,
  `thongtinchieu_id` bigint(20) UNSIGNED NOT NULL,
  `trang_thai` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seat_showtime_status`
--

INSERT INTO `seat_showtime_status` (`id`, `ghengoi_id`, `thongtinchieu_id`, `trang_thai`, `created_at`, `updated_at`) VALUES
(4, 525, 25, 1, NULL, NULL),
(5, 526, 25, 1, NULL, NULL),
(6, 527, 25, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `showtimes`
--

CREATE TABLE `showtimes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ngay_chieu` date NOT NULL,
  `thoi_luong_chieu` varchar(255) NOT NULL,
  `phim_id` bigint(20) UNSIGNED NOT NULL,
  `rapphim_id` bigint(20) UNSIGNED NOT NULL,
  `room_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `gio_chieu` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `showtimes`
--

INSERT INTO `showtimes` (`id`, `ngay_chieu`, `thoi_luong_chieu`, `phim_id`, `rapphim_id`, `room_id`, `created_at`, `updated_at`, `deleted_at`, `gio_chieu`) VALUES
(25, '2024-11-27', '120', 24, 8, 8, '2024-10-24 05:25:41', '2024-10-24 05:25:41', NULL, '23:00:00'),
(26, '2024-11-28', '120', 24, 8, 8, '2024-10-24 05:26:49', '2024-10-24 05:26:49', NULL, '13:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `theaters`
--

CREATE TABLE `theaters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_rap` varchar(255) NOT NULL,
  `dia_diem` varchar(255) NOT NULL,
  `tong_ghe` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `theaters`
--

INSERT INTO `theaters` (`id`, `ten_rap`, `dia_diem`, `tong_ghe`, `created_at`, `updated_at`, `deleted_at`) VALUES
(8, 'Lotte Ciname 1', 'Hà Đông', 1500, '2024-10-07 06:29:21', '2024-10-07 06:29:21', NULL),
(9, 'Lotte Ciname cơ sở 2', 'Hà Đông', 1000, '2024-10-07 06:29:56', '2024-10-07 06:29:56', NULL),
(12, 'Lotte Ciname cơ sở 4', 'Xuân Phương', 1200, '2024-10-07 06:31:18', '2024-10-07 06:31:18', NULL),
(13, 'Rạp 8 test', 'HA HA', 300, '2024-10-07 06:32:36', '2024-10-17 06:06:45', '2024-10-17 06:06:45'),
(14, 'Rạp phim 0099', 'Xuân Phương 999', 900, '2024-10-17 06:02:57', '2024-10-17 06:04:29', NULL),
(15, 'rafffft', 'xuân ffffphương', 666, '2024-10-23 06:22:45', '2024-10-23 06:25:49', '2024-10-23 06:25:49');

-- --------------------------------------------------------

--
-- Table structure for table `type_blogs`
--

CREATE TABLE `type_blogs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ten_loai_bai_viet` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ho_ten` varchar(255) NOT NULL,
  `anh` varchar(255) DEFAULT NULL,
  `gioi_tinh` enum('nam','nu','khac') NOT NULL,
  `email` varchar(255) NOT NULL,
  `so_dien_thoai` varchar(10) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `diem_thuong` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `ma_giam_gia` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `so_luot_quay` int(11) DEFAULT NULL,
  `vai_tro` enum('user','admin','nhan_vien') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `ho_ten`, `anh`, `gioi_tinh`, `email`, `so_dien_thoai`, `email_verified_at`, `password`, `remember_token`, `diem_thuong`, `ma_giam_gia`, `created_at`, `updated_at`, `deleted_at`, `so_luot_quay`, `vai_tro`) VALUES
(13, 'Bùi Văn Ánh', NULL, 'nam', 'vlaanhiu@gmail.com', '0327367912', NULL, '$2y$12$GmfYbLlEeb280quZOl.JDuyo52DNAMYoew2cVirxQXl4TevwDQGv2', NULL, 0, NULL, '2024-10-20 07:12:05', '2024-10-20 07:12:05', NULL, NULL, 'admin'),
(14, 'Bùi Văn Ánh', NULL, 'nam', 'buianh2592003@gmail.com', '0327367922', NULL, '$2y$12$uppEWzWUnJrh.ghxxzxT8ugEU2Sukh5ndBvGyay3qkRw1tQXKDcyW', NULL, 0, NULL, '2024-10-22 09:20:20', '2024-10-22 09:20:20', NULL, NULL, 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `vouchers`
--

CREATE TABLE `vouchers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ma_giam_gia` varchar(255) NOT NULL,
  `muc_giam_gia` int(11) NOT NULL,
  `mota` varchar(255) NOT NULL,
  `ngay_het_han` date NOT NULL,
  `so_luong` int(11) NOT NULL,
  `so_luong_da_su_dung` int(11) DEFAULT NULL,
  `trang_thai` tinyint(4) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vouchers`
--

INSERT INTO `vouchers` (`id`, `ma_giam_gia`, `muc_giam_gia`, `mota`, `ngay_het_han`, `so_luong`, `so_luong_da_su_dung`, `trang_thai`, `created_at`, `updated_at`, `deleted_at`) VALUES
(7, 'Giam', 5, 'Giảm 5% mỗi lần mua vé xem phim', '2024-12-12', 10000, 15, 0, '2024-10-21 07:09:41', '2024-10-24 10:08:00', NULL),
(8, 'GiaGia', 10, 'giam 10 %%%', '2024-11-11', 10000, NULL, 0, '2024-10-24 00:28:07', '2024-10-24 00:30:25', NULL),
(9, 'Gia', 10, 'giam 10 %', '2024-11-11', 10000, NULL, 0, '2024-10-24 00:30:56', '2024-10-24 00:31:28', '2024-10-24 00:31:28'),
(10, 'vouche11', 15, 'giam gia 15%', '2024-11-11', 10000, NULL, 0, '2024-10-24 10:25:21', '2024-10-24 10:26:13', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blogs_loaibaiviet_id_foreign` (`loaibaiviet_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_user_id_foreign` (`user_id`),
  ADD KEY `bookings_doan_id_foreign` (`doan_id`),
  ADD KEY `bookings_magiamgia_id_foreign` (`magiamgia_id`);

--
-- Indexes for table `booking_details`
--
ALTER TABLE `booking_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_details_booking_id_foreign` (`booking_id`),
  ADD KEY `booking_details_thanhtoan_id_foreign` (`thanhtoan_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_user_id_foreign` (`user_id`),
  ADD KEY `comments_phim_id_foreign` (`phim_id`),
  ADD KEY `comments_khoangkhac_id_foreign` (`khoangkhac_id`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contacts_user_id_foreign` (`user_id`);

--
-- Indexes for table `countdown_vouchers`
--
ALTER TABLE `countdown_vouchers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `countdown_vouchers_magiamgia_id_foreign` (`magiamgia_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `foods`
--
ALTER TABLE `foods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `history_rotations`
--
ALTER TABLE `history_rotations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `history_rotations_user_id_foreign` (`user_id`),
  ADD KEY `history_rotations_vongquay_id_foreign` (`vongquay_id`);

--
-- Indexes for table `members`
--
ALTER TABLE `members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `memberships`
--
ALTER TABLE `memberships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `memberships_dangkyhoivien_id_foreign` (`dangkyhoivien_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `moments`
--
ALTER TABLE `moments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `moments_user_id_foreign` (`user_id`),
  ADD KEY `moments_phim_id_foreign` (`phim_id`);

--
-- Indexes for table `moviegenres`
--
ALTER TABLE `moviegenres`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movie_movie_genre`
--
ALTER TABLE `movie_movie_genre`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_movie_genre_movie_id_foreign` (`movie_id`),
  ADD KEY `movie_movie_genre_movie_genre_id_foreign` (`movie_genre_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payments_ma_thanh_toan_unique` (`ma_thanh_toan`),
  ADD KEY `payments_booking_id_foreign` (`booking_id`),
  ADD KEY `payments_registermember_id_foreign` (`registermember_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `playlist_movies`
--
ALTER TABLE `playlist_movies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `playlist_movies_user_id_foreign` (`user_id`),
  ADD KEY `playlist_movies_phim_id_foreign` (`phim_id`);

--
-- Indexes for table `register_members`
--
ALTER TABLE `register_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `register_members_user_id_foreign` (`user_id`),
  ADD KEY `register_members_hoivien_id_foreign` (`hoivien_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rooms_rapphim_id_foreign` (`rapphim_id`);

--
-- Indexes for table `rotations`
--
ALTER TABLE `rotations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seats_room_id_foreign` (`room_id`);

--
-- Indexes for table `seat_showtime_status`
--
ALTER TABLE `seat_showtime_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seat_showtimes_ghengoi_id_foreign` (`ghengoi_id`),
  ADD KEY `seat_showtimes_thongtinchieu_id_foreign` (`thongtinchieu_id`);

--
-- Indexes for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `showtimes_phim_id_foreign` (`phim_id`),
  ADD KEY `showtimes_rapphim_id_foreign` (`rapphim_id`),
  ADD KEY `showtimes_room_id_foreign` (`room_id`);

--
-- Indexes for table `theaters`
--
ALTER TABLE `theaters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `type_blogs`
--
ALTER TABLE `type_blogs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_so_dien_thoai_unique` (`so_dien_thoai`);

--
-- Indexes for table `vouchers`
--
ALTER TABLE `vouchers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `booking_details`
--
ALTER TABLE `booking_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `countdown_vouchers`
--
ALTER TABLE `countdown_vouchers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `foods`
--
ALTER TABLE `foods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `history_rotations`
--
ALTER TABLE `history_rotations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `members`
--
ALTER TABLE `members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `memberships`
--
ALTER TABLE `memberships`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `moments`
--
ALTER TABLE `moments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `moviegenres`
--
ALTER TABLE `moviegenres`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `movie_movie_genre`
--
ALTER TABLE `movie_movie_genre`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `playlist_movies`
--
ALTER TABLE `playlist_movies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `register_members`
--
ALTER TABLE `register_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `rotations`
--
ALTER TABLE `rotations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=591;

--
-- AUTO_INCREMENT for table `seat_showtime_status`
--
ALTER TABLE `seat_showtime_status`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `showtimes`
--
ALTER TABLE `showtimes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `theaters`
--
ALTER TABLE `theaters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `type_blogs`
--
ALTER TABLE `type_blogs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `vouchers`
--
ALTER TABLE `vouchers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_loaibaiviet_id_foreign` FOREIGN KEY (`loaibaiviet_id`) REFERENCES `type_blogs` (`id`);

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_doan_id_foreign` FOREIGN KEY (`doan_id`) REFERENCES `foods` (`id`),
  ADD CONSTRAINT `bookings_magiamgia_id_foreign` FOREIGN KEY (`magiamgia_id`) REFERENCES `vouchers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `booking_details`
--
ALTER TABLE `booking_details`
  ADD CONSTRAINT `booking_details_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `booking_details_thanhtoan_id_foreign` FOREIGN KEY (`thanhtoan_id`) REFERENCES `payments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_khoangkhac_id_foreign` FOREIGN KEY (`khoangkhac_id`) REFERENCES `moments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_phim_id_foreign` FOREIGN KEY (`phim_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contacts`
--
ALTER TABLE `contacts`
  ADD CONSTRAINT `contacts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `countdown_vouchers`
--
ALTER TABLE `countdown_vouchers`
  ADD CONSTRAINT `countdown_vouchers_magiamgia_id_foreign` FOREIGN KEY (`magiamgia_id`) REFERENCES `vouchers` (`id`);

--
-- Constraints for table `history_rotations`
--
ALTER TABLE `history_rotations`
  ADD CONSTRAINT `history_rotations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `history_rotations_vongquay_id_foreign` FOREIGN KEY (`vongquay_id`) REFERENCES `rotations` (`id`);

--
-- Constraints for table `memberships`
--
ALTER TABLE `memberships`
  ADD CONSTRAINT `memberships_dangkyhoivien_id_foreign` FOREIGN KEY (`dangkyhoivien_id`) REFERENCES `register_members` (`id`);

--
-- Constraints for table `moments`
--
ALTER TABLE `moments`
  ADD CONSTRAINT `moments_phim_id_foreign` FOREIGN KEY (`phim_id`) REFERENCES `movies` (`id`),
  ADD CONSTRAINT `moments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `movie_movie_genre`
--
ALTER TABLE `movie_movie_genre`
  ADD CONSTRAINT `movie_movie_genre_movie_genre_id_foreign` FOREIGN KEY (`movie_genre_id`) REFERENCES `moviegenres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `movie_movie_genre_movie_id_foreign` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`),
  ADD CONSTRAINT `payments_registermember_id_foreign` FOREIGN KEY (`registermember_id`) REFERENCES `register_members` (`id`);

--
-- Constraints for table `playlist_movies`
--
ALTER TABLE `playlist_movies`
  ADD CONSTRAINT `playlist_movies_phim_id_foreign` FOREIGN KEY (`phim_id`) REFERENCES `movies` (`id`),
  ADD CONSTRAINT `playlist_movies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `register_members`
--
ALTER TABLE `register_members`
  ADD CONSTRAINT `register_members_hoivien_id_foreign` FOREIGN KEY (`hoivien_id`) REFERENCES `members` (`id`),
  ADD CONSTRAINT `register_members_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_rapphim_id_foreign` FOREIGN KEY (`rapphim_id`) REFERENCES `theaters` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `seats_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seat_showtime_status`
--
ALTER TABLE `seat_showtime_status`
  ADD CONSTRAINT `seat_showtimes_ghengoi_id_foreign` FOREIGN KEY (`ghengoi_id`) REFERENCES `seats` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `seat_showtimes_thongtinchieu_id_foreign` FOREIGN KEY (`thongtinchieu_id`) REFERENCES `showtimes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD CONSTRAINT `showtimes_phim_id_foreign` FOREIGN KEY (`phim_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `showtimes_rapphim_id_foreign` FOREIGN KEY (`rapphim_id`) REFERENCES `theaters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `showtimes_room_id_foreign` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
