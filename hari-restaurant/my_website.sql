-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Máy chủ: localhost
-- Thời gian đã tạo: Th5 06, 2025 lúc 03:42 AM
-- Phiên bản máy phục vụ: 8.0.39
-- Phiên bản PHP: 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `my_website`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admins`
--

CREATE TABLE `admins` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Admin',
  `department` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `role`, `department`, `last_login_ip`, `last_login_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'HẢI DZ', 'admin@gmail.com', '$2y$12$ZEpXUR2ugfVNoq9Wc52koeqJ3HvjfdcN9IMGVR.i9IVfHibAyfuuK', 'Admin', NULL, '127.0.0.1', '2025-05-05 08:36:01', NULL, '2024-11-29 07:01:06', '2025-05-05 08:36:01');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `cart`
--

CREATE TABLE `cart` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `item_id` int UNSIGNED NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `ReservationID` int UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `item_id`, `quantity`, `created_at`, `updated_at`, `ReservationID`) VALUES
(141, 1, 23, 1, '2024-12-06 22:24:20', '2024-12-06 22:24:20', NULL),
(330, 17, 28, 1, '2025-04-17 02:01:54', '2025-04-17 02:01:54', NULL),
(331, 17, 27, 1, '2025-04-17 02:01:56', '2025-04-17 02:01:56', NULL),
(332, 17, 26, 1, '2025-04-17 02:01:58', '2025-04-17 02:01:58', NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `menucategory`
--

CREATE TABLE `menucategory` (
  `CategoryID` int UNSIGNED NOT NULL,
  `CategoryName` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `menucategory`
--

INSERT INTO `menucategory` (`CategoryID`, `CategoryName`, `Description`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Món chính', 'Các món ăn chính trong thực đơn', '2024-11-09 01:16:33', '2024-11-09 01:16:33'),
(2, 'Khai vị', 'Các món khai vị', '2024-11-09 01:16:33', '2024-11-09 01:16:33'),
(3, 'Tráng miệng', 'Các món tráng miệng', '2024-11-09 01:16:33', '2024-11-09 01:16:33'),
(4, 'Chay', 'Dành cho khách ăn chay', '2024-11-20 06:05:36', '2024-11-27 06:39:15'),
(7, 'Đồ uống', 'giải khát tuyệt vời', '2024-11-26 15:37:08', '2024-11-27 06:43:47');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `menuitem`
--

CREATE TABLE `menuitem` (
  `ItemID` int UNSIGNED NOT NULL,
  `CategoryID` int UNSIGNED NOT NULL,
  `ItemName` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `status` enum('Món mới','Phổ biến','Đặc biệt','Bình thường') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Bình thường',
  `Description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `Available` tinyint(1) DEFAULT '1',
  `ImageURL` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `menuitem`
--

INSERT INTO `menuitem` (`ItemID`, `CategoryID`, `ItemName`, `Price`, `status`, `Description`, `Available`, `ImageURL`, `CreatedAt`, `UpdatedAt`) VALUES
(16, 2, 'Súp bí đỏ kem tươi', 50000.00, 'Phổ biến', 'khai vị tuyệt vời', 1, 'images/1732688898_supbido.jpg', '2024-11-27 06:28:18', '2024-12-03 08:32:17'),
(17, 2, 'Gỏi cuốn tôm thịt', 40000.00, 'Bình thường', '1 phần 2 cuộn', 1, 'images/1732689184_goicuon.jpg', '2024-11-27 06:33:04', '2024-11-27 06:33:04'),
(19, 1, 'Mỳ ý sốt kem nấm', 120000.00, 'Món mới', 'Đậm đà hương vị', 1, 'images/1732689451_my_y.jpg', '2024-11-27 06:37:31', '2024-11-27 06:37:31'),
(20, 4, 'Lẩu nấm chay thập cẩm', 150000.00, 'Bình thường', 'dành cho 2 -3 người', 1, 'images/1732689600_lau_nam.jpg', '2024-11-27 06:40:00', '2024-11-27 06:40:00'),
(21, 3, 'Bánh flan caramel', 30000.00, 'Phổ biến', 'Ngon ngọt béo ngậy', 1, 'images/1732689723_banh_flan.jpg', '2024-11-27 06:42:03', '2024-11-27 06:42:03'),
(22, 7, 'Cocktail mojito dâu tây', 100000.00, 'Bình thường', NULL, 1, 'images/1732689847_cocktail.jpg', '2024-11-27 06:44:07', '2024-11-27 06:44:07'),
(23, 1, 'Cá chiên sốt chanh dây', 150000.00, 'Đặc biệt', 'Hương vị quê hương', 1, 'images/1732690184_cachien.jpg', '2024-11-27 06:49:44', '2024-12-03 08:33:26'),
(24, 1, 'Cá hồi nướng', 170000.00, 'Món mới', 'Cá hồi nướng siêu ngon', 1, 'images/1733838309_cahoinuong.jpg', '2024-12-10 13:45:09', '2024-12-10 13:45:09'),
(25, 1, 'Gà nướng lá sen', 140000.00, 'Bình thường', 'Gà nướng thơm ngon', 1, 'images/1733838360_ganuonglasen.jpg', '2024-12-10 13:46:00', '2024-12-10 13:46:00'),
(26, 1, 'Cá hồi kho tiêu', 100000.00, 'Đặc biệt', 'Cá hồi tuyệt vời', 1, 'images/1733838527_cahoikhotieu.jpg', '2024-12-10 13:48:47', '2024-12-10 13:48:47'),
(27, 1, 'Canh khổ qua', 45000.00, 'Phổ biến', 'Canh khổ qua nhồi thịt', 1, 'images/1733838604_canhkhoiqua.jpg', '2024-12-10 13:50:04', '2024-12-10 13:50:04'),
(28, 8, 'coca', 15000.00, 'Bình thường', NULL, 1, 'images/1735040795_cahoikhotieu.jpg', '2024-12-24 11:36:04', '2024-12-24 11:46:35');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_11_27_170449_create_notifications_table', 1),
(5, 'create_admins_table', 1),
(6, '2025_04_26_073839_create_personal_access_tokens_table', 2);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `notifications`
--

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint UNSIGNED NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `order`
--

CREATE TABLE `order` (
  `OrderID` int UNSIGNED NOT NULL,
  `UserID` bigint UNSIGNED DEFAULT NULL,
  `ReservationID` int UNSIGNED DEFAULT NULL,
  `TotalAmount` decimal(15,2) NOT NULL,
  `OrderDate` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Status` enum('Đã thanh toán','Chưa thanh toán') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Chưa thanh toán'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payment`
--

CREATE TABLE `payment` (
  `PaymentID` int UNSIGNED NOT NULL,
  `PaymentCode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ReservationID` int UNSIGNED NOT NULL,
  `Amount` decimal(15,2) NOT NULL,
  `PaymentMethod` enum('Thanh toán tại nhà hàng','VNPAY','Chuyển khoản ngân hàng') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Status` enum('Chờ thanh toán','Đã thanh toán','Đã hủy','Từ chối') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Chờ thanh toán',
  `PaymentProof` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `payment`
--

INSERT INTO `payment` (`PaymentID`, `PaymentCode`, `ReservationID`, `Amount`, `PaymentMethod`, `Status`, `PaymentProof`, `CreatedAt`, `UpdatedAt`) VALUES
(111, 'PAY-C0ABE7', 130, 675000.00, 'Chuyển khoản ngân hàng', 'Đã thanh toán', 'payment_proofs/1734973294_PAY-C0ABE7.jpg', '2024-12-23 10:01:22', '2024-12-23 10:10:06'),
(112, 'PAY-613751', 131, 45000.00, 'Chuyển khoản ngân hàng', 'Đã thanh toán', 'payment_proofs/1734973849_PAY-613751.jpg', '2024-12-23 10:10:40', '2024-12-23 10:11:04'),
(113, 'PAY-9DC806', 132, 270000.00, 'Chuyển khoản ngân hàng', 'Đã thanh toán', 'payment_proofs/1734974419_PAY-9DC806.jpg', '2024-12-23 10:20:06', '2024-12-23 10:24:28'),
(114, 'PAY-1FD1DE', 133, 285000.00, 'Chuyển khoản ngân hàng', 'Đã thanh toán', 'payment_proofs/1734974937_PAY-1FD1DE.jpg', '2024-12-23 10:28:46', '2024-12-23 10:33:47'),
(115, 'PAY-DECBAF', 134, 270000.00, 'Thanh toán tại nhà hàng', 'Đã thanh toán', NULL, '2024-12-23 10:43:57', '2024-12-24 00:26:27'),
(116, 'PAY-2DDFC6', 135, 45000.00, 'Chuyển khoản ngân hàng', 'Từ chối', 'payment_proofs/1734977098_PAY-2DDFC6.jpg', '2024-12-23 11:04:48', '2024-12-23 11:14:05'),
(117, 'PAY-66AF79', 136, 45000.00, 'Chuyển khoản ngân hàng', 'Từ chối', 'payment_proofs/1734978080_PAY-66AF79.jpg', '2024-12-23 11:21:10', '2024-12-23 11:21:35'),
(118, 'PAY-0873CD', 137, 145000.00, 'Chuyển khoản ngân hàng', 'Từ chối', 'payment_proofs/1734978792_PAY-0873CD.jpg', '2024-12-23 11:33:01', '2024-12-23 11:33:33'),
(119, 'PAY-71A243', 138, 285000.00, 'Chuyển khoản ngân hàng', 'Từ chối', 'payment_proofs/1734979801_PAY-71A243.png', '2024-12-23 11:49:48', '2024-12-23 11:50:21'),
(120, 'PAY-619AAE', 139, 285000.00, 'Chuyển khoản ngân hàng', 'Từ chối', 'payment_proofs/1734980026_PAY-619AAE.jpg', '2024-12-23 11:53:35', '2024-12-23 11:53:55'),
(121, 'PAY-C35084', 140, 285000.00, 'Chuyển khoản ngân hàng', 'Đã thanh toán', 'payment_proofs/1734980194_PAY-C35084.jpg', '2024-12-23 11:56:24', '2024-12-23 11:56:50'),
(122, 'PAY-65E4CA', 141, 285000.00, 'Chuyển khoản ngân hàng', 'Đã thanh toán', 'payment_proofs/1734984115_PAY-65E4CA.png', '2024-12-23 13:01:43', '2024-12-23 13:03:37'),
(123, 'PAY-65F046', 142, 145000.00, 'Chuyển khoản ngân hàng', 'Từ chối', 'payment_proofs/1734984787_PAY-65F046.png', '2024-12-23 13:12:55', '2024-12-23 13:13:22'),
(124, 'PAY-A583C8', 143, 45000.00, 'Chuyển khoản ngân hàng', 'Từ chối', 'payment_proofs/1734984954_PAY-A583C8.jpeg', '2024-12-23 13:15:44', '2024-12-23 13:16:04'),
(125, 'PAY-CA7A37', 144, 100000.00, 'Chuyển khoản ngân hàng', 'Từ chối', 'payment_proofs/1734985063_PAY-CA7A37.jpg', '2024-12-23 13:17:33', '2024-12-23 13:17:53'),
(126, 'PAY-570824', 145, 100000.00, 'Chuyển khoản ngân hàng', 'Từ chối', 'payment_proofs/1734985154_PAY-570824.jpg', '2024-12-23 13:19:04', '2024-12-23 13:19:24'),
(127, 'PAY-044F58', 146, 45000.00, 'Chuyển khoản ngân hàng', 'Từ chối', 'payment_proofs/1734985500_PAY-044F58.jpeg', '2024-12-23 13:24:49', '2024-12-23 13:27:44'),
(128, 'PAY-11B096', 147, 145000.00, 'Chuyển khoản ngân hàng', 'Từ chối', 'payment_proofs/1734985798_PAY-11B096.jpg', '2024-12-23 13:29:49', '2024-12-23 13:30:08'),
(129, 'PAY-B935D5', 148, 375000.00, 'Chuyển khoản ngân hàng', 'Đã thanh toán', 'payment_proofs/1735003877_PAY-B935D5.jpg', '2024-12-23 18:30:40', '2024-12-23 18:32:13'),
(130, 'PAY-ABC4F5', 149, 145000.00, 'Thanh toán tại nhà hàng', 'Đã thanh toán', NULL, '2024-12-23 18:38:21', '2024-12-24 00:26:28'),
(131, 'PAY-2F1B6D', 150, 145000.00, 'Thanh toán tại nhà hàng', 'Chờ thanh toán', NULL, '2024-12-24 02:40:46', '2024-12-24 02:40:46'),
(132, 'PAY-1D3667', 151, 285000.00, 'Thanh toán tại nhà hàng', 'Chờ thanh toán', NULL, '2024-12-24 02:42:43', '2024-12-24 02:42:43'),
(133, 'PAY-FCCB22', 152, 285000.00, 'Chuyển khoản ngân hàng', 'Đã thanh toán', 'payment_proofs/1735039915_PAY-FCCB22.jpg', '2024-12-24 04:31:41', '2024-12-24 04:32:52'),
(134, 'PAY-F26FD4', 153, 470000.00, 'Thanh toán tại nhà hàng', 'Đã thanh toán', NULL, '2024-12-24 04:47:43', '2024-12-24 04:49:15'),
(135, 'PAY-AZJUOM', 155, 330000.00, 'Thanh toán tại nhà hàng', 'Chờ thanh toán', NULL, '2025-05-03 02:07:49', '2025-05-03 09:07:49'),
(136, 'PAY-AIIVYN', 156, 150000.00, 'Thanh toán tại nhà hàng', 'Chờ thanh toán', NULL, '2025-05-03 02:24:34', '2025-05-03 09:24:34'),
(137, 'PAY-5NIIRD', 157, 90000.00, 'Thanh toán tại nhà hàng', 'Chờ thanh toán', NULL, '2025-05-03 02:30:22', '2025-05-03 09:30:22');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(6, 'App\\Models\\User', 24, 'auth_token', 'a08116999b4f8999d6d89d3a0cad6ba8356ea534f266e8f18220ebf385ee7d6f', '[\"*\"]', '2025-04-27 03:30:44', NULL, '2025-04-27 03:30:25', '2025-04-27 03:30:44'),
(29, 'App\\Models\\User', 17, 'auth_token', '3df5258d0f896c0f8d0477d60ae601f6cec7e73f7d0ef625b6967b7b7f7967a1', '[\"*\"]', '2025-05-05 20:39:30', NULL, '2025-05-04 07:58:54', '2025-05-05 20:39:30');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reservation`
--

CREATE TABLE `reservation` (
  `ReservationID` int UNSIGNED NOT NULL,
  `ReservationCode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `UserID` bigint UNSIGNED NOT NULL,
  `FullName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `TableID` int UNSIGNED DEFAULT NULL,
  `GuestCount` int NOT NULL,
  `ReservationDate` datetime NOT NULL,
  `Status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `Note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `CheckInTime` timestamp NULL DEFAULT NULL,
  `CheckOutTime` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `reservation`
--

INSERT INTO `reservation` (`ReservationID`, `ReservationCode`, `UserID`, `FullName`, `Phone`, `TableID`, `GuestCount`, `ReservationDate`, `Status`, `Note`, `CreatedAt`, `UpdatedAt`, `CheckInTime`, `CheckOutTime`) VALUES
(130, 'RES2BFB00', 17, 'Cục', '0966994591', NULL, 1, '2024-12-25 12:01:00', 'Đã hủy', NULL, '2024-12-23 10:01:18', '2024-12-23 10:10:15', NULL, NULL),
(131, 'RES9A9726', 17, 'Cục', '0966994591', NULL, 1, '2024-12-25 12:10:00', 'Đã hủy', NULL, '2024-12-23 10:10:37', '2024-12-23 11:41:02', NULL, NULL),
(132, 'RES167E46', 17, 'Cục', '0966994591', 4, 1, '2024-12-25 12:19:00', 'Đã hoàn tất', NULL, '2024-12-23 10:20:02', '2024-12-23 12:35:24', '2024-12-23 12:35:22', '2024-12-23 12:35:24'),
(133, 'RES9E3FD6', 17, 'Cục', '0966994591', 6, 1, '2024-12-25 12:28:00', 'Đã hoàn tất', NULL, '2024-12-23 10:28:41', '2024-12-24 00:26:29', '2024-12-24 00:26:23', '2024-12-24 00:26:29'),
(134, 'RESEDA236', 17, 'Cục', '0966994591', 4, 1, '2024-12-24 12:43:00', 'Đã hoàn tất', NULL, '2024-12-23 10:43:39', '2024-12-24 00:26:27', '2024-12-24 00:26:22', '2024-12-24 00:26:27'),
(135, 'RES75398A', 17, 'Cục', '0966994591', NULL, 1, '2024-12-25 13:04:00', 'Đã hủy', NULL, '2024-12-23 11:04:44', '2024-12-23 11:14:05', NULL, NULL),
(136, 'RESF22630', 17, 'Cục', '0966994591', NULL, 1, '2024-12-25 13:21:00', 'Đã hủy', NULL, '2024-12-23 11:21:05', '2024-12-23 11:21:35', NULL, NULL),
(137, 'RES409232', 17, 'Cục', '0966994591', NULL, 1, '2024-12-24 14:32:00', 'Đã hủy', NULL, '2024-12-23 11:32:56', '2024-12-23 11:33:33', NULL, NULL),
(138, 'RESB9E8D0', 17, 'Cục', '0966994591', NULL, 1, '2024-12-25 13:49:00', 'Đã hủy', NULL, '2024-12-23 11:49:44', '2024-12-23 11:50:21', NULL, NULL),
(139, 'RESF820B8', 17, 'Cục', '0966994591', NULL, 1, '2024-12-25 13:53:00', 'Đã hủy', NULL, '2024-12-23 11:53:30', '2024-12-23 11:53:55', NULL, NULL),
(140, 'RESD42B65', 17, 'Cục', '0966994591', 8, 1, '2024-12-25 13:56:00', 'Đã hoàn tất', NULL, '2024-12-23 11:56:19', '2024-12-24 00:26:57', '2024-12-24 00:26:51', '2024-12-24 00:26:57'),
(141, 'RES725DDF', 17, 'Cục', '0966994591', 4, 1, '2024-12-25 15:01:00', 'Đã hoàn tất', NULL, '2024-12-23 13:01:37', '2024-12-24 00:26:59', '2024-12-24 00:26:52', '2024-12-24 00:26:59'),
(142, 'RES4E4AAC', 17, 'Cục', '0966994591', NULL, 1, '2024-12-25 15:12:00', 'Đã hủy', NULL, '2024-12-23 13:12:48', '2024-12-23 13:13:22', NULL, NULL),
(143, 'RES965DC4', 17, 'Cục', '0966994591', NULL, 1, '2024-12-25 15:15:00', 'Đã hủy', NULL, '2024-12-23 13:15:39', '2024-12-23 13:16:04', NULL, NULL),
(144, 'RES1BBD03', 17, 'Cục', '0966994591', NULL, 1, '2024-12-25 15:17:00', 'Đã hủy', NULL, '2024-12-23 13:17:29', '2024-12-23 13:17:53', NULL, NULL),
(145, 'RES53EEBE', 17, 'Cục', '0966994591', NULL, 1, '2024-12-25 15:18:00', 'Đã hủy', NULL, '2024-12-23 13:19:00', '2024-12-23 13:19:24', NULL, NULL),
(146, 'RES79382A', 17, 'Cục', '0966994591', NULL, 1, '2024-12-24 09:24:00', 'Đã hủy', NULL, '2024-12-23 13:24:44', '2024-12-23 13:27:44', NULL, NULL),
(147, 'RESBD9A6E', 17, 'Cục', '0966994591', NULL, 1, '2024-12-24 15:29:00', 'Đã hủy', NULL, '2024-12-23 13:29:45', '2024-12-23 13:30:08', NULL, NULL),
(148, 'RESC0B84E', 17, 'Cục', '0966994591', 9, 1, '2024-12-25 20:30:00', 'Đã hoàn tất', NULL, '2024-12-23 18:30:18', '2024-12-24 00:26:58', '2024-12-24 00:26:53', '2024-12-24 00:26:58'),
(149, 'RESF45C93', 17, 'Cục', '0966994591', 10, 1, '2024-12-25 08:38:00', 'Đã hoàn tất', NULL, '2024-12-23 18:38:14', '2024-12-24 00:26:28', '2024-12-24 00:26:22', '2024-12-24 00:26:28'),
(150, 'RESDDD6C0', 17, 'Cục', '0966994591', NULL, 1, '2024-12-24 17:40:00', 'Chờ xác nhận', NULL, '2024-12-24 02:40:42', '2024-12-24 02:40:46', NULL, NULL),
(151, 'RESF832A3', 17, 'Cục', '0966994591', NULL, 1, '2024-12-25 18:42:00', 'Chờ xác nhận', NULL, '2024-12-24 02:42:39', '2024-12-24 02:42:43', NULL, NULL),
(152, 'RESFB80FF', 17, 'Hải', '0966994591', 6, 4, '2024-12-24 20:30:00', 'Đã hoàn tất', 'làm món trước 30 phút', '2024-12-24 04:31:32', '2024-12-24 04:33:36', '2024-12-24 04:33:31', '2024-12-24 04:33:36'),
(153, 'RES41B1FF', 17, 'Cục', '0966994591', 6, 4, '2024-12-25 18:47:00', 'Đã hoàn tất', 'làm món trước 30 phút', '2024-12-24 04:47:37', '2024-12-24 04:49:15', '2024-12-24 04:49:09', '2024-12-24 04:49:15'),
(154, 'RES96E68C', 17, 'Cục', '0966994591', NULL, 4, '2025-04-18 10:09:00', 'Tạm thời', 'làm món trước 30 phút', '2025-04-17 02:09:14', '2025-04-17 02:09:14', NULL, NULL),
(155, 'RESJPUOOW', 17, 'Cục', '0966994591', 4, 2, '2025-05-04 19:00:00', 'Đã xác nhận', NULL, '2025-05-03 02:07:49', '2025-05-03 02:43:25', NULL, NULL),
(156, 'RESOQOJ7K', 17, 'Cục', '0903198434', NULL, 2, '2025-05-05 19:00:00', 'Chờ xác nhận', NULL, '2025-05-03 02:24:34', '2025-05-03 02:24:34', NULL, NULL),
(157, 'RESRGYYRM', 17, 'Cục', '0966994591', NULL, 2, '2025-05-03 19:00:00', 'Chờ xác nhận', NULL, '2025-05-03 02:30:22', '2025-05-03 02:30:22', NULL, NULL);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reservation_item`
--

CREATE TABLE `reservation_item` (
  `ReservationItemID` bigint UNSIGNED NOT NULL,
  `ReservationID` int UNSIGNED NOT NULL,
  `ItemID` int UNSIGNED NOT NULL,
  `Quantity` int NOT NULL DEFAULT '1',
  `Price` decimal(10,2) NOT NULL,
  `PaymentID` int UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `is_initial_order` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `reservation_item`
--

INSERT INTO `reservation_item` (`ReservationItemID`, `ReservationID`, `ItemID`, `Quantity`, `Price`, `PaymentID`, `created_at`, `updated_at`, `is_initial_order`) VALUES
(164, 130, 27, 1, 45000.00, NULL, '2024-12-23 17:01:34', '2024-12-23 17:01:34', 0),
(165, 130, 25, 1, 140000.00, NULL, '2024-12-23 17:01:34', '2024-12-23 17:01:34', 0),
(166, 130, 24, 1, 170000.00, NULL, '2024-12-23 17:01:34', '2024-12-23 17:01:34', 0),
(167, 130, 23, 1, 150000.00, NULL, '2024-12-23 17:01:34', '2024-12-23 17:01:34', 0),
(168, 130, 16, 1, 50000.00, NULL, '2024-12-23 17:02:36', '2024-12-23 17:02:36', 0),
(169, 130, 19, 1, 120000.00, NULL, '2024-12-23 17:10:06', '2024-12-23 17:10:06', 0),
(170, 131, 27, 1, 45000.00, NULL, '2024-12-23 17:10:49', '2024-12-23 17:10:49', 0),
(171, 132, 20, 1, 150000.00, NULL, '2024-12-23 17:20:19', '2024-12-23 17:20:19', 0),
(172, 132, 19, 1, 120000.00, NULL, '2024-12-23 17:24:28', '2024-12-23 17:24:28', 0),
(173, 133, 27, 1, 45000.00, NULL, '2024-12-23 17:28:57', '2024-12-23 17:28:57', 0),
(174, 133, 26, 1, 100000.00, NULL, '2024-12-23 17:28:57', '2024-12-23 17:28:57', 0),
(175, 133, 25, 1, 140000.00, NULL, '2024-12-23 17:28:57', '2024-12-23 17:28:57', 0),
(176, 134, 23, 1, 150000.00, NULL, '2024-12-23 17:55:47', '2024-12-23 17:55:47', 0),
(181, 134, 19, 1, 120000.00, NULL, '2024-12-23 18:36:27', '2024-12-23 18:36:27', 0),
(188, 140, 27, 1, 45000.00, NULL, '2024-12-23 18:56:34', '2024-12-23 18:56:34', 0),
(189, 140, 26, 1, 100000.00, NULL, '2024-12-23 18:56:34', '2024-12-23 18:56:34', 0),
(190, 140, 25, 1, 140000.00, NULL, '2024-12-23 18:56:34', '2024-12-23 18:56:34', 0),
(191, 141, 27, 1, 45000.00, NULL, '2024-12-23 20:01:55', '2024-12-23 20:01:55', 0),
(192, 141, 26, 1, 100000.00, NULL, '2024-12-23 20:01:55', '2024-12-23 20:01:55', 0),
(193, 141, 25, 1, 140000.00, NULL, '2024-12-23 20:01:55', '2024-12-23 20:01:55', 0),
(202, 148, 27, 1, 45000.00, NULL, '2024-12-24 01:31:20', '2024-12-24 01:31:20', 0),
(203, 148, 26, 1, 100000.00, NULL, '2024-12-24 01:31:20', '2024-12-24 01:31:20', 0),
(204, 148, 20, 1, 150000.00, NULL, '2024-12-24 01:31:20', '2024-12-24 01:31:20', 0),
(205, 148, 16, 1, 50000.00, NULL, '2024-12-24 01:31:20', '2024-12-24 01:31:20', 0),
(206, 148, 21, 1, 30000.00, NULL, '2024-12-24 01:31:20', '2024-12-24 01:31:20', 0),
(207, 149, 26, 1, 100000.00, NULL, '2024-12-24 01:38:21', '2024-12-24 01:38:21', 0),
(208, 149, 27, 1, 45000.00, NULL, '2024-12-24 01:38:21', '2024-12-24 01:38:21', 0),
(209, 150, 27, 1, 45000.00, NULL, '2024-12-24 09:40:46', '2024-12-24 09:40:46', 0),
(210, 150, 26, 1, 100000.00, NULL, '2024-12-24 09:40:46', '2024-12-24 09:40:46', 0),
(211, 151, 27, 1, 45000.00, NULL, '2024-12-24 09:42:43', '2024-12-24 09:42:43', 0),
(212, 151, 26, 1, 100000.00, NULL, '2024-12-24 09:42:43', '2024-12-24 09:42:43', 0),
(213, 151, 25, 1, 140000.00, NULL, '2024-12-24 09:42:43', '2024-12-24 09:42:43', 0),
(214, 152, 26, 1, 100000.00, NULL, '2024-12-24 11:31:56', '2024-12-24 11:31:56', 0),
(215, 152, 27, 1, 45000.00, NULL, '2024-12-24 11:31:56', '2024-12-24 11:31:56', 0),
(216, 152, 25, 1, 140000.00, NULL, '2024-12-24 11:31:56', '2024-12-24 11:31:56', 0),
(217, 153, 28, 1, 15000.00, NULL, '2024-12-24 11:47:43', '2024-12-24 11:47:43', 0),
(218, 153, 27, 1, 45000.00, NULL, '2024-12-24 11:47:43', '2024-12-24 11:47:43', 0),
(219, 153, 26, 1, 100000.00, NULL, '2024-12-24 11:47:43', '2024-12-24 11:47:43', 0),
(220, 153, 25, 1, 140000.00, NULL, '2024-12-24 11:47:43', '2024-12-24 11:47:43', 0),
(221, 153, 24, 1, 170000.00, NULL, '2024-12-24 11:47:43', '2024-12-24 11:47:43', 0),
(222, 155, 16, 2, 50000.00, NULL, '2025-05-03 02:07:49', '2025-05-03 02:07:49', 1),
(223, 155, 24, 1, 170000.00, NULL, '2025-05-03 02:07:49', '2025-05-03 02:07:49', 1),
(224, 155, 21, 2, 30000.00, NULL, '2025-05-03 02:07:49', '2025-05-03 02:07:49', 1),
(225, 156, 16, 3, 50000.00, NULL, '2025-05-03 02:24:34', '2025-05-03 02:24:34', 1),
(226, 157, 16, 1, 50000.00, NULL, '2025-05-03 02:30:22', '2025-05-03 02:30:22', 1),
(227, 157, 17, 1, 40000.00, NULL, '2025-05-03 02:30:22', '2025-05-03 02:30:22', 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('kp9X6353VGAYopCbFtXb3n68BH4Kqui3bJM3aTwb', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibXAzRHduSHIzSENpSTNVNExZN3RzQjZweVRCaFJTTEVQNGRkdHNhRyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1746459340),
('sQvc4B4lTTPJ2j9txihPhepDZQ8gEp0CeMv4XP0f', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/136.0.0.0 Safari/537.36 Edg/136.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoicjhVWFR0bDlJUWZuOVU3Q1FBVzJnWldBOVJnM081UG1UWHltUVI1RSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi90YWtlYXdheS1vcmRlcnMvY3VycmVudCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTI6ImxvZ2luX2FkbWluXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6MTt9', 1746459856);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `table`
--

CREATE TABLE `table` (
  `TableID` int UNSIGNED NOT NULL,
  `TableNumber` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Status` enum('Trống','Đang sử dụng','Bảo trì') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'Trống',
  `Seats` int NOT NULL,
  `Location` enum('Trong nhà','Ngoài sân','VIP') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Trong nhà',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `table`
--

INSERT INTO `table` (`TableID`, `TableNumber`, `Status`, `Seats`, `Location`, `created_at`, `updated_at`) VALUES
(1, 'T01', 'Đang sử dụng', 4, 'Trong nhà', '2024-11-21 18:46:04', '2024-12-23 09:39:02'),
(2, 'T02', 'Bảo trì', 6, 'VIP', '2024-11-21 18:46:04', '2024-12-23 03:32:12'),
(4, 'T04', 'Đang sử dụng', 2, 'Ngoài sân', '2024-11-21 18:46:04', '2025-05-03 02:43:25'),
(5, 'T05', 'Đang sử dụng', 8, 'Trong nhà', '2024-11-22 08:25:54', '2024-12-23 09:30:47'),
(6, 'T06', 'Trống', 8, 'VIP', '2024-11-22 08:31:02', '2024-12-24 04:49:15'),
(8, 'T03', 'Trống', 6, 'VIP', '2024-11-24 01:37:46', '2024-12-24 00:26:57'),
(9, 'T07', 'Trống', 1, 'Trong nhà', '2024-11-30 09:20:55', '2024-12-24 00:26:58'),
(10, 'T08', 'Trống', 16, 'VIP', '2024-11-30 09:21:09', '2024-12-24 00:26:28');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `takeaway_orders`
--

CREATE TABLE `takeaway_orders` (
  `OrderID` int UNSIGNED NOT NULL,
  `OrderCode` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `UserID` bigint UNSIGNED NOT NULL,
  `CustomerName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `TotalAmount` decimal(15,2) NOT NULL,
  `Status` enum('Pending','Confirmed','In Preparation','Out for Delivery','Delivered','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `PaymentMethod` enum('Cash on Delivery','Online Payment') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Cash on Delivery',
  `PaymentStatus` enum('Pending','Paid','Refunded') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pending',
  `Note` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `DeliveryTime` datetime DEFAULT NULL,
  `EstimatedDeliveryTime` datetime DEFAULT NULL,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `UpdatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `takeaway_order_items`
--

CREATE TABLE `takeaway_order_items` (
  `OrderItemID` int UNSIGNED NOT NULL,
  `OrderID` int UNSIGNED NOT NULL,
  `ItemID` int UNSIGNED NOT NULL,
  `Quantity` int NOT NULL DEFAULT '1',
  `Price` decimal(10,2) NOT NULL,
  `Created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `Updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `takeaway_order_tracking`
--

CREATE TABLE `takeaway_order_tracking` (
  `TrackingID` int UNSIGNED NOT NULL,
  `OrderID` int UNSIGNED NOT NULL,
  `Status` enum('Pending','Confirmed','In Preparation','Out for Delivery','Delivered','Cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `CreatedAt` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `roles` enum('User') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'User',
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `email_verified_at`, `roles`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Hải Hay Ho ', 'nguyenhuuhai01122005@gmail.com', '$2y$12$q0bWR6mDBuLK3llWhb4CfeC.U9vSLOs.gMr7aUgrRV/FkuE/MIiiO', NULL, 'User', NULL, '2024-11-01 20:31:22', '2024-11-20 03:33:59'),
(14, 'Hữu Hải', 'hai123@gmail.com', '$2y$12$tGC5AbzrLxjVOKZRxzlilOm0us/8wrAx81/YXx2R4/Q6pYvQOnpGm', NULL, 'User', NULL, '2024-11-16 04:20:25', '2024-11-19 21:40:36'),
(15, 'Thu Trang', 'thutrang123@gmail.com', '$2y$12$RXv5gEjU462E36JMlqJi9.OuNUOORzdSPFYt6yx/KXrslern2HKTm', NULL, 'User', NULL, '2024-11-17 02:34:16', '2024-11-20 03:34:02'),
(17, 'Cục', 'huuhaiauau@gmail.com', '$2y$12$lb.qY4/6S/KxgQzdx56SjO7HG/CgpcORzDxVRT/avjXhi/gg1FLoS', NULL, 'User', NULL, '2024-11-30 11:03:49', '2024-11-30 11:05:53'),
(19, 'Bo Cute', 'nguyenhuuhai0112@gmail.com', '$2y$12$.90O3t6OquROvT11D93GoOzFdKDLe37n4hfk1cgv0ssGKznNgjRv2', NULL, 'User', NULL, '2024-12-07 02:00:27', '2024-12-07 02:00:27'),
(21, 'hải', 'test@gmail.com', '$2y$12$k2K.3EVNk4z7TdL8naWpSebLP4HqIn9j3SH/dotMmgaaH2Y7AYx3.', NULL, 'User', NULL, '2025-04-25 08:57:59', '2025-04-25 08:57:59'),
(22, 'sdfsdf', 'sdfsdf@gmail.com', '$2y$12$LwR6zjG9OQFvB8K.xXPrHO.Eax.xyrqW8rW8nmjLZI54XXfxM.thy', NULL, 'User', NULL, '2025-04-25 09:09:37', '2025-04-25 09:09:37'),
(23, 'Nguyen Huu Hai', 'hahaha@gmail.com', '$2y$12$ECxKht5rz3SOLzxu6sdQXOoWpcbi0R87RAC3At1RGTTPew/v62UqG', NULL, 'User', NULL, '2025-04-26 00:30:17', '2025-04-26 00:30:17'),
(24, 'hari', 'haha@gmai.com', '$2y$12$YcVukq2QxglCfld0v0RQMeq1XhfiAD00PDUjSq7eLayPKc3j/jM1e', NULL, 'User', NULL, '2025-04-26 01:31:40', '2025-04-26 01:31:40');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Chỉ mục cho bảng `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`),
  ADD KEY `cart_reservation_fk` (`ReservationID`);

--
-- Chỉ mục cho bảng `menucategory`
--
ALTER TABLE `menucategory`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Chỉ mục cho bảng `menuitem`
--
ALTER TABLE `menuitem`
  ADD PRIMARY KEY (`ItemID`),
  ADD KEY `CategoryID` (`CategoryID`);

--
-- Chỉ mục cho bảng `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`);

--
-- Chỉ mục cho bảng `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`OrderID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `ReservationID` (`ReservationID`);

--
-- Chỉ mục cho bảng `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`PaymentID`),
  ADD UNIQUE KEY `idx_payment_code` (`PaymentCode`),
  ADD KEY `ReservationID` (`ReservationID`);

--
-- Chỉ mục cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Chỉ mục cho bảng `reservation`
--
ALTER TABLE `reservation`
  ADD PRIMARY KEY (`ReservationID`),
  ADD UNIQUE KEY `idx_reservation_code` (`ReservationCode`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `TableID` (`TableID`);

--
-- Chỉ mục cho bảng `reservation_item`
--
ALTER TABLE `reservation_item`
  ADD PRIMARY KEY (`ReservationItemID`),
  ADD KEY `ReservationID` (`ReservationID`),
  ADD KEY `ItemID` (`ItemID`),
  ADD KEY `PaymentID` (`PaymentID`);

--
-- Chỉ mục cho bảng `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Chỉ mục cho bảng `table`
--
ALTER TABLE `table`
  ADD PRIMARY KEY (`TableID`);

--
-- Chỉ mục cho bảng `takeaway_orders`
--
ALTER TABLE `takeaway_orders`
  ADD PRIMARY KEY (`OrderID`),
  ADD UNIQUE KEY `idx_order_code` (`OrderCode`),
  ADD KEY `UserID` (`UserID`);

--
-- Chỉ mục cho bảng `takeaway_order_items`
--
ALTER TABLE `takeaway_order_items`
  ADD PRIMARY KEY (`OrderItemID`),
  ADD KEY `OrderID` (`OrderID`),
  ADD KEY `ItemID` (`ItemID`);

--
-- Chỉ mục cho bảng `takeaway_order_tracking`
--
ALTER TABLE `takeaway_order_tracking`
  ADD PRIMARY KEY (`TrackingID`),
  ADD KEY `OrderID` (`OrderID`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `cart`
--
ALTER TABLE `cart`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=333;

--
-- AUTO_INCREMENT cho bảng `menucategory`
--
ALTER TABLE `menucategory`
  MODIFY `CategoryID` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT cho bảng `menuitem`
--
ALTER TABLE `menuitem`
  MODIFY `ItemID` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT cho bảng `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT cho bảng `order`
--
ALTER TABLE `order`
  MODIFY `OrderID` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `payment`
--
ALTER TABLE `payment`
  MODIFY `PaymentID` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT cho bảng `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT cho bảng `reservation`
--
ALTER TABLE `reservation`
  MODIFY `ReservationID` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT cho bảng `reservation_item`
--
ALTER TABLE `reservation_item`
  MODIFY `ReservationItemID` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=228;

--
-- AUTO_INCREMENT cho bảng `table`
--
ALTER TABLE `table`
  MODIFY `TableID` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT cho bảng `takeaway_orders`
--
ALTER TABLE `takeaway_orders`
  MODIFY `OrderID` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `takeaway_order_items`
--
ALTER TABLE `takeaway_order_items`
  MODIFY `OrderItemID` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `takeaway_order_tracking`
--
ALTER TABLE `takeaway_order_tracking`
  MODIFY `TrackingID` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Ràng buộc đối với các bảng kết xuất
--

--
-- Ràng buộc cho bảng `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `menuitem` (`ItemID`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_reservation_fk` FOREIGN KEY (`ReservationID`) REFERENCES `reservation` (`ReservationID`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `payment`
--
ALTER TABLE `payment`
  ADD CONSTRAINT `payment_reservation_fk` FOREIGN KEY (`ReservationID`) REFERENCES `reservation` (`ReservationID`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `reservation`
--
ALTER TABLE `reservation`
  ADD CONSTRAINT `reservation_table_fk` FOREIGN KEY (`TableID`) REFERENCES `table` (`TableID`),
  ADD CONSTRAINT `reservation_user_fk` FOREIGN KEY (`UserID`) REFERENCES `users` (`id`);

--
-- Ràng buộc cho bảng `reservation_item`
--
ALTER TABLE `reservation_item`
  ADD CONSTRAINT `reservation_item_fk_1` FOREIGN KEY (`ReservationID`) REFERENCES `reservation` (`ReservationID`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_item_fk_2` FOREIGN KEY (`ItemID`) REFERENCES `menuitem` (`ItemID`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservation_item_ibfk_1` FOREIGN KEY (`PaymentID`) REFERENCES `payment` (`PaymentID`);

--
-- Ràng buộc cho bảng `takeaway_orders`
--
ALTER TABLE `takeaway_orders`
  ADD CONSTRAINT `takeaway_orders_user_fk` FOREIGN KEY (`UserID`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `takeaway_order_items`
--
ALTER TABLE `takeaway_order_items`
  ADD CONSTRAINT `takeaway_order_items_item_fk` FOREIGN KEY (`ItemID`) REFERENCES `menuitem` (`ItemID`) ON DELETE CASCADE,
  ADD CONSTRAINT `takeaway_order_items_order_fk` FOREIGN KEY (`OrderID`) REFERENCES `takeaway_orders` (`OrderID`) ON DELETE CASCADE;

--
-- Ràng buộc cho bảng `takeaway_order_tracking`
--
ALTER TABLE `takeaway_order_tracking`
  ADD CONSTRAINT `takeaway_order_tracking_order_fk` FOREIGN KEY (`OrderID`) REFERENCES `takeaway_orders` (`OrderID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
