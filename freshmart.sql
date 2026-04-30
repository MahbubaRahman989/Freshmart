-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 19, 2026 at 08:10 AM
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
-- Database: `freshmart`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(30) NOT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `email`, `password`, `address`, `created_at`) VALUES
(1, 'Admin', ' info@freshmart.com', 'freshmart123!', '123 Grocery Street, Uttara, Dhaka', '2025-12-04 13:20:40');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(10) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `email`, `message`, `created_at`) VALUES
(1, 'jui111@gmail.com', 'Today\'s products are so fresh.. ', '2026-01-10 05:08:33'),
(2, 'jui111@gmail.com', 'Products is s fresh .', '2026-01-14 13:15:53');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_assignments`
--

CREATE TABLE `delivery_assignments` (
  `id` int(10) UNSIGNED NOT NULL,
  `delivery_person_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `assigned_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_assignments`
--

INSERT INTO `delivery_assignments` (`id`, `delivery_person_id`, `order_id`, `assigned_date`) VALUES
(1, 1, 103, '2026-01-06'),
(2, 1, 114, '2026-01-08'),
(3, 1, 114, '2026-01-08'),
(4, 1, 118, '2026-01-10'),
(5, 2, 117, '2026-01-10'),
(6, 1, 115, '2026-01-10'),
(7, 1, 108, '2026-01-10'),
(8, 1, 106, '2026-01-10'),
(9, 1, 102, '2026-01-10'),
(10, 1, 101, '2026-01-10'),
(11, 1, 95, '2026-01-10'),
(12, 1, 94, '2026-01-10'),
(13, 2, 117, '2026-01-10'),
(14, 1, 121, '2026-01-12'),
(15, 2, 122, '2026-01-12'),
(16, 2, 122, '2026-01-12'),
(17, 1, 120, '2026-01-13'),
(18, 2, 124, '2026-01-13'),
(19, 1, 125, '2026-01-12'),
(20, 1, 88, '2026-01-12'),
(21, 2, 119, '2026-01-12'),
(22, 3, 93, '2026-01-12'),
(23, 3, 83, '2026-01-13'),
(24, 3, 63, '2026-01-13'),
(25, 3, 133, '2026-01-15'),
(26, 3, 138, '2026-01-15'),
(27, 3, 137, '2026-01-15'),
(28, 1, 136, '2026-01-16'),
(29, 1, 135, '2026-01-16'),
(30, 1, 129, '2026-01-16'),
(31, 3, 127, '2026-01-14'),
(32, 3, 128, '2026-01-14'),
(33, 3, 131, '2026-01-15'),
(34, 3, 141, '2026-01-15'),
(35, 3, 141, '2026-01-16');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_locations`
--

CREATE TABLE `delivery_locations` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `delivery_person_id` int(11) NOT NULL,
  `lat` decimal(10,7) NOT NULL,
  `lng` decimal(10,7) NOT NULL,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_locations`
--

INSERT INTO `delivery_locations` (`id`, `order_id`, `delivery_person_id`, `lat`, `lng`, `updated_at`) VALUES
(1, 110, 1, 23.9174797, 90.3895601, '2026-01-05 01:31:22'),
(19, 107, 2, 23.9174797, 90.3895601, '2026-01-05 01:51:53'),
(35, 114, 1, 23.9152724, 90.3895601, '2026-01-12 23:30:25'),
(107, 118, 1, 23.9152724, 90.3895601, '2026-01-12 23:30:25'),
(116, 121, 1, 23.8899279, 90.3966255, '2026-01-10 11:22:48'),
(178, 117, 2, 23.9122722, 90.3907376, '2026-01-13 23:44:41'),
(186, 122, 2, 23.8899279, 90.3966255, '2026-01-10 11:34:07'),
(288, 124, 2, 23.9167152, 90.3978032, '2026-01-11 12:42:22');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_persons`
--

CREATE TABLE `delivery_persons` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `status` enum('available','busy','out_of_work') DEFAULT 'available',
  `active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_persons`
--

INSERT INTO `delivery_persons` (`id`, `name`, `phone`, `password`, `email`, `address`, `profile_pic`, `status`, `active`) VALUES
(1, 'Jishan Islam', '01999435290', '$2y$10$JVtgODndqd/3lgsJgEalBe27XpGs7IEDYGbtRNnmWoqYLMF1pcokG', 'jishan@gmail.com', 'Sirajgang, Rajshahi', 'dp_1_1768236070.png', 'available', 1),
(2, 'Karim Rahman', '01999378467', '$2y$10$6bbc3Cdi2DMxQ2MeRUXRfuFotGPg.BRXQMqcDme5A6vWeOa5nWJpG', 'karim@gmail.com', 'Dhanmondi, Dhaka', NULL, 'available', 1),
(3, 'Emon Halder', '01999999999', '$2y$10$wSkWba3SituhL04WzdH3/uMB.a9o5MfSmg81d.hyySQwtmtzCU.oi', 'emon@gmail.com', 'Uttara, Dhaka', 'dp_3_1768163981.png', 'available', 1);

-- --------------------------------------------------------

--
-- Table structure for table `delivery_tracking_history`
--

CREATE TABLE `delivery_tracking_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `delivery_person_id` int(11) DEFAULT NULL,
  `status` enum('not_assigned','assigned','out_for_delivery','delivered') NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_tracking_history`
--

INSERT INTO `delivery_tracking_history` (`id`, `order_id`, `delivery_person_id`, `status`, `note`, `created_at`) VALUES
(1, 109, 2, 'delivered', 'Delivered successfully', '2026-01-05 00:39:38'),
(2, 110, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-05 00:54:30'),
(3, 107, 2, 'assigned', 'Assigned to Karim Rahman', '2026-01-05 01:39:35'),
(4, 107, 2, 'out_for_delivery', 'Rider is on the way', '2026-01-05 01:42:18'),
(5, 68, 2, 'assigned', 'Assigned to Karim Rahman', '2026-01-05 03:13:52'),
(6, 68, 2, 'out_for_delivery', 'Rider is on the way', '2026-01-05 03:14:17'),
(7, 111, 2, 'assigned', 'Assigned to Karim Rahman', '2026-01-05 03:28:40'),
(8, 112, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-07 01:02:38'),
(9, 103, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-07 01:49:46'),
(10, 114, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-07 04:54:46'),
(11, 114, 1, 'out_for_delivery', 'Rider is on the way', '2026-01-07 04:55:11'),
(12, 118, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-10 09:00:29'),
(13, 117, 2, 'assigned', 'Assigned to Karim Rahman', '2026-01-10 09:04:43'),
(14, 115, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-10 09:05:15'),
(15, 108, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-10 09:05:31'),
(16, 106, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-10 09:05:55'),
(17, 102, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-10 09:06:07'),
(18, 101, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-10 09:06:47'),
(19, 95, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-10 09:07:13'),
(20, 94, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-10 09:07:24'),
(21, 117, 2, 'out_for_delivery', 'Rider is on the way', '2026-01-10 10:01:49'),
(22, 121, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-10 11:20:53'),
(23, 122, 2, 'assigned', 'Assigned to Karim Rahman', '2026-01-10 11:31:50'),
(24, 122, 2, 'assigned', 'Assigned to Karim Rahman', '2026-01-10 11:32:09'),
(25, 120, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-11 01:44:29'),
(26, 124, 2, 'assigned', 'Assigned to Karim Rahman', '2026-01-11 03:17:23'),
(27, 125, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-12 22:35:42'),
(28, 88, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-12 22:36:15'),
(29, 119, 2, 'assigned', 'Assigned to Karim Rahman', '2026-01-12 22:36:46'),
(30, 93, 3, 'assigned', 'Assigned to Emon Halder', '2026-01-12 22:37:12'),
(31, 83, 3, 'assigned', 'Assigned to Emon Halder', '2026-01-13 09:25:01'),
(32, 63, 3, 'assigned', 'Assigned to Emon Halder', '2026-01-13 09:25:25'),
(33, 133, 3, 'assigned', 'Assigned to Emon Halder', '2026-01-14 02:31:11'),
(34, 138, 3, 'assigned', 'Assigned to Emon Halder', '2026-01-14 03:36:34'),
(35, 137, 3, 'assigned', 'Assigned to Emon Halder', '2026-01-14 03:40:56'),
(36, 136, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-14 03:45:10'),
(37, 135, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-14 09:58:57'),
(38, 129, 1, 'assigned', 'Assigned to Jishan Islam', '2026-01-14 10:21:14'),
(39, 127, 3, 'assigned', 'Assigned to Emon Halder', '2026-01-14 10:29:04'),
(40, 128, 3, 'assigned', 'Assigned to Emon Halder', '2026-01-14 10:30:11'),
(41, 131, 3, 'assigned', 'Assigned to Emon Halder', '2026-01-16 03:17:25'),
(42, 141, 3, 'assigned', 'Assigned to Emon Halder', '2026-01-16 03:22:17'),
(43, 141, 3, 'out_for_delivery', 'Rider is on the way', '2026-01-16 15:29:59');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `order_id`, `message`, `is_read`, `created_at`) VALUES
(1, 7, 81, 'Your order #81 delivery status updated: ASSIGNED', 1, '2026-01-02 20:54:03'),
(2, 9, 0, ' Your order #85 has been cancelled and ৳360.72 refunded to your account.', 1, '2026-01-03 01:08:55'),
(3, 9, 0, ' Your order #66 has been cancelled successfully.', 1, '2026-01-03 01:11:10'),
(4, 3, 0, ' Your order #48 has been cancelled and ৳561.12 refunded to your account.', 1, '2026-01-03 02:02:13'),
(5, 7, 0, ' Your order #91 has been cancelled and ৳3,443.87 refunded to your account.', 1, '2026-01-03 02:14:32'),
(6, 7, 81, 'Your order #81 delivery status updated: OUT FOR DELIVERY', 1, '2026-01-03 02:44:52'),
(7, 7, 76, 'Your order #76 delivery status updated: OUT FOR DELIVERY', 1, '2026-01-03 02:46:11'),
(8, 7, 75, 'Your order #75 delivery status updated: OUT FOR DELIVERY', 1, '2026-01-03 02:46:26'),
(9, 15, 90, 'Your order #90 delivery status updated: OUT FOR DELIVERY', 1, '2026-01-03 03:32:00'),
(10, 15, 90, 'Your order #90 delivery status updated: ASSIGNED', 1, '2026-01-03 03:32:18'),
(11, 15, 90, 'Your order #90 delivery status updated: ASSIGNED', 1, '2026-01-03 03:48:33'),
(12, 15, 90, 'Your order #90 delivery status updated: OUT FOR DELIVERY', 1, '2026-01-03 03:48:41'),
(13, 15, 89, 'Your order #89 delivery status updated: ASSIGNED', 1, '2026-01-03 03:50:57'),
(14, 15, 89, 'Your order #89 delivery status updated: OUT FOR DELIVERY', 1, '2026-01-03 03:51:17'),
(15, 15, 89, 'Your COD payment for order #89 has been received. Order delivered successfully.', 1, '2026-01-03 03:51:46'),
(16, 3, 86, 'Your order #86 delivery status updated: OUT FOR DELIVERY', 1, '2026-01-03 03:52:35'),
(17, 3, 86, 'Your COD payment for order #86 has been received. Order delivered successfully.', 1, '2026-01-03 04:03:52'),
(18, 9, 84, 'Your order #84 delivery status updated: OUT FOR DELIVERY', 1, '2026-01-03 04:05:25'),
(19, 15, 0, ' Your order #92 has been cancelled successfully.', 1, '2026-01-03 04:08:07'),
(20, 9, 51, 'Your order #51 delivery status updated: ASSIGNED', 1, '2026-01-03 14:04:24'),
(21, 7, 64, 'Your order #64 delivery status updated: ASSIGNED', 1, '2026-01-03 14:04:52'),
(22, 7, 97, 'Your order #97 delivery status updated: ASSIGNED', 1, '2026-01-03 14:48:09'),
(23, 7, 97, 'Your order #97 delivery status updated: OUT FOR DELIVERY', 1, '2026-01-03 14:50:22'),
(24, 7, 97, 'Your COD payment for order #97 has been received. Order delivered successfully.', 1, '2026-01-03 14:50:58'),
(25, 7, 98, 'Your order #98 delivery status updated: ASSIGNED', 1, '2026-01-04 04:16:31'),
(26, 1, 98, 'Delivery person (Jishan Islam) accepted delivery for Order #98.', 0, '2026-01-04 04:17:02'),
(27, 9, 96, 'Your order #96 delivery status updated: ASSIGNED', 1, '2026-01-04 04:18:00'),
(28, 1, 98, 'Delivery person (Jishan Islam) accepted delivery for Order #98.', 0, '2026-01-04 04:18:05'),
(29, 12, 99, 'Your order #99 delivery status updated: ASSIGNED', 1, '2026-01-04 04:45:21'),
(30, 1, 99, 'Delivery person (Jishan Islam) accepted delivery for Order #99.', 0, '2026-01-04 04:47:48'),
(31, 1, 99, 'Delivery person (Jishan Islam) accepted delivery for Order #99.', 0, '2026-01-04 04:49:19'),
(32, 12, 99, 'Your order #99 is out for delivery.', 1, '2026-01-04 04:55:48'),
(33, 1, 99, 'Delivery person (Jishan Islam) set Order #99 as OUT FOR DELIVERY.', 0, '2026-01-04 04:55:48'),
(34, 1, 99, 'COD COLLECTED: Delivery person (Jishan Islam) collected Tk 1550.00 for Order #99. Please confirm payment & complete order.', 0, '2026-01-04 04:56:03'),
(35, 7, 98, 'Your order #98 delivery status updated: DELIVERED', 1, '2026-01-04 04:56:32'),
(36, 12, 99, 'Your COD payment for order #99 has been received. Order delivered successfully.', 1, '2026-01-04 04:57:25'),
(37, 1, 99, 'Admin confirmed COD payment for Order #99. Delivery completed.', 0, '2026-01-04 04:57:25'),
(38, 1, 99, 'COD payment confirmed for Order #99. Order marked Delivered & Completed.', 0, '2026-01-04 04:57:25'),
(39, 1, 99, 'COD COLLECTED: Delivery person (Jishan Islam) collected Tk 1550.00 for Order #99. Please confirm payment & complete order.', 0, '2026-01-04 04:58:16'),
(40, 1, 96, 'Delivery person (Jishan Islam) accepted delivery for Order #96.', 0, '2026-01-04 04:58:57'),
(41, 9, 96, 'Your order #96 delivery status updated: OUT FOR DELIVERY', 1, '2026-01-04 04:59:33'),
(42, 1, 96, 'Delivery person (Jishan Islam) accepted delivery for Order #96.', 0, '2026-01-04 04:59:41'),
(43, 1, 96, 'COD COLLECTED: Delivery person (Jishan Islam) collected Tk 660.00 for Order #96. Please confirm payment & complete order.', 0, '2026-01-04 04:59:56'),
(44, 1, 96, 'COD COLLECTED: Delivery person (Jishan Islam) collected Tk 660.00 for Order #96. Please confirm payment & complete order.', 0, '2026-01-04 05:00:04'),
(45, 9, 96, 'Your COD payment for order #96 has been received. Order delivered successfully.', 1, '2026-01-04 05:00:17'),
(46, 1, 96, 'Admin confirmed COD payment for Order #96. Delivery completed.', 0, '2026-01-04 05:00:17'),
(47, 1, 96, 'COD payment confirmed for Order #96. Order marked Delivered & Completed.', 0, '2026-01-04 05:00:17'),
(48, 1, 96, 'Delivery person (Jishan Islam) accepted delivery for Order #96.', 0, '2026-01-04 05:16:56'),
(49, 1, 96, 'Delivery person (Jishan Islam) accepted delivery for Order #96.', 0, '2026-01-04 05:34:11'),
(50, 9, 100, 'Your order #100 delivery status updated: ASSIGNED', 1, '2026-01-04 06:03:48'),
(51, 1, 100, 'Delivery person (Jishan Islam) accepted delivery for Order #100.', 0, '2026-01-04 06:04:31'),
(52, 9, 100, 'Your order #100 delivery status updated: OUT FOR DELIVERY', 1, '2026-01-04 06:06:13'),
(53, 1, 100, 'Delivery person (Jishan Islam) accepted delivery for Order #100.', 0, '2026-01-04 06:06:24'),
(54, 1, 100, 'COD COLLECTED: Delivery person (Jishan Islam) collected Tk 1920.00 for Order #100. Please confirm payment & complete order.', 0, '2026-01-04 06:07:04'),
(55, 9, 100, 'Your COD payment for order #100 has been received. Order delivered successfully.', 1, '2026-01-04 06:07:45'),
(56, 1, 100, 'Admin confirmed COD payment for Order #100. Delivery completed.', 0, '2026-01-04 06:07:45'),
(57, 1, 100, 'COD payment confirmed for Order #100. Order marked Delivered & Completed.', 0, '2026-01-04 06:07:45'),
(58, 3, 0, ' Your order #87 has been cancelled and ৳2,121.23 refunded to your account.', 1, '2026-01-04 13:29:57'),
(59, 3, 105, 'Your order #105 delivery status updated: ASSIGNED', 1, '2026-01-04 13:40:15'),
(60, 1, 105, 'Delivery person (Jishan Islam) accepted delivery for Order #105.', 0, '2026-01-04 13:46:07'),
(61, 3, 105, 'Your order #105 delivery status updated: OUT FOR DELIVERY', 1, '2026-01-04 13:46:59'),
(62, 1, 105, 'Delivery person (Jishan Islam) accepted delivery for Order #105.', 0, '2026-01-04 13:47:28'),
(63, 3, 105, 'Your order #105 has been delivered successfully.', 1, '2026-01-04 13:47:50'),
(64, 1, 105, 'Delivery DONE! (Jishan Islam) delivered Order #105.', 0, '2026-01-04 13:47:50'),
(65, 3, 105, 'Your order #105 delivery status updated: DELIVERED', 1, '2026-01-04 13:48:25'),
(66, 3, 104, 'Your order #104 delivery status updated: ASSIGNED', 1, '2026-01-04 13:49:31'),
(67, 3, 105, 'Your order #105 has been delivered successfully.', 1, '2026-01-04 13:49:42'),
(68, 1, 105, 'Delivery DONE! (Jishan Islam) delivered Order #105.', 0, '2026-01-04 13:49:42'),
(69, 1, 104, 'Delivery person (Jishan Islam) accepted delivery for Order #104.', 0, '2026-01-04 13:50:01'),
(70, 3, 104, 'Your order #104 delivery status updated: ASSIGNED', 1, '2026-01-04 13:52:07'),
(71, 1, 104, 'Delivery person (Karim Rahman) accepted delivery for Order #104.', 0, '2026-01-04 13:52:48'),
(72, 1, 104, 'Delivery person (Karim Rahman) accepted delivery for Order #104.', 0, '2026-01-04 13:53:42'),
(73, 1, 104, 'Delivery person (Karim Rahman) accepted delivery for Order #104.', 0, '2026-01-04 13:53:46'),
(74, 3, 104, 'Your order #104 delivery status updated: OUT FOR DELIVERY', 1, '2026-01-04 13:54:16'),
(75, 1, 104, 'Delivery person (Karim Rahman) accepted delivery for Order #104.', 0, '2026-01-04 13:55:02'),
(76, 1, 104, 'COD COLLECTED: Delivery person (Karim Rahman) collected Tk 2344.68 for Order #104. Please confirm payment & complete order.', 0, '2026-01-04 13:55:10'),
(77, 3, 104, 'Your COD payment for order #104 has been received. Order delivered successfully.', 1, '2026-01-04 13:55:57'),
(78, 2, 104, 'Admin confirmed COD payment for Order #104. Delivery completed.', 0, '2026-01-04 13:55:57'),
(79, 1, 104, 'COD payment confirmed for Order #104. Order marked Delivered & Completed.', 0, '2026-01-04 13:55:57'),
(80, 1, 104, 'COD COLLECTED: Delivery person (Karim Rahman) collected Tk 2344.68 for Order #104. Please confirm payment & complete order.', 0, '2026-01-04 13:56:10'),
(81, 12, 109, 'Your order #109 delivery status updated: ASSIGNED', 1, '2026-01-05 00:04:09'),
(82, 12, 109, 'Your order #109 is out for delivery.', 1, '2026-01-05 00:05:47'),
(83, 1, 109, 'Delivery person (Karim Rahman) set Order #109 as OUT FOR DELIVERY.', 0, '2026-01-05 00:05:47'),
(84, 12, 109, 'Your order #109 delivery status updated: DELIVERED', 1, '2026-01-05 00:39:38'),
(85, 12, 110, 'Your order #110 delivery status updated: ASSIGNED', 1, '2026-01-05 00:54:30'),
(86, 1, 110, 'Delivery person (Jishan Islam) accepted delivery for Order #110.', 0, '2026-01-05 00:55:37'),
(87, 12, 110, 'Your order #110 is out for delivery.', 1, '2026-01-05 00:55:45'),
(88, 1, 110, 'Delivery person (Jishan Islam) set Order #110 as OUT FOR DELIVERY.', 0, '2026-01-05 00:55:45'),
(89, 12, 110, 'Your order #110 is out for delivery.', 1, '2026-01-05 01:31:02'),
(90, 1, 110, 'Delivery person (Jishan Islam) set Order #110 as OUT FOR DELIVERY.', 0, '2026-01-05 01:31:02'),
(91, 1, 110, 'Delivery person (Jishan Islam) accepted delivery for Order #110.', 0, '2026-01-05 01:31:15'),
(92, 1, 110, 'COD COLLECTED: Delivery person (Jishan Islam) collected Tk 1580.00 for Order #110. Please confirm payment & complete order.', 0, '2026-01-05 01:31:22'),
(93, 1, 110, 'COD COLLECTED: Delivery person (Jishan Islam) collected Tk 1580.00 for Order #110. Please confirm payment & complete order.', 0, '2026-01-05 01:38:22'),
(94, 12, 110, 'Your COD payment for order #110 has been received. Order delivered successfully.', 1, '2026-01-05 01:38:34'),
(95, 1, 110, 'Admin confirmed COD payment for Order #110. Delivery completed.', 0, '2026-01-05 01:38:34'),
(96, 1, 110, 'COD payment confirmed for Order #110. Order marked Delivered & Completed.', 0, '2026-01-05 01:38:34'),
(97, 12, 0, ' Your order #8 has been cancelled successfully.', 1, '2026-01-05 01:39:15'),
(98, 12, 107, 'Your order #107 delivery status updated: ASSIGNED', 1, '2026-01-05 01:39:35'),
(99, 1, 110, 'COD COLLECTED: Delivery person (Jishan Islam) collected Tk 1580.00 for Order #110. Please confirm payment & complete order.', 0, '2026-01-05 01:39:45'),
(100, 12, 107, 'Your order #107 delivery status updated: OUT FOR DELIVERY', 1, '2026-01-05 01:42:18'),
(101, 12, 68, 'Your order #68 delivery status updated: ASSIGNED', 1, '2026-01-05 03:13:52'),
(102, 12, 68, 'Your order #68 delivery status updated: OUT FOR DELIVERY', 1, '2026-01-05 03:14:17'),
(103, 1, 68, 'Delivery person (Karim Rahman) accepted delivery for Order #68.', 0, '2026-01-05 03:14:27'),
(104, 12, 68, 'Your order #68 has been delivered successfully.', 1, '2026-01-05 03:26:20'),
(105, 1, 68, 'Delivery DONE! (Karim Rahman) delivered Order #68.', 0, '2026-01-05 03:26:20'),
(106, 12, 111, 'Your order #111 delivery status updated: ASSIGNED', 1, '2026-01-05 03:28:40'),
(107, 1, 111, 'Delivery person (Karim Rahman) accepted delivery for Order #111.', 0, '2026-01-05 03:29:23'),
(108, 12, 111, 'Your order #111 is out for delivery.', 1, '2026-01-05 03:29:37'),
(109, 1, 111, 'Delivery person (Karim Rahman) set Order #111 as OUT FOR DELIVERY.', 0, '2026-01-05 03:29:37'),
(110, 12, 111, 'Your order #111 is out for delivery.', 1, '2026-01-05 03:55:36'),
(111, 1, 111, 'Delivery person (Karim Rahman) set Order #111 as OUT FOR DELIVERY.', 0, '2026-01-05 03:55:36'),
(112, 1, 111, 'COD COLLECTED: Delivery person (Karim Rahman) collected Tk 210.00 for Order #111. Please confirm payment & complete order.', 0, '2026-01-05 03:55:44'),
(113, 12, 111, 'Your COD payment for order #111 has been received. Order delivered successfully.', 1, '2026-01-05 03:55:56'),
(114, 2, 111, 'Admin confirmed COD payment for Order #111. Delivery completed.', 0, '2026-01-05 03:55:56'),
(115, 1, 111, 'COD payment confirmed for Order #111. Order marked Delivered & Completed.', 0, '2026-01-05 03:55:56'),
(116, 7, 112, 'Your order #112 delivery status updated: ASSIGNED', 1, '2026-01-07 01:02:38'),
(117, 1, 112, 'Delivery person (Jishan Islam) accepted delivery for Order #112.', 0, '2026-01-07 01:04:11'),
(118, 7, 0, ' Your order #113 has been cancelled and ৳408.24 refunded to your account.', 1, '2026-01-07 01:37:29'),
(119, 3, 103, 'Your order #103 delivery status updated: ASSIGNED', 1, '2026-01-07 01:49:46'),
(120, 1, 112, 'Delivery person (Jishan Islam) accepted delivery for Order #112.', 0, '2026-01-07 01:50:12'),
(121, 7, 114, 'Your order #114 delivery status updated: ASSIGNED', 1, '2026-01-07 04:54:46'),
(122, 7, 114, 'Your order #114 delivery status updated: OUT FOR DELIVERY', 1, '2026-01-07 04:55:11'),
(137, 12, 107, 'Your order #107 has been delivered successfully.', 1, '2026-01-07 11:31:24'),
(138, 1, 107, 'Delivery DONE! (Karim Rahman) delivered Order #107.', 0, '2026-01-07 11:31:24'),
(139, 7, 0, ' Your order #116 has been cancelled and ৳48.38 refunded to your account.', 1, '2026-01-07 12:00:02'),
(140, 7, 118, 'Your order #118 delivery status updated: ASSIGNED', 1, '2026-01-10 09:00:29'),
(141, 7, 117, 'Your order #117 delivery status updated: ASSIGNED', 1, '2026-01-10 09:04:43'),
(142, 7, 115, 'Your order #115 delivery status updated: ASSIGNED', 1, '2026-01-10 09:05:15'),
(143, 7, 108, 'Your order #108 delivery status updated: ASSIGNED', 1, '2026-01-10 09:05:31'),
(144, 3, 106, 'Your order #106 delivery status updated: ASSIGNED', 1, '2026-01-10 09:05:55'),
(145, 3, 102, 'Your order #102 delivery status updated: ASSIGNED', 1, '2026-01-10 09:06:07'),
(146, 9, 101, 'Your order #101 delivery status updated: ASSIGNED', 0, '2026-01-10 09:06:47'),
(147, 9, 95, 'Your order #95 delivery status updated: ASSIGNED', 0, '2026-01-10 09:07:13'),
(148, 7, 94, 'Your order #94 delivery status updated: ASSIGNED', 1, '2026-01-10 09:07:24'),
(149, 1, 118, 'Delivery person (Jishan Islam) accepted delivery for Order #118.', 0, '2026-01-10 09:23:29'),
(150, 1, 118, 'Delivery person (Jishan Islam) accepted delivery for Order #118.', 0, '2026-01-10 09:41:08'),
(151, 7, 118, 'Your order #118 is out for delivery.', 1, '2026-01-10 10:01:05'),
(152, 1, 118, 'Delivery person (Jishan Islam) set Order #118 as OUT FOR DELIVERY.', 0, '2026-01-10 10:01:05'),
(153, 7, 117, 'Your order #117 delivery status updated: OUT FOR DELIVERY', 1, '2026-01-10 10:01:49'),
(154, 1, 114, 'Delivery person (Jishan Islam) accepted delivery for Order #114.', 0, '2026-01-10 10:02:08'),
(155, 7, 121, 'Your order #121 delivery status updated: ASSIGNED', 1, '2026-01-10 11:20:53'),
(156, 1, 121, 'Delivery person (Jishan Islam) accepted delivery for Order #121.', 0, '2026-01-10 11:21:55'),
(157, 7, 121, 'Your order #121 is out for delivery.', 1, '2026-01-10 11:22:48'),
(158, 1, 121, 'Delivery person (Jishan Islam) set Order #121 as OUT FOR DELIVERY.', 0, '2026-01-10 11:22:48'),
(159, 1, 121, 'COD COLLECTED: Delivery person (Jishan Islam) collected Tk 615.00 for Order #121. Please confirm payment & complete order.', 0, '2026-01-10 11:24:46'),
(160, 7, 121, 'Your COD payment for order #121 has been received. Order delivered successfully.', 1, '2026-01-10 11:25:16'),
(161, 1, 121, 'Admin confirmed COD payment for Order #121. Delivery completed.', 0, '2026-01-10 11:25:16'),
(162, 1, 121, 'COD payment confirmed for Order #121. Order marked Delivered & Completed.', 0, '2026-01-10 11:25:16'),
(163, 1, 121, 'COD COLLECTED: Delivery person (Jishan Islam) collected Tk 615.00 for Order #121. Please confirm payment & complete order.', 0, '2026-01-10 11:25:49'),
(164, 7, 122, 'Your order #122 delivery status updated: ASSIGNED', 1, '2026-01-10 11:31:50'),
(165, 7, 122, 'Your order #122 delivery status updated: ASSIGNED', 1, '2026-01-10 11:32:09'),
(166, 1, 122, 'Delivery person (Karim Rahman) accepted delivery for Order #122.', 0, '2026-01-10 11:33:30'),
(167, 7, 122, 'Your order #122 is out for delivery.', 1, '2026-01-10 11:34:07'),
(168, 1, 122, 'Delivery person (Karim Rahman) set Order #122 as OUT FOR DELIVERY.', 0, '2026-01-10 11:34:07'),
(169, 7, 122, 'Your order #122 has been delivered successfully.', 1, '2026-01-10 11:35:04'),
(170, 1, 122, 'Delivery DONE! (Karim Rahman) delivered Order #122.', 0, '2026-01-10 11:35:04'),
(171, 7, 0, ' Your order #123 has been cancelled and ৳224.40 refunded to your account.', 1, '2026-01-10 12:22:07'),
(172, 7, 120, 'Your order #120 delivery status updated: ASSIGNED', 1, '2026-01-11 01:44:29'),
(173, 3, 124, 'Your order #124 delivery status updated: ASSIGNED', 0, '2026-01-11 03:17:23'),
(174, 1, 124, 'Delivery person (Karim Rahman) accepted delivery for Order #124.', 0, '2026-01-11 12:42:03'),
(175, 3, 124, 'Your order #124 is out for delivery.', 0, '2026-01-11 12:42:22'),
(176, 1, 124, 'Delivery person (Karim Rahman) set Order #124 as OUT FOR DELIVERY.', 0, '2026-01-11 12:42:22'),
(177, 3, 124, 'Your order #124 has been delivered successfully.', 0, '2026-01-11 12:44:47'),
(178, 1, 124, 'Delivery DONE! (Karim Rahman) delivered Order #124.', 0, '2026-01-11 12:44:47'),
(179, 3, 124, 'Your order #124 has been delivered successfully.', 0, '2026-01-11 12:44:50'),
(180, 1, 124, 'Delivery DONE! (Karim Rahman) delivered Order #124.', 0, '2026-01-11 12:44:50'),
(181, 3, 124, 'Your order #124 has been delivered successfully.', 0, '2026-01-11 12:46:41'),
(182, 1, 124, 'Delivery DONE! (Karim Rahman) delivered Order #124.', 0, '2026-01-11 12:46:41'),
(183, 7, 125, 'Your order #125 delivery status updated: ASSIGNED', 1, '2026-01-12 22:35:42'),
(184, 7, 88, 'Your order #88 delivery status updated: ASSIGNED', 1, '2026-01-12 22:36:15'),
(185, 7, 119, 'Your order #119 delivery status updated: ASSIGNED', 1, '2026-01-12 22:36:46'),
(186, 7, 93, 'Your order #93 delivery status updated: ASSIGNED', 1, '2026-01-12 22:37:12'),
(187, 7, 83, 'Your order #83 delivery status updated: ASSIGNED', 1, '2026-01-13 09:25:01'),
(188, 3, 63, 'Your order #63 delivery status updated: ASSIGNED', 0, '2026-01-13 09:25:25'),
(189, 12, 0, ' Your order #42 has been cancelled successfully.', 1, '2026-01-13 21:41:24'),
(190, 16, 0, ' Your order #134 has been cancelled and ৳4,643.88 refunded to your account.', 1, '2026-01-14 02:25:32'),
(191, 9, 133, 'Your order #133 delivery status updated: ASSIGNED', 0, '2026-01-14 02:31:11'),
(192, 9, 133, 'Your order #133 has been delivered successfully.', 0, '2026-01-14 02:31:37'),
(193, 1, 133, 'Delivery DONE! (Emon Halder) delivered Order #133.', 0, '2026-01-14 02:31:37'),
(194, 1, 133, 'Delivery person (Emon Halder) accepted delivery for Order #133.', 0, '2026-01-14 03:34:01'),
(195, 16, 138, 'Your order #138 delivery status updated: ASSIGNED', 0, '2026-01-14 03:36:34'),
(196, 1, 138, 'Delivery person (Emon Halder) accepted delivery for Order #138.', 0, '2026-01-14 03:38:05'),
(197, 16, 138, 'Your order #138 is out for delivery.', 0, '2026-01-14 03:38:56'),
(198, 1, 138, 'Delivery person (Emon Halder) set Order #138 as OUT FOR DELIVERY.', 0, '2026-01-14 03:38:56'),
(199, 1, 138, 'COD COLLECTED: Delivery person (Emon Halder) collected Tk 666.9 for Order #138. Please confirm payment & complete order.', 0, '2026-01-14 03:39:38'),
(200, 16, 138, 'Your COD payment for order #138 has been received. Order delivered successfully.', 0, '2026-01-14 03:39:49'),
(201, 3, 138, 'Admin confirmed COD payment for Order #138. Delivery completed.', 0, '2026-01-14 03:39:49'),
(202, 1, 138, 'COD payment confirmed for Order #138. Order marked Delivered & Completed.', 0, '2026-01-14 03:39:49'),
(203, 1, 138, 'COD COLLECTED: Delivery person (Emon Halder) collected Tk 666.9 for Order #138. Please confirm payment & complete order.', 0, '2026-01-14 03:39:59'),
(204, 16, 137, 'Your order #137 delivery status updated: ASSIGNED', 0, '2026-01-14 03:40:56'),
(205, 16, 136, 'Your order #136 delivery status updated: ASSIGNED', 0, '2026-01-14 03:45:10'),
(206, 16, 135, 'Your order #135 delivery status updated: ASSIGNED', 0, '2026-01-14 09:58:57'),
(207, 12, 129, 'Your order #129 delivery status updated: ASSIGNED', 1, '2026-01-14 10:21:14'),
(208, 12, 127, 'Your order #127 delivery status updated: ASSIGNED', 1, '2026-01-14 10:29:04'),
(209, 12, 128, 'Your order #128 delivery status updated: ASSIGNED', 1, '2026-01-14 10:30:11'),
(210, 7, 131, 'Your order #131 delivery status updated: ASSIGNED', 0, '2026-01-16 03:17:25'),
(211, 7, 141, 'Your order #141 delivery status updated: ASSIGNED', 0, '2026-01-16 03:22:17'),
(212, 1, 141, 'Delivery person (Emon Halder) accepted delivery for Order #141.', 0, '2026-01-16 03:22:30'),
(213, 7, 141, 'Your order #141 delivery status updated: OUT FOR DELIVERY', 0, '2026-01-16 15:29:59');

-- --------------------------------------------------------

--
-- Table structure for table `offers`
--

CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `discount_percent` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `start_at` datetime DEFAULT NULL,
  `end_date` date NOT NULL,
  `end_at` datetime DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offers`
--

INSERT INTO `offers` (`id`, `title`, `description`, `discount_percent`, `start_date`, `start_at`, `end_date`, `end_at`, `is_active`, `created_at`) VALUES
(8, 'Winter Offer', 'Winter best offer for Personal care', 20, '2026-01-11', '2026-01-11 01:00:00', '2026-01-11', '2026-01-11 03:00:00', 1, '2026-01-10 18:56:35'),
(9, 'Best Chocolate Offer', 'Winter Chocolate offer', 5, '2026-01-11', NULL, '2026-01-12', NULL, 1, '2026-01-10 19:00:44'),
(10, 'Winter todays offer', '', 10, '2026-01-11', '2026-01-11 14:20:00', '2026-01-11', '2026-01-11 18:20:00', 1, '2026-01-11 07:17:15'),
(11, 'Winter  Best Offer', 'Today\'s offer are great', 10, '2026-01-14', '2026-01-14 11:32:00', '2026-01-14', '2026-01-14 13:30:00', 1, '2026-01-14 05:32:04');

-- --------------------------------------------------------

--
-- Table structure for table `offer_products`
--

CREATE TABLE `offer_products` (
  `id` int(11) NOT NULL,
  `offer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `offer_products`
--

INSERT INTO `offer_products` (`id`, `offer_id`, `product_id`) VALUES
(44, 8, 25),
(43, 8, 27),
(42, 8, 28),
(54, 9, 21),
(53, 9, 22),
(58, 10, 38),
(57, 10, 39),
(64, 11, 26),
(63, 11, 28),
(62, 11, 29);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `customer_address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `zip` varchar(20) DEFAULT NULL,
  `items_total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `shipping_amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `delivery_area` varchar(30) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` datetime DEFAULT current_timestamp(),
  `delivery_status` enum('not_assigned','assigned','out_for_delivery','delivered') DEFAULT 'not_assigned',
  `delivery_person_id` int(11) DEFAULT NULL,
  `delivery_person` varchar(100) DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `transaction_id` varchar(100) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT 'COD',
  `payment_status` varchar(50) DEFAULT 'Pending',
  `cancelled_at` datetime DEFAULT NULL,
  `dp_accepted` tinyint(1) NOT NULL DEFAULT 0,
  `accepted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `customer_phone`, `customer_address`, `city`, `zip`, `items_total`, `tax_amount`, `shipping_amount`, `delivery_area`, `total_amount`, `status`, `created_at`, `delivery_status`, `delivery_person_id`, `delivery_person`, `delivery_date`, `transaction_id`, `payment_method`, `payment_status`, `cancelled_at`, `dp_accepted`, `accepted_at`) VALUES
(1, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 449.00, 'Pending', '2025-12-26 13:28:06', 'assigned', NULL, 'Naim Mia', '2025-12-29', NULL, 'COD', 'Pending', NULL, 0, NULL),
(2, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 2860.00, 'Pending', '2025-12-26 21:50:40', 'assigned', NULL, 'Wahab Alam', '2025-12-30', NULL, 'COD', 'Pending', NULL, 0, NULL),
(3, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1450.00, 'Pending', '2025-12-26 22:04:53', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(4, 8, 'Anamika Akter', '01998714402', 'House Building', 'Dhaka', '1218', 0.00, 0.00, 0.00, NULL, 949.00, 'Pending', '2025-12-26 22:11:42', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(5, 9, 'Prime Das', '01883677845', 'Rampura', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 649.00, 'Pending', '2025-12-26 22:36:07', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(6, 12, 'Moon Akter', '01993433391', 'Rampura', 'Dhaka', '1840', 0.00, 0.00, 0.00, NULL, 449.00, 'Pending', '2025-12-27 08:40:31', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(7, 12, 'Moon Akter', '01993433391', 'Rampura', 'Dhaka', '1840', 0.00, 0.00, 0.00, NULL, 300.00, 'Pending', '2025-12-27 08:50:11', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(8, 12, 'Moon Akter', '01993433391', 'Rampura', 'Dhaka', '1840', 0.00, 0.00, 0.00, NULL, 10.00, 'cancelled', '2025-12-27 08:55:17', 'not_assigned', NULL, NULL, '0000-00-00', NULL, 'COD', 'Cancelled', '2026-01-05 01:39:15', 0, NULL),
(9, 8, 'Anamika Akter', '01998714402', 'House Building', 'Dhaka', '1218', 0.00, 0.00, 0.00, NULL, 500.00, 'Pending', '2025-12-27 09:05:00', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(10, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1290.00, 'Pending', '2025-12-27 15:00:36', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(11, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1570.00, 'Pending', '2025-12-27 15:01:09', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(12, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 2580.00, 'cancelled', '2025-12-27 15:04:14', 'assigned', 5, 'Enamul Hoq', '0000-00-00', NULL, 'COD', 'Pending', '2026-01-02 20:50:57', 0, NULL),
(13, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 1290.00, 'Pending', '2025-12-28 11:27:36', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(14, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 300.00, 'Pending', '2025-12-28 13:06:31', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(15, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 305.00, 'Pending', '2025-12-28 19:37:37', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(16, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1450.00, 'Pending', '2025-12-28 19:38:02', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(18, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 1450.00, 'Pending', '2025-12-28 19:56:41', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(19, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 10.00, 'Pending', '2025-12-28 20:14:19', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(20, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 449.00, 'Pending', '2025-12-28 21:16:28', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(21, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 300.00, 'cancelled', '2025-12-28 21:28:22', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', '2025-12-31 02:39:39', 0, NULL),
(22, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 1450.00, 'Pending', '2025-12-28 23:03:00', 'not_assigned', NULL, NULL, NULL, 'SSLCZ_TEST_6951722643242', 'SSLCommerz', 'Success', NULL, 0, NULL),
(37, 9, 'Unknown Name', 'N/A', 'N/A', 'N/A', 'N/A', 0.00, 0.00, 0.00, NULL, 561.12, 'Pending', '2025-12-29 02:06:35', 'not_assigned', NULL, NULL, NULL, '37', 'SSLCommerz', 'Success', NULL, 0, NULL),
(38, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 60.12, 'cancelled', '2025-12-29 02:11:08', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', '2025-12-31 02:39:28', 0, NULL),
(39, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 60.12, 'Pending', '2025-12-29 02:11:35', 'not_assigned', NULL, NULL, NULL, '39', 'SSLCommerz', 'Success', NULL, 0, NULL),
(40, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 180.36, 'Pending', '2025-12-29 02:14:45', 'not_assigned', NULL, NULL, NULL, '40', 'SSLCommerz', 'Success', NULL, 0, NULL),
(41, 9, 'Prime Das', '01883677845', 'Rampura', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 195.39, 'Pending', '2025-12-29 02:25:05', 'not_assigned', NULL, NULL, NULL, '41', 'SSLCommerz', 'Success', NULL, 0, NULL),
(42, 12, 'Moon Akter', '01993433391', 'Rampura', 'Dhaka', '1840', 0.00, 0.00, 0.00, NULL, 270.54, 'cancelled', '2025-12-29 02:29:51', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Cancelled', '2026-01-13 21:41:24', 0, NULL),
(43, 12, 'Moon Akter', '01993433391', 'Rampura', 'Dhaka', '1840', 0.00, 0.00, 0.00, NULL, 270.54, 'Pending', '2025-12-29 02:30:20', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(44, 12, 'Moon Akter', '01993433391', 'Rampura', 'Dhaka', '1840', 0.00, 0.00, 0.00, NULL, 270.54, 'Pending', '2025-12-29 02:30:25', 'not_assigned', NULL, NULL, NULL, '44', 'SSLCommerz', 'Success', NULL, 0, NULL),
(45, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 50.10, 'Pending', '2025-12-29 02:40:15', 'not_assigned', NULL, NULL, NULL, '45', 'SSLCommerz', 'Success', NULL, 0, NULL),
(46, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1202.40, 'Pending', '2025-12-29 02:48:47', 'not_assigned', NULL, NULL, NULL, '46', 'SSLCommerz', 'Success', NULL, 0, NULL),
(47, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 25.05, 'Pending', '2025-12-29 03:17:31', 'not_assigned', NULL, NULL, NULL, '47', 'SSLCommerz', 'Success', NULL, 0, NULL),
(48, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 561.12, 'cancelled', '2025-12-29 03:38:04', 'not_assigned', NULL, NULL, NULL, '48', 'SSLCommerz', 'Refunded', '2026-01-03 02:02:13', 0, NULL),
(49, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 180.36, 'processing', '2025-12-29 03:40:29', 'assigned', 5, 'Enamul Hoq', '2026-01-06', '49', 'SSLCommerz', 'Success', NULL, 0, NULL),
(50, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1793.58, 'Pending', '2025-12-29 03:45:09', 'not_assigned', NULL, NULL, NULL, '50', 'SSLCommerz', 'Success', NULL, 0, NULL),
(51, 9, 'Prime Das', '01883677845', 'Rampura', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 2294.58, 'Processing', '2025-12-29 04:21:12', 'assigned', 6, 'Wahab Alam', '2026-01-08', '51', 'SSLCommerz', 'Success', NULL, 0, NULL),
(52, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 245.49, 'processing', '2025-12-29 04:24:37', 'assigned', 5, 'Enamul Hoq', '2026-01-06', '52', 'SSLCommerz', 'Success', NULL, 0, NULL),
(53, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 1742.48, 'processing', '2025-12-29 04:28:08', 'assigned', 5, 'Enamul Hoq', '2026-01-06', '53', 'SSLCommerz', 'Success', NULL, 0, NULL),
(54, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 2294.58, 'processing', '2025-12-29 11:36:08', 'assigned', 5, 'Enamul Hoq', '2026-01-06', NULL, 'COD', 'Pending', NULL, 0, NULL),
(55, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 1793.58, 'Pending', '2025-12-30 11:51:51', 'not_assigned', NULL, NULL, NULL, '55', 'SSLCommerz', 'Success', NULL, 0, NULL),
(56, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1292.58, 'cancelled', '2025-12-30 12:28:47', 'not_assigned', NULL, NULL, NULL, '56', 'SSLCommerz', 'Refunded', '2026-01-03 00:53:01', 0, NULL),
(57, 9, 'Prime Das', '01883677845', 'Rampura', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1753.50, 'processing', '2025-12-30 12:50:37', 'assigned', 5, 'Enamul Hoq', '2026-01-06', '57', 'SSLCommerz', 'Success', NULL, 0, NULL),
(58, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 50.10, 'cancelled', '2025-12-30 14:03:49', 'not_assigned', NULL, NULL, NULL, '58', 'SSLCommerz', 'Success', '2025-12-31 01:33:11', 0, NULL),
(60, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 1290.00, 'cancelled', '2025-12-30 16:23:19', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', '2025-12-30 22:04:25', 0, NULL),
(61, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 999.00, 'cancelled', '2025-12-30 16:25:43', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', '2025-12-30 21:48:21', 0, NULL),
(62, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 1450.00, 'cancelled', '2025-12-30 21:02:58', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', '2025-12-30 21:48:13', 0, NULL),
(63, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 300.60, 'Processing', '2025-12-30 22:23:36', 'assigned', 3, 'Emon Halder', NULL, '63', 'SSLCommerz', 'Success', NULL, 0, NULL),
(64, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1292.58, 'Processing', '2026-01-01 15:43:52', 'assigned', 6, 'Wahab Alam', '2026-01-08', '64', 'SSLCommerz', 'Success', NULL, 0, NULL),
(65, 9, 'Prime Das', '01883677845', 'Rampura', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1290.00, 'processing', '2026-01-01 15:48:21', 'assigned', 7, 'Karim Rahman', '2026-01-08', NULL, 'COD', 'Pending', NULL, 0, NULL),
(66, 9, 'Prime Das', '01883677845', 'Rampura', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 2900.00, 'cancelled', '2026-01-01 15:49:11', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Cancelled', '2026-01-03 01:11:10', 0, NULL),
(67, 9, 'Prime Das', '01883677845', 'Rampura', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1452.90, 'processing', '2026-01-01 15:49:23', 'assigned', 7, 'Karim Rahman', '2026-01-05', '67', 'SSLCommerz', 'Success', NULL, 0, NULL),
(68, 12, 'Moon Akter', '01993433391', 'Rampura', 'Dhaka', '1840', 0.00, 0.00, 0.00, NULL, 1673.34, 'Completed', '2026-01-01 16:08:04', 'delivered', 2, 'Karim Rahman', '2026-01-06', '68', 'SSLCommerz', 'Success', NULL, 0, NULL),
(69, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1202.40, 'processing', '2026-01-01 16:14:20', 'assigned', 5, 'Enamul Hoq', '2026-01-06', NULL, 'COD', 'Pending', NULL, 0, NULL),
(70, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1290.00, 'processing', '2026-01-01 16:19:18', 'assigned', 5, 'Enamul Hoq', '2026-01-06', NULL, 'COD', 'Pending', NULL, 0, NULL),
(71, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1450.00, 'processing', '2026-01-01 16:24:23', 'assigned', 5, 'Enamul Hoq', '2026-01-06', NULL, 'COD', 'Pending', NULL, 0, NULL),
(72, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 500.00, 'processing', '2026-01-01 16:25:13', 'assigned', 5, 'Enamul Hoq', '2026-01-06', NULL, 'COD', 'Pending', NULL, 0, NULL),
(73, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 889.00, 'processing', '2026-01-01 16:30:30', 'assigned', 5, 'Enamul Hoq', '2026-01-06', NULL, 'COD', 'Pending', NULL, 0, NULL),
(74, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1450.00, 'processing', '2026-01-01 16:36:56', 'assigned', 5, 'Enamul Hoq', '2026-01-06', NULL, 'COD', 'Pending', NULL, 0, NULL),
(75, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1452.90, 'shipped', '2026-01-01 17:01:18', 'out_for_delivery', 5, 'Enamul Hoq', '2026-01-06', NULL, 'SSLCommerz', 'Pending', NULL, 0, NULL),
(76, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1292.58, 'shipped', '2026-01-01 17:06:44', 'out_for_delivery', 5, 'Enamul Hoq', '2026-01-06', NULL, 'SSLCommerz', 'Pending', NULL, 0, NULL),
(77, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 15.03, 'processing', '2026-01-01 17:09:12', 'assigned', 5, 'Enamul Hoq', '2026-01-05', NULL, 'SSLCommerz', 'Pending', NULL, 0, NULL),
(78, 9, 'Prime Das', '01883677845', 'Rampura', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 180.36, 'processing', '2026-01-01 17:22:01', 'assigned', 5, 'Enamul Hoq', '2026-01-13', '78', 'SSLCommerz', 'Success', NULL, 0, NULL),
(79, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1292.58, 'processing', '2026-01-01 17:23:30', 'assigned', 5, 'Enamul Hoq', '2026-01-08', '79', 'SSLCommerz', 'Success', NULL, 0, NULL),
(80, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1292.58, 'processing', '2026-01-01 17:28:45', 'assigned', 7, 'Karim Rahman', '2026-01-07', '80', 'SSLCommerz', 'Success', NULL, 0, NULL),
(81, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 560.00, 'shipped', '2026-01-02 20:52:51', 'out_for_delivery', 5, 'Enamul Hoq', '2026-01-07', NULL, 'COD', 'Pending', NULL, 0, NULL),
(83, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1312.62, 'Processing', '2026-01-02 23:46:51', 'assigned', 3, 'Emon Halder', NULL, '83', 'SSLCommerz', 'Success', NULL, 0, NULL),
(84, 9, 'Prime Das', '01883677845', 'Rampura', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1305.00, 'Shipped', '2026-01-02 23:51:57', 'out_for_delivery', 7, 'Karim Rahman', NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(85, 9, 'Prime Das', '01883677845', 'Rampura', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 360.72, 'cancelled', '2026-01-03 00:32:02', 'not_assigned', NULL, NULL, NULL, '85', 'SSLCommerz', 'Refunded', '2026-01-03 01:08:55', 0, NULL),
(86, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 2121.23, 'Completed', '2026-01-03 02:04:33', 'delivered', 7, 'Karim Rahman', '2026-01-13', NULL, 'COD', 'Paid', NULL, 0, NULL),
(87, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 2121.23, 'cancelled', '2026-01-03 02:04:38', 'not_assigned', NULL, NULL, NULL, '87', 'SSLCommerz', 'Refunded', '2026-01-04 13:29:57', 0, NULL),
(88, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 3161.31, 'Processing', '2026-01-03 02:07:19', 'assigned', 1, 'Jishan Islam', NULL, '88', 'SSLCommerz', 'Success', NULL, 0, NULL),
(89, 15, 'Piku Hoq', '01998434352', 'Station Road', 'Uttara', '1215', 0.00, 0.00, 0.00, NULL, 8241.45, 'Completed', '2026-01-03 02:10:53', 'delivered', 7, 'Karim Rahman', '2026-01-04', NULL, 'COD', 'Paid', NULL, 0, NULL),
(90, 15, 'Piku Hoq', '01998434352', 'Station Road', 'Uttara', '1215', 0.00, 0.00, 0.00, NULL, 8241.45, 'Shipped', '2026-01-03 02:10:57', 'out_for_delivery', 5, 'Enamul Hoq', '2026-01-08', '90', 'SSLCommerz', 'Success', NULL, 0, NULL),
(91, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 3443.87, 'cancelled', '2026-01-03 02:12:57', 'not_assigned', NULL, NULL, NULL, '91', 'SSLCommerz', 'Refunded', '2026-01-03 02:14:32', 0, NULL),
(92, 15, 'Piku Hoq', '01998434352', 'Station Road', 'Uttara', '1215', 0.00, 0.00, 0.00, NULL, 1290.00, 'cancelled', '2026-01-03 04:07:56', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Cancelled', '2026-01-03 04:08:07', 0, NULL),
(93, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 761.52, 'Processing', '2026-01-03 14:08:45', 'assigned', 3, 'Emon Halder', NULL, '93', 'SSLCommerz', 'Success', NULL, 0, NULL),
(94, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1989.00, 'Processing', '2026-01-03 14:11:46', 'assigned', 1, 'Jishan Islam', NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(95, 9, 'Prime Das', '01883677845', 'Rampura', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 2595.00, 'Processing', '2026-01-03 14:14:22', 'assigned', 1, 'Jishan Islam', NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(96, 9, 'Prime Das', '01883677845', 'Rampura', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 660.00, 'Completed', '2026-01-03 14:14:56', 'delivered', 1, 'Jishan Islam', '2026-01-08', NULL, 'COD', 'Paid', NULL, 0, NULL),
(97, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1710.00, 'Completed', '2026-01-03 14:47:01', 'delivered', 5, 'Enamul Hoq', '2026-01-08', NULL, 'COD', 'Paid', NULL, 0, NULL),
(98, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 130.26, 'Completed', '2026-01-03 14:52:42', 'delivered', 1, 'Jishan Islam', '2026-01-08', '98', 'SSLCommerz', 'Success', NULL, 0, NULL),
(99, 12, 'Moon Akter', '01993433391', 'Rampura', 'Dhaka', '1840', 0.00, 0.00, 0.00, NULL, 1550.00, 'Completed', '2026-01-04 04:44:36', 'delivered', 1, 'Jishan Islam', '2026-01-08', NULL, 'COD', 'Paid', NULL, 0, NULL),
(100, 9, 'Prime Das', '01883677845', 'Rampura', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1920.00, 'Completed', '2026-01-04 05:55:53', 'delivered', 1, 'Jishan Islam', '2026-01-08', NULL, 'COD', 'Paid', NULL, 0, NULL),
(101, 9, 'Prime Das', '01883677845', 'Rampura', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 816.63, 'Processing', '2026-01-04 05:56:30', 'assigned', 1, 'Jishan Islam', NULL, '101', 'SSLCommerz', 'Success', NULL, 0, NULL),
(102, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 2344.68, 'Processing', '2026-01-04 13:32:14', 'assigned', 1, 'Jishan Islam', NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(103, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 2344.68, 'Processing', '2026-01-04 13:32:19', 'assigned', 1, 'Jishan Islam', NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(104, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 2344.68, 'Completed', '2026-01-04 13:32:27', 'delivered', 2, 'Karim Rahman', '2026-01-05', NULL, 'COD', 'Paid', NULL, 0, NULL),
(105, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 2344.68, 'Completed', '2026-01-04 13:38:51', 'delivered', 1, 'Jishan Islam', '2026-01-05', '105', 'SSLCommerz', 'Success', NULL, 0, NULL),
(106, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 1202.40, 'Processing', '2026-01-04 21:23:25', 'assigned', 1, 'Jishan Islam', NULL, '106', 'SSLCommerz', 'Success', NULL, 0, NULL),
(107, 12, 'Moon Akter', '01993433391', 'Rampura', 'Dhaka', '1840', 0.00, 0.00, 0.00, NULL, 2585.16, 'Completed', '2026-01-04 21:26:29', 'delivered', 2, 'Karim Rahman', NULL, '107', 'SSLCommerz', 'Success', NULL, 0, NULL),
(108, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 490.98, 'Processing', '2026-01-04 21:33:27', 'assigned', 1, 'Jishan Islam', NULL, '108', 'SSLCommerz', 'Success', NULL, 0, NULL),
(109, 12, 'Moon Akter', '01993433391', 'Rampura', 'Dhaka', '1840', 0.00, 0.00, 0.00, NULL, 1260.00, 'Completed', '2026-01-05 00:01:48', 'delivered', 2, 'Karim Rahman', '2026-01-07', '109', 'SSLCommerz', 'Success', NULL, 0, NULL),
(110, 12, 'Moon Akter', '01993433391', 'Rampura', 'Dhaka', '1840', 0.00, 0.00, 0.00, NULL, 1580.00, 'Completed', '2026-01-05 00:54:04', 'delivered', 1, 'Jishan Islam', '2026-01-08', NULL, 'COD', 'Paid', NULL, 0, NULL),
(111, 12, 'Moon Akter', '01993433391', 'Rampura', 'Dhaka', '1840', 0.00, 0.00, 0.00, NULL, 210.00, 'Completed', '2026-01-05 03:28:10', 'delivered', 2, 'Karim Rahman', '2026-01-06', NULL, 'COD', 'Paid', NULL, 0, NULL),
(112, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1431.36, 'Processing', '2026-01-06 20:51:56', 'assigned', 1, 'Jishan Islam', NULL, '112', 'SSLCommerz', 'Success', NULL, 0, NULL),
(113, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 408.24, 'cancelled', '2026-01-07 01:36:08', 'not_assigned', NULL, NULL, NULL, '113', 'SSLCommerz', 'Refunded', '2026-01-07 01:37:29', 0, NULL),
(114, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1305.00, 'Shipped', '2026-01-07 04:31:18', 'out_for_delivery', 1, 'Jishan Islam', '2026-01-08', NULL, 'COD', 'Pending', NULL, 0, NULL),
(115, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1679.33, 'Processing', '2026-01-07 04:57:18', 'assigned', 1, 'Jishan Islam', NULL, '115', 'SSLCommerz', 'Success', NULL, 0, NULL),
(116, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 48.38, 'cancelled', '2026-01-07 11:57:43', 'not_assigned', NULL, NULL, NULL, '116', 'SSLCommerz', 'Refunded', '2026-01-07 12:00:02', 0, NULL),
(117, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 195.00, 'Shipped', '2026-01-08 06:52:35', 'out_for_delivery', 2, 'Karim Rahman', NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(118, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 2632.90, 'Shipped', '2026-01-08 07:47:02', 'out_for_delivery', 1, 'Jishan Islam', NULL, '118', 'SSLCommerz', 'Success', NULL, 0, NULL),
(119, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 392.11, 'Processing', '2026-01-10 11:13:35', 'assigned', 2, 'Karim Rahman', NULL, '119', 'SSLCommerz', 'Success', NULL, 0, NULL),
(120, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 2090.00, 'Processing', '2026-01-10 11:15:20', 'assigned', 1, 'Jishan Islam', '2026-01-13', NULL, 'COD', 'Pending', NULL, 0, NULL),
(121, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 615.00, 'Completed', '2026-01-10 11:18:47', 'delivered', 1, 'Jishan Islam', '2026-01-12', NULL, 'COD', 'Paid', NULL, 0, NULL),
(122, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 790.27, 'Completed', '2026-01-10 11:26:40', 'delivered', 2, 'Karim Rahman', '2026-01-12', '122', 'SSLCommerz', 'Success', NULL, 0, NULL),
(123, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 224.40, 'cancelled', '2026-01-10 12:20:58', 'not_assigned', NULL, NULL, NULL, '123', 'SSLCommerz', 'Refunded', '2026-01-10 12:22:07', 0, NULL),
(124, 3, 'Jui Rahman Pranti', '01998746354', 'Tongi', 'Dhaka', '1217', 0.00, 0.00, 0.00, NULL, 1012.00, 'Completed', '2026-01-11 02:35:26', 'delivered', 2, 'Karim Rahman', '2026-01-13', '124', 'SSLCommerz', 'Success', NULL, 0, NULL),
(125, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 1346.95, 'Processing', '2026-01-11 21:45:23', 'assigned', 1, 'Jishan Islam', NULL, '125', 'SSLCommerz', 'Success', NULL, 0, NULL),
(126, 7, 'Maliha Akter', '01998733210', 'House Building', 'Dhaka', '1215', 0.00, 0.00, 0.00, NULL, 35.00, 'Pending', '2026-01-13 13:01:23', 'not_assigned', NULL, NULL, NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(127, 12, 'Moon Akter', '01993433391', 'Rampura', 'Dhaka', '1840', 3099.00, 61.98, 60.00, 'dhaka', 3220.98, 'Processing', '2026-01-13 23:09:15', 'assigned', 3, 'Emon Halder', '2026-01-16', NULL, 'COD', 'Pending', NULL, 0, NULL),
(128, 12, 'Moon Akter', '01993433391', 'Rampura', 'Dhaka', '1840', 1600.00, 32.00, 60.00, 'dhaka', 1692.00, 'Processing', '2026-01-13 23:13:23', 'assigned', 3, 'Emon Halder', NULL, NULL, 'COD', 'Pending', NULL, 0, NULL),
(129, 12, 'Moon Akter', '01993433391', 'Rampura', 'Dhaka', '1840', 0.00, 0.00, 0.00, NULL, 666.90, 'Processing', '2026-01-13 23:45:30', 'assigned', 1, 'Jishan Islam', '2026-01-16', NULL, 'COD', 'Pending', NULL, 0, NULL),
(130, 12, 'Moon Akter', '01993433391', 'Rampura', 'Dhaka', '1840', 0.00, 0.00, 0.00, NULL, 666.90, 'Pending', '2026-01-13 23:45:51', 'not_assigned', NULL, NULL, NULL, '130', 'SSLCommerz', 'Success', NULL, 0, NULL),
(131, 7, 'Maliha Akter', '01998733210', 'House Building, Uttara', 'Dhaka', '1215', 930.00, 18.60, 60.00, 'dhaka', 1008.60, 'Processing', '2026-01-14 00:29:05', 'assigned', 3, 'Emon Halder', NULL, '131', 'SSLCommerz', 'Success', NULL, 0, NULL),
(132, 7, 'Maliha Akter', '01998733211', 'Badda,Dhaka', 'Dhaka', '1738', 2105.00, 42.10, 60.00, 'dhaka', 2207.10, 'Pending', '2026-01-14 00:38:14', 'not_assigned', NULL, NULL, NULL, '132', 'SSLCommerz', 'Success', NULL, 0, NULL),
(133, 9, 'Prime Das', '01883677845', 'Rampura', 'Dhaka', '1215', 1900.00, 38.00, 60.00, 'dhaka', 1998.00, 'Completed', '2026-01-14 01:47:17', 'delivered', 3, 'Emon Halder', '2026-01-15', '133', 'SSLCommerz', 'Success', NULL, 1, '2026-01-14 03:34:00'),
(134, 16, 'Jishan Islam', '01999837367', 'Tv Center', 'Dhaka', '1215', 4494.00, 89.88, 60.00, 'dhaka', 4643.88, 'cancelled', '2026-01-14 01:49:37', 'not_assigned', NULL, NULL, NULL, '134', 'SSLCommerz', 'Refunded', '2026-01-14 02:25:32', 0, NULL),
(135, 16, 'Jishan Islam', '0198374657', 'Rajendrapur', 'Dhaka', '1267', 3379.00, 67.58, 150.00, 'outside', 3596.58, 'Processing', '2026-01-14 02:42:24', 'assigned', 1, 'Jishan Islam', '2026-01-16', NULL, 'COD', 'Pending', NULL, 0, NULL),
(136, 16, 'Jishan Islam', '0198374657', 'Rajendrapur', 'Dhaka', '1267', 3369.00, 67.38, 150.00, 'outside', 3586.38, 'Processing', '2026-01-14 02:53:43', 'assigned', 1, 'Jishan Islam', '2026-01-16', NULL, 'COD', 'Pending', NULL, 0, NULL),
(137, 16, 'Jishan Islam', '0198374657', 'Rajendrapur', 'Dhaka', '1267', 1450.00, 29.00, 60.00, 'dhaka', 1539.00, 'Processing', '2026-01-14 02:59:29', 'assigned', 3, 'Emon Halder', '2026-01-15', NULL, 'COD', 'Pending', NULL, 0, NULL),
(138, 16, 'Jishan Islam', '0198374657', 'Rajendrapur', 'Dhaka', '1267', 595.00, 11.90, 60.00, 'dhaka', 666.90, 'Completed', '2026-01-14 03:19:05', 'delivered', 3, 'Emon Halder', '2026-01-15', NULL, 'COD', 'Paid', NULL, 1, '2026-01-14 03:38:05'),
(139, 7, 'Maliha Akter', '01998733211', 'Badda,Dhaka', 'Dhaka', '1738', 70.00, 1.40, 60.00, 'dhaka', 131.40, 'Pending', '2026-01-14 20:07:42', 'not_assigned', NULL, NULL, NULL, '139', 'SSLCommerz', 'Success', NULL, 0, NULL),
(140, 7, 'Maliha Akter', '01998733211', 'Badda,Dhaka', 'Dhaka', '1738', 800.00, 16.00, 60.00, 'dhaka', 876.00, 'Pending', '2026-01-14 20:10:29', 'not_assigned', NULL, NULL, NULL, '140', 'SSLCommerz', 'Success', NULL, 0, NULL),
(141, 7, 'Maliha Akter Munni', '01998733344', 'Uttara,Dhaka', 'Dhaka', '1464', 3099.00, 61.98, 60.00, 'dhaka', 3220.98, 'Shipped', '2026-01-14 20:18:30', 'out_for_delivery', 3, 'Emon Halder', NULL, NULL, 'COD', 'Pending', NULL, 1, '2026-01-16 03:22:30'),
(142, 7, 'Maliha Akter Munni', '01998733344', 'Uttara,Dhaka', 'Dhaka', '1464', 815.00, 16.30, 60.00, 'dhaka', 891.30, 'Pending', '2026-01-16 17:42:51', 'not_assigned', NULL, NULL, NULL, '142', 'SSLCommerz', 'Success', NULL, 0, NULL),
(143, 12, 'Moon Akter', '01993433391', 'Rampura', 'Dhaka', '1840', 1790.00, 35.80, 60.00, 'dhaka', 1885.80, 'Pending', '2026-01-17 10:30:23', 'not_assigned', NULL, NULL, NULL, '143', 'SSLCommerz', 'Success', NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `variant` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`, `variant`, `created_at`) VALUES
(1, 60, 28, 'Enchanteur Charming Perfumed Body Lotion ', 1290.00, 1, NULL, '2025-12-30 10:23:19'),
(2, 61, 26, 'Kodomo Baby  Shampoo Original', 999.00, 1, NULL, '2025-12-30 10:25:43'),
(3, 62, 27, 'NIVEA Body Milk Intensive Moisture', 1450.00, 1, NULL, '2025-12-30 15:02:58'),
(4, 65, 28, 'Enchanteur Charming Perfumed Body Lotion ', 1290.00, 1, NULL, '2026-01-01 09:48:21'),
(5, 66, 27, 'NIVEA Body Milk Intensive Moisture', 1450.00, 2, NULL, '2026-01-01 09:49:11'),
(6, 70, 28, 'Enchanteur Charming Perfumed Body Lotion ', 1290.00, 1, NULL, '2026-01-01 10:19:19'),
(7, 71, 27, 'NIVEA Body Milk Intensive Moisture', 1450.00, 1, NULL, '2026-01-01 10:24:23'),
(8, 72, 29, 'Enchanteur Romantic Deo spray', 500.00, 1, NULL, '2026-01-01 10:25:13'),
(9, 73, 21, 'cavendish and harvey candy', 449.00, 1, NULL, '2026-01-01 10:30:30'),
(10, 73, 24, 'Parachute Coconut Oil', 220.00, 2, NULL, '2026-01-01 10:30:30'),
(11, 74, 27, 'NIVEA Body Milk Intensive Moisture', 1450.00, 1, NULL, '2026-01-01 10:36:56'),
(12, 81, 11, 'Pomegranet', 560.00, 1, NULL, '2026-01-02 14:52:51'),
(13, 82, 28, 'Enchanteur Charming Perfumed Body Lotion ', 1290.00, 4, NULL, '2026-01-02 17:46:22'),
(14, 82, 29, 'Enchanteur Romantic Deo spray', 500.00, 2, NULL, '2026-01-02 17:46:22'),
(15, 84, 28, 'Enchanteur Charming Perfumed Body Lotion ', 1290.00, 1, NULL, '2026-01-02 17:51:58'),
(16, 84, 12, 'Nic Nac', 15.00, 1, NULL, '2026-01-02 17:51:58'),
(17, 92, 28, 'Enchanteur Charming Perfumed Body Lotion ', 1290.00, 1, NULL, '2026-01-02 22:07:56'),
(18, 94, 14, 'Cadbury Dairy Milk Silk Chocolate Bar', 495.00, 2, NULL, '2026-01-03 08:11:46'),
(19, 94, 26, 'Kodomo Baby  Shampoo Original', 999.00, 1, NULL, '2026-01-03 08:11:46'),
(20, 95, 32, 'Vim Dishwashing Bar', 15.00, 1, NULL, '2026-01-03 08:14:22'),
(21, 95, 28, 'Enchanteur Charming Perfumed Body Lotion ', 1290.00, 2, NULL, '2026-01-03 08:14:22'),
(22, 96, 25, 'Parachute Just For Baby - Baby Oil', 300.00, 1, NULL, '2026-01-03 08:14:56'),
(23, 96, 17, 'Munch chocolate', 90.00, 4, NULL, '2026-01-03 08:14:56'),
(24, 97, 31, 'Vim Dishwashing Liquid', 130.00, 2, NULL, '2026-01-03 08:47:01'),
(25, 97, 27, 'NIVEA Body Milk Intensive Moisture', 1450.00, 1, NULL, '2026-01-03 08:47:01'),
(26, 99, 31, 'Vim Dishwashing Liquid', 130.00, 2, NULL, '2026-01-03 22:44:36'),
(27, 99, 28, 'Enchanteur Charming Perfumed Body Lotion ', 1290.00, 1, NULL, '2026-01-03 22:44:36'),
(28, 100, 32, 'Vim Dishwashing Bar', 15.00, 2, NULL, '2026-01-03 23:55:53'),
(29, 100, 27, 'NIVEA Body Milk Intensive Moisture', 1450.00, 1, NULL, '2026-01-03 23:55:53'),
(30, 100, 24, 'Parachute Coconut Oil', 220.00, 2, NULL, '2026-01-03 23:55:53'),
(31, 110, 31, 'Vim Dishwashing Liquid', 130.00, 1, NULL, '2026-01-04 18:54:04'),
(32, 110, 27, 'NIVEA Body Milk Intensive Moisture', 1450.00, 1, NULL, '2026-01-04 18:54:04'),
(33, 111, 32, 'Vim Dishwashing Bar', 15.00, 2, NULL, '2026-01-04 21:28:10'),
(34, 111, 15, 'Amul Dark Chocolate', 180.00, 1, NULL, '2026-01-04 21:28:10'),
(35, 114, 28, 'Enchanteur Charming Perfumed Body Lotion ', 1290.00, 1, NULL, '2026-01-06 22:31:18'),
(36, 114, 32, 'Vim Dishwashing Bar', 15.00, 1, NULL, '2026-01-06 22:31:18'),
(37, 117, 3, 'Red Apple', 195.00, 1, NULL, '2026-01-08 00:52:35'),
(38, 120, 34, 'WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', 800.00, 1, NULL, '2026-01-10 05:15:20'),
(39, 120, 28, 'Enchanteur Charming Perfumed Body Lotion ', 1290.00, 1, NULL, '2026-01-10 05:15:20'),
(40, 121, 37, 'Dove Men Care Fresh Clean 2 in 1 Shampoo + Conditioner', 580.00, 1, NULL, '2026-01-10 05:18:47'),
(41, 121, 36, 'Rok Dishwashing Steel Scourer', 35.00, 1, NULL, '2026-01-10 05:18:47'),
(42, 126, 39, 'Cotton Duster', 35.00, 1, NULL, '2026-01-13 07:01:23'),
(43, 127, 40, 'Royal Canin Adult Persian 2', 3099.00, 1, NULL, '2026-01-13 17:09:15'),
(44, 128, 34, 'WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', 800.00, 2, NULL, '2026-01-13 17:13:23'),
(45, 132, 39, 'Cotton Duster', 35.00, 1, NULL, '2026-01-13 18:38:14'),
(46, 132, 30, 'Trix Lemon Dish Washing Liquid 1Ltr.', 270.00, 1, NULL, '2026-01-13 18:38:14'),
(47, 132, 34, 'WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', 800.00, 1, NULL, '2026-01-13 18:38:14'),
(48, 132, 29, 'Enchanteur Romantic Deo spray', 500.00, 2, NULL, '2026-01-13 18:38:14'),
(49, 133, 30, 'Trix Lemon Dish Washing Liquid 1Ltr.', 270.00, 1, NULL, '2026-01-13 19:47:17'),
(50, 133, 31, 'Vim Dishwashing Liquid', 130.00, 1, NULL, '2026-01-13 19:47:17'),
(51, 133, 27, 'NIVEA Body Milk Intensive Moisture', 1450.00, 1, NULL, '2026-01-13 19:47:17'),
(52, 133, 20, 'Ifad Eggy Pillow Bar-B-Q Chips', 10.00, 5, NULL, '2026-01-13 19:47:17'),
(53, 134, 34, 'WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', 800.00, 1, NULL, '2026-01-13 19:49:37'),
(54, 134, 33, 'Smart Heart Kitten Cat Food 450gm', 340.00, 1, NULL, '2026-01-13 19:49:37'),
(55, 134, 26, 'Kodomo Baby  Shampoo Original', 999.00, 1, NULL, '2026-01-13 19:49:37'),
(56, 134, 22, 'The Belgain Heart Chocolate', 355.00, 5, NULL, '2026-01-13 19:49:37'),
(57, 134, 37, 'Dove Men Care Fresh Clean 2 in 1 Shampoo + Conditioner', 580.00, 1, NULL, '2026-01-13 19:49:37'),
(58, 135, 40, 'Royal Canin Adult Persian 2', 3099.00, 1, NULL, '2026-01-13 20:42:24'),
(59, 135, 23, 'Livon Hair Serum', 280.00, 1, NULL, '2026-01-13 20:42:24'),
(60, 136, 30, 'Trix Lemon Dish Washing Liquid 1Ltr.', 270.00, 1, NULL, '2026-01-13 20:53:43'),
(61, 136, 40, 'Royal Canin Adult Persian 2', 3099.00, 1, NULL, '2026-01-13 20:53:43'),
(62, 137, 27, 'NIVEA Body Milk Intensive Moisture', 1450.00, 1, NULL, '2026-01-13 20:59:29'),
(63, 138, 38, 'Layer\'r Shot Red Stallion Body Spray', 595.00, 1, NULL, '2026-01-13 21:19:05'),
(64, 139, 39, 'Cotton Duster', 35.00, 1, NULL, '2026-01-14 14:07:42'),
(65, 139, 36, 'Rok Dishwashing Steel Scourer', 35.00, 1, NULL, '2026-01-14 14:07:42'),
(66, 140, 34, 'WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', 800.00, 1, NULL, '2026-01-14 14:10:29'),
(67, 141, 40, 'Royal Canin Adult Persian 2', 3099.00, 1, NULL, '2026-01-14 14:18:30'),
(68, 142, 32, 'Vim Dishwashing Bar', 15.00, 1, NULL, '2026-01-16 11:42:51'),
(69, 142, 34, 'WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', 800.00, 1, NULL, '2026-01-16 11:42:51'),
(70, 143, 33, 'Smart Heart Kitten Cat Food 450gm', 340.00, 1, NULL, '2026-01-17 04:30:23'),
(71, 143, 27, 'NIVEA Body Milk Intensive Moisture', 1450.00, 1, NULL, '2026-01-17 04:30:23');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `subcategory` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `original_price` decimal(10,2) DEFAULT NULL,
  `discount_percent` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `subcategory`, `category`, `quantity`, `created_at`, `original_price`, `discount_percent`) VALUES
(1, 'Fresh Mango', 'Sweet and juicy mangoes, perfect for summer', 180.00, '1764744987_mango.png', 'fruits', 'foods', 30, '2025-12-14 11:44:13', 180.00, 0),
(2, 'Fresh Orange', 'Sweet and juicy mangoes, perfect for summer', 380.00, '1764744963_orange.png', 'fruits', 'foods', 14, '2025-12-14 11:44:13', 380.00, 0),
(3, 'Red Apple', 'Crisp and delicious red apples', 195.00, '1764744921_apple1.png', 'fruits', 'foods', 22, '2025-12-14 11:44:13', 195.00, 0),
(4, 'Banana', 'Soft & delicious Banana', 10.00, '1764744901_banana.png', 'fruits', 'foods', 192, '2025-12-14 11:44:13', 100.00, 0),
(5, 'Guava', 'Delicious Guava', 120.00, '1764744869_guava.png', 'fruits', 'foods', 10, '2025-12-14 11:44:13', 120.00, 0),
(6, 'Strawberry', 'Fresh and sweet strawberries', 100.00, '1764744849_strawberry.png', 'fruits', 'foods', 3, '2025-12-14 11:44:13', 100.00, 0),
(7, 'Pineapple', 'Tropical sweet pineapple', 50.00, '1764744810_pineapple.png', 'fruits', 'foods', 16, '2025-12-14 11:44:13', 50.00, 0),
(8, 'Green Olive', 'Fresh green olives', 175.00, '1764744538_olives.png', 'fruits', 'foods', 13, '2025-12-14 11:44:13', 175.00, 0),
(10, 'Avocados', 'Fresh Avacado contain a wide range of nutrients. Health benefits of avocado consumption may include improving digestion, lowering the risk of depression, and preventing bone loss.', 1200.00, '1764744265_avacado.png', 'fruits', 'foods', 12, '2025-12-14 11:44:13', 1200.00, 0),
(11, 'Pomegranet', 'Fresh  Pomegranet Pomegranate was used in traditional medicine to treat intestinal parasitic infections, diarrhea, sore throat, and other conditions. It has been used orally (by mouth), topically (applied to the skin), and as a gargle or mouthwash.\r\nPreparations from pomegranate are currently promoted for many conditions including high blood pressure, heart disease, and diabetes.', 560.00, '1764745550_pomegranete.png', 'fruits', 'foods', 5, '2025-12-14 11:44:13', 560.00, 0),
(12, 'Nic Nac', 'There are many flavours of Nic Nac, including milk, white, and dark chocolate.', 35.00, '1765055603_nicnac.png', 'chocolate', 'foods', 1, '2025-12-14 11:44:13', 15.00, 0),
(13, 'Cadbury Bournville Dark Chocolate Bar', 'A chocolate that allows you to relax, unwind and end your day on a sweet note with Rich cocoa', 550.00, '1765105089_cadburyB.png', 'chocolate', 'foods', 2, '2025-12-14 11:44:13', 550.00, 0),
(14, 'Cadbury Dairy Milk Silk Chocolate Bar', 'Cadbury Dairy Milk Silk is all about regaling in the richness and creaminess of chocolate. Indulge in a rich, smooth, and creamy celebration.', 495.00, '1765103619_dairymilk.png', 'chocolate', 'foods', 24, '2025-12-14 11:44:13', 495.00, 0),
(15, 'Amul Dark Chocolate', 'Amul Dark Chocolate is made with the finest ingredients and delicious cocoa.', 180.00, '1765105949_amul.png', 'chocolate', 'foods', 4, '2025-12-14 11:44:13', 180.00, 0),
(16, 'Cadbury 5 Star Chocolate', 'Indulgence, Guaranteed Satisfaction, Deliciously Smooth.', 60.00, '1765106409_5star.png', 'chocolate', 'foods', 30, '2025-12-14 11:44:13', 60.00, 0),
(17, 'Munch chocolate', 'Perfect for sweet cravings anytime.', 90.00, '1765108167_munch.png', 'chocolate', 'foods', 5, '2025-12-14 11:44:13', 90.00, 0),
(18, 'Ifad Eggy Stix Bar-B-Q Chips', 'Ifad Eggy Stix Bar-B-Q Chips 16 gm is a crispy, crunchy snack with a savory barbecue flavor. ', 10.00, '1765174119_stix.png', 'chips', 'foods', 15, '2025-12-14 11:44:13', 10.00, 0),
(19, 'Lay\'s Thai Style Spicy Chicken Potato Chips', 'Lay\'s Thai Style Spicy Chicken Potato Chips 13g bring a burst of bold, spicy flavor with a savory chicken taste, inspired by Thai cuisine.', 25.00, '1765174425_laysC.jpg', 'chips', 'foods', 16, '2025-12-14 11:44:13', 25.00, 0),
(20, 'Ifad Eggy Pillow Bar-B-Q Chips', 'Ifad Eggy Pillow Bar-B-Q Chips 16 gm is a flavorful snack featuring a unique pillow-shaped texture with a delicious barbecue flavor.', 10.00, '1765178800_pillow.png', 'chips', 'foods', 21, '2025-12-14 11:44:13', 10.00, 0),
(21, 'cavendish and harvey candy', 'Cavendish & Harvey candy is a premium German confectionery brand famous for its hard fruit drops, often sold in elegant, resealable tins or jars, made with real fruit juice for intense, authentic fruit flavors like citrus, mixed fruit, and tropical blends, focusing on quality, tradition, and delightful, sophisticated indulgence for over 100 countries. ', 449.00, '1766728194_cavendish.png', 'candy', 'foods', 14, '2025-12-14 11:44:13', 2.00, 0),
(22, 'The Belgain Heart Chocolate', 'Belgian Hearts Chocolate is crafted and shaped into a heart-shaped. With that, it is kept in mind that while you are having it you get lost and dissolved into the richness of the chocolate.', 355.00, '1766727926_balgainH.png', 'chocolate', 'foods', 14, '2025-12-14 11:44:13', 2.00, 0),
(23, 'Livon Hair Serum', 'Livon Product Benefits: Soft & Silky Hair, Boosts Shine, Anti Frizz Item', 280.00, '1765591503_livon.png', 'womens-care', 'personal-care', 19, '2025-12-14 11:44:13', 280.00, 0),
(24, 'Parachute Coconut Oil', 'Parachute Coconut Oil is Made from the finest quality coconut to ensure the best Coconut Oil. It has 5 Stage Purification process to ensure pure coconut oil every time, Long lasting freshness, and Consistent composition and viscosity in every drop of oil.', 220.00, '1765591673_parachute.png', 'womens-care', 'personal-care', 5, '2025-12-14 11:44:13', 220.00, 0),
(25, 'Parachute Just For Baby - Baby Oil', 'Parachute Just for Baby - Baby Oil Just for Baby Oil with goodness of Natural Olive & Almond oil gets easily absorbed in your baby’s skin and it is great for everyday massage.', 300.00, '1765691510_justforbabyOIL.png', 'baby-care', 'personal-care', 19, '2025-12-14 11:51:50', 300.00, 0),
(26, 'Kodomo Baby  Shampoo Original', 'Kodomo Baby Shampoo Original (0+), 400 ml, is a mild and gentle shampoo specially designed for newborns and babies. Its tear-free formula ensures that it’s safe for your little one’s sensitive eyes, providing a soothing and comfortable bathing experience.', 999.00, '1765693517_kodombaby.png', 'baby-care', 'personal-care', 10, '2025-12-14 12:25:17', 999.00, 0),
(27, 'NIVEA Body Milk Intensive Moisture', 'Deeply moisturized skin with the new NIVEA Intensive Lotion Body Milk with deep moisture serum. This rich and creamy formula with 2 times more almond oil deeply moisturizes and softens your dry skin', 1450.00, '1766180525_nevia.png', 'womens-care', 'personal-care', 17, '2025-12-20 03:27:06', 1450.00, 0),
(28, 'Enchanteur Charming Perfumed Body Lotion ', 'The body lotion is formulated to provide deep hydration to your skin, helping to replenish moisture and keep it soft.', 1290.00, '1766181502_enchanter.png', 'womens-care', 'personal-care', 11, '2025-12-20 03:58:22', 1290.00, 0),
(29, 'Enchanteur Romantic Deo spray', 'The signature Enchanteur Romantic fragrance, of roses, white jasmines, violets and vanilla, is infused in this Perfumed Deo Spray. Its special formula is gentle on the skin and offers lasting freshness.', 500.00, '1766184132_enchanterS.png', 'womens-care', 'personal-care', 16, '2025-12-20 04:42:12', 500.00, 0),
(30, 'Trix Lemon Dish Washing Liquid 1Ltr.', 'Trix Dishwashing Liquid Lemon 1Ltr. Cut through dirt and grime with refreshing lemon scent.', 270.00, '1767426750_trix.png', 'kitchen', 'household', 16, '2026-01-03 13:52:30', NULL, 0),
(31, 'Vim Dishwashing Liquid', 'Vim liquid, with the power of 100 lemons, gives you complete cleaning without leaving any residue, unlike Dishwash Bars. It is also great value for money with one spoon of Vim liquid being enough to clean a full sink of dirty utensils.', 130.00, '1767426889_vim.png', 'kitchen', 'household', 28, '2026-01-03 13:54:49', NULL, 0),
(32, 'Vim Dishwashing Bar', 'With the power of 100 lemons, Vim Bar helps to clean tough grease the fastest. It gives you a pleasant cleaning experience with its refreshing lemon fragrance. It removes stains easily.', 15.00, '1767427035_vimB.png', 'kitchen', 'household', 13, '2026-01-03 13:57:15', NULL, 0),
(33, 'Smart Heart Kitten Cat Food 450gm', 'Smart Heart kitten food is delicious and made from real meat and fish. It is specially formulated for kitten development with nutrients to provide enhanced brain function, support the nervous system and develop the kitten’s memory. Your beloved kitten will have proper muscle and body structure development, a shiny coat and a healthy life.', 340.00, '1767888699_kitten1.png', 'catcare', 'pet-care', 5, '2026-01-08 17:27:02', NULL, 0),
(34, 'WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', 'WHISKAS® 1+ Years Dry Cat Food is 100% complete and balanced to provide daily nutrition for adult cats. Specially designed with all the vitamins and minerals needed for a healthy and happy life, helping to provide the best possible care for your cat.', 800.00, '1767871959_kitten2.png', 'catcare', 'pet-care', 0, '2026-01-08 17:32:39', NULL, 0),
(35, 'Mop Cotton Refill (17\") China', 'Give your floors a new look & shine with this white cotton finish mop head! Made with high quality absorbent thin cotton. This mop head provides extra protection to your floors and makes the floor look neat and free of bacteria and dirt. Use it to soak up spills, or for everyday cleaning tasks. Large surface area increase absorption which makes the floors neat and clean with no watermarks.', 185.00, '1767889467_Mop.png', 'cleaning', 'household', 7, '2026-01-08 22:24:27', NULL, 0),
(36, 'Rok Dishwashing Steel Scourer', 'Easy to remove dirt and food particles, Ideal for cleaning the kitchen, stainless steel crocarize, pans, grills, pots, and dishes with minimum effort.', 35.00, '1767889595_rok.png', 'cleaning', 'household', 4, '2026-01-08 22:26:35', NULL, 0),
(37, 'Dove Men Care Fresh Clean 2 in 1 Shampoo + Conditioner', 'Dove Men+Care Fresh Clean 2in1 Shampoo+Conditioner is a hair care product specifically designed for men. It comes in a 250ml bottle and is manufactured in France. This shampoo and conditioner combo is formulated with a blend of ingredients that are designed to cleanse the hair and scalp while also conditioning and nourishing the hair. It is designed to leave your hair feeling fresh and clean, while also making it look healthy and shiny.', 580.00, '1767945953_menShampoo.png', 'mens-care', 'personal-care', 1, '2026-01-09 14:05:53', NULL, 0),
(38, 'Layer\'r Shot Red Stallion Body Spray', 'For the rugged outdoorsman who can’t do without adventure, comes in a wild racy blend of bergamot, pepper, and woody fragrances.\r\n\r\n\r\nFRAGRANCE NOTES:\r\n\r\n\r\nTop Notes: FOUGERE FRESH & BERGAMOT\r\n\r\nHeart Notes: PEPPER & LEMON\r\n\r\nBase Notes: CEDARWOOD\r\n\r\nKEY FEATURES:\r\n\r\n\r\nLong-lasting fragrance\r\n\r\nIdeal for daily use\r\n\r\nCan be used on both, the body and clothes\r\n\r\n', 595.00, '1767967486_layer spray.png', 'mens-care', 'personal-care', 5, '2026-01-09 20:04:46', NULL, 0),
(39, 'Cotton Duster', 'Material: COTTON\r\n\r\nPattern: PLAIN\r\n\r\nShape: SQUARE\r\n\r\nUsage/Application: KITCHEN & HOME Appliances\r\n\r\nSize: 24\"X13\"', 35.00, '1768023619_dus.png', 'cleaning', 'household', 0, '2026-01-10 11:40:19', NULL, 0),
(40, 'Royal Canin Adult Persian 2', 'Balanced and complete feed for cats - Specially for adult Persian cats - Over 12 months old.Reduces hairball Balances urinal PH Supports digestive performance Promotes healthy skin and coat Contains low indigestible proteins (L.I.P), prebiotics and omega 3 & 6 fatty acids.Quantity: 2 kg.', 3099.00, '1768061013_royelC.png', 'catcare', 'pet-care', 0, '2026-01-10 22:03:33', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `refund_transaction_id` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `refunds`
--

INSERT INTO `refunds` (`id`, `order_id`, `amount`, `refund_transaction_id`, `created_at`) VALUES
(1, 113, 408.24, 'REFUND_1767728249', '2026-01-07 01:37:29'),
(2, 116, 48.38, 'REFUND_1767765602', '2026-01-07 12:00:02'),
(3, 123, 224.40, 'REFUND_1768026127', '2026-01-10 12:22:07'),
(4, 134, 4643.88, 'REFUND_1768335932', '2026-01-14 02:25:32');

-- --------------------------------------------------------

--
-- Table structure for table `staff_notifications`
--

CREATE TABLE `staff_notifications` (
  `id` int(11) NOT NULL,
  `to_role` enum('admin','delivery') NOT NULL,
  `to_id` int(11) NOT NULL,
  `from_role` enum('admin','delivery') NOT NULL,
  `from_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `type` enum('assign','reply','cod_request','delivery_done','info') DEFAULT 'info',
  `message` text NOT NULL,
  `reply_text` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `replied_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staff_notifications`
--

INSERT INTO `staff_notifications` (`id`, `to_role`, `to_id`, `from_role`, `from_id`, `order_id`, `type`, `message`, `reply_text`, `is_read`, `created_at`, `replied_at`) VALUES
(1, 'delivery', 1, 'admin', 1, 98, 'assign', 'Order #98 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-04 04:16:31', NULL),
(2, 'admin', 1, 'admin', 1, 98, 'info', 'Order #98 delivery set to: ASSIGNED', NULL, 0, '2026-01-04 04:16:31', NULL),
(3, 'delivery', 1, 'admin', 1, 96, 'assign', 'Order #96 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-04 04:18:00', NULL),
(4, 'admin', 1, 'admin', 1, 96, 'info', 'Order #96 delivery set to: ASSIGNED', NULL, 0, '2026-01-04 04:18:00', NULL),
(5, 'delivery', 1, 'admin', 1, 99, 'assign', 'Order #99 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-04 04:45:21', NULL),
(6, 'admin', 1, 'admin', 1, 99, 'info', 'Order #99 delivery set to: ASSIGNED', NULL, 0, '2026-01-04 04:45:21', NULL),
(7, 'delivery', 1, 'admin', 1, 98, 'assign', 'Order #98 updated: DELIVERED. Please check your dashboard.', NULL, 0, '2026-01-04 04:56:32', NULL),
(8, 'admin', 1, 'admin', 1, 98, 'info', 'Order #98 delivery set to: DELIVERED', NULL, 0, '2026-01-04 04:56:32', NULL),
(9, 'delivery', 1, 'admin', 1, 96, 'assign', 'Order #96 updated: OUT FOR DELIVERY. Please check your dashboard.', NULL, 0, '2026-01-04 04:59:33', NULL),
(10, 'admin', 1, 'admin', 1, 96, 'info', 'Order #96 delivery set to: OUT FOR DELIVERY', NULL, 0, '2026-01-04 04:59:33', NULL),
(11, 'delivery', 1, 'admin', 1, 100, 'assign', 'Order #100 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-04 06:03:48', NULL),
(12, 'admin', 1, 'admin', 1, 100, 'info', 'Order #100 delivery set to: ASSIGNED', NULL, 0, '2026-01-04 06:03:48', NULL),
(13, 'delivery', 1, 'admin', 1, 100, 'assign', 'Order #100 updated: OUT FOR DELIVERY. Please check your dashboard.', NULL, 0, '2026-01-04 06:06:13', NULL),
(14, 'admin', 1, 'admin', 1, 100, 'info', 'Order #100 delivery set to: OUT FOR DELIVERY', NULL, 0, '2026-01-04 06:06:13', NULL),
(15, 'delivery', 1, 'admin', 1, 105, 'assign', 'Order #105 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-04 13:40:15', NULL),
(16, 'admin', 1, 'admin', 1, 105, 'info', 'Order #105 delivery set to: ASSIGNED', NULL, 0, '2026-01-04 13:40:15', NULL),
(17, 'delivery', 1, 'admin', 1, 105, 'assign', 'Order #105 updated: OUT FOR DELIVERY. Please check your dashboard.', NULL, 0, '2026-01-04 13:46:59', NULL),
(18, 'admin', 1, 'admin', 1, 105, 'info', 'Order #105 delivery set to: OUT FOR DELIVERY', NULL, 0, '2026-01-04 13:46:59', NULL),
(19, 'delivery', 1, 'admin', 1, 105, 'assign', 'Order #105 updated: DELIVERED. Please check your dashboard.', NULL, 0, '2026-01-04 13:48:25', NULL),
(20, 'admin', 1, 'admin', 1, 105, 'info', 'Order #105 delivery set to: DELIVERED', NULL, 0, '2026-01-04 13:48:25', NULL),
(21, 'delivery', 1, 'admin', 1, 104, 'assign', 'Order #104 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-04 13:49:32', NULL),
(22, 'admin', 1, 'admin', 1, 104, 'info', 'Order #104 delivery set to: ASSIGNED', NULL, 0, '2026-01-04 13:49:32', NULL),
(23, 'delivery', 2, 'admin', 1, 104, 'assign', 'Order #104 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-04 13:52:07', NULL),
(24, 'admin', 1, 'admin', 1, 104, 'info', 'Order #104 delivery set to: ASSIGNED', NULL, 0, '2026-01-04 13:52:07', NULL),
(25, 'delivery', 2, 'admin', 1, 104, 'assign', 'Order #104 updated: OUT FOR DELIVERY. Please check your dashboard.', NULL, 0, '2026-01-04 13:54:16', NULL),
(26, 'admin', 1, 'admin', 1, 104, 'info', 'Order #104 delivery set to: OUT FOR DELIVERY', NULL, 0, '2026-01-04 13:54:16', NULL),
(27, 'delivery', 2, 'admin', 1, 109, 'assign', 'Order #109 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-05 00:04:09', NULL),
(28, 'admin', 1, 'admin', 1, 109, 'info', 'Order #109 delivery set to: ASSIGNED', NULL, 0, '2026-01-05 00:04:09', NULL),
(29, 'delivery', 2, 'admin', 1, 109, 'assign', 'Order #109 updated: DELIVERED. Please check your dashboard.', NULL, 0, '2026-01-05 00:39:38', NULL),
(30, 'admin', 1, 'admin', 1, 109, 'info', 'Order #109 delivery set to: DELIVERED', NULL, 0, '2026-01-05 00:39:38', NULL),
(31, 'delivery', 1, 'admin', 1, 110, 'assign', 'Order #110 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-05 00:54:30', NULL),
(32, 'admin', 1, 'admin', 1, 110, 'info', 'Order #110 delivery set to: ASSIGNED', NULL, 0, '2026-01-05 00:54:30', NULL),
(33, 'delivery', 2, 'admin', 1, 107, 'assign', 'Order #107 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-05 01:39:35', NULL),
(34, 'admin', 1, 'admin', 1, 107, 'info', 'Order #107 delivery set to: ASSIGNED', NULL, 0, '2026-01-05 01:39:35', NULL),
(35, 'delivery', 2, 'admin', 1, 107, 'assign', 'Order #107 updated: OUT FOR DELIVERY. Please check your dashboard.', NULL, 0, '2026-01-05 01:42:18', NULL),
(36, 'admin', 1, 'admin', 1, 107, 'info', 'Order #107 delivery set to: OUT FOR DELIVERY', NULL, 0, '2026-01-05 01:42:18', NULL),
(37, 'delivery', 2, 'admin', 1, 68, 'assign', 'Order #68 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-05 03:13:52', NULL),
(38, 'admin', 1, 'admin', 1, 68, 'info', 'Order #68 delivery set to: ASSIGNED', NULL, 0, '2026-01-05 03:13:52', NULL),
(39, 'delivery', 2, 'admin', 1, 68, 'assign', 'Order #68 updated: OUT FOR DELIVERY. Please check your dashboard.', NULL, 0, '2026-01-05 03:14:17', NULL),
(40, 'admin', 1, 'admin', 1, 68, 'info', 'Order #68 delivery set to: OUT FOR DELIVERY', NULL, 0, '2026-01-05 03:14:17', NULL),
(41, 'delivery', 2, 'admin', 1, 111, 'assign', 'Order #111 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-05 03:28:40', NULL),
(42, 'admin', 1, 'admin', 1, 111, 'info', 'Order #111 delivery set to: ASSIGNED', NULL, 0, '2026-01-05 03:28:40', NULL),
(43, 'delivery', 1, 'admin', 1, 112, 'assign', 'Order #112 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-07 01:02:38', NULL),
(44, 'admin', 1, 'admin', 1, 112, 'info', 'Order #112 delivery set to: ASSIGNED', NULL, 0, '2026-01-07 01:02:38', NULL),
(45, 'delivery', 1, 'admin', 1, 103, 'assign', 'Order #103 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-07 01:49:46', NULL),
(46, 'admin', 1, 'admin', 1, 103, 'info', 'Order #103 delivery set to: ASSIGNED', NULL, 0, '2026-01-07 01:49:46', NULL),
(47, 'delivery', 1, 'admin', 1, 114, 'assign', 'Order #114 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-07 04:54:46', NULL),
(48, 'admin', 1, 'admin', 1, 114, 'info', 'Order #114 delivery set to: ASSIGNED', NULL, 0, '2026-01-07 04:54:46', NULL),
(49, 'delivery', 1, 'admin', 1, 114, 'assign', 'Order #114 updated: OUT FOR DELIVERY. Please check your dashboard.', NULL, 0, '2026-01-07 04:55:11', NULL),
(50, 'admin', 1, 'admin', 1, 114, 'info', 'Order #114 delivery set to: OUT FOR DELIVERY', NULL, 0, '2026-01-07 04:55:11', NULL),
(51, 'delivery', 1, 'admin', 1, 118, 'assign', 'Order #118 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-10 09:00:29', NULL),
(52, 'admin', 1, 'admin', 1, 118, 'info', 'Order #118 delivery set to: ASSIGNED', NULL, 0, '2026-01-10 09:00:29', NULL),
(53, 'delivery', 2, 'admin', 1, 117, 'assign', 'Order #117 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-10 09:04:43', NULL),
(54, 'admin', 1, 'admin', 1, 117, 'info', 'Order #117 delivery set to: ASSIGNED', NULL, 0, '2026-01-10 09:04:43', NULL),
(55, 'delivery', 1, 'admin', 1, 115, 'assign', 'Order #115 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-10 09:05:15', NULL),
(56, 'admin', 1, 'admin', 1, 115, 'info', 'Order #115 delivery set to: ASSIGNED', NULL, 0, '2026-01-10 09:05:15', NULL),
(57, 'delivery', 1, 'admin', 1, 108, 'assign', 'Order #108 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-10 09:05:31', NULL),
(58, 'admin', 1, 'admin', 1, 108, 'info', 'Order #108 delivery set to: ASSIGNED', NULL, 0, '2026-01-10 09:05:31', NULL),
(59, 'delivery', 1, 'admin', 1, 106, 'assign', 'Order #106 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-10 09:05:55', NULL),
(60, 'admin', 1, 'admin', 1, 106, 'info', 'Order #106 delivery set to: ASSIGNED', NULL, 0, '2026-01-10 09:05:55', NULL),
(61, 'delivery', 1, 'admin', 1, 102, 'assign', 'Order #102 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-10 09:06:07', NULL),
(62, 'admin', 1, 'admin', 1, 102, 'info', 'Order #102 delivery set to: ASSIGNED', NULL, 0, '2026-01-10 09:06:07', NULL),
(63, 'delivery', 1, 'admin', 1, 101, 'assign', 'Order #101 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-10 09:06:47', NULL),
(64, 'admin', 1, 'admin', 1, 101, 'info', 'Order #101 delivery set to: ASSIGNED', NULL, 0, '2026-01-10 09:06:47', NULL),
(65, 'delivery', 1, 'admin', 1, 95, 'assign', 'Order #95 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-10 09:07:13', NULL),
(66, 'admin', 1, 'admin', 1, 95, 'info', 'Order #95 delivery set to: ASSIGNED', NULL, 0, '2026-01-10 09:07:13', NULL),
(67, 'delivery', 1, 'admin', 1, 94, 'assign', 'Order #94 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-10 09:07:24', NULL),
(68, 'admin', 1, 'admin', 1, 94, 'info', 'Order #94 delivery set to: ASSIGNED', NULL, 0, '2026-01-10 09:07:24', NULL),
(69, 'delivery', 2, 'admin', 1, 117, 'assign', 'Order #117 updated: OUT FOR DELIVERY. Please check your dashboard.', NULL, 0, '2026-01-10 10:01:49', NULL),
(70, 'admin', 1, 'admin', 1, 117, 'info', 'Order #117 delivery set to: OUT FOR DELIVERY', NULL, 0, '2026-01-10 10:01:49', NULL),
(71, 'delivery', 1, 'admin', 1, 121, 'assign', 'Order #121 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-10 11:20:53', NULL),
(72, 'admin', 1, 'admin', 1, 121, 'info', 'Order #121 delivery set to: ASSIGNED', NULL, 0, '2026-01-10 11:20:53', NULL),
(73, 'delivery', 2, 'admin', 1, 122, 'assign', 'Order #122 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-10 11:31:50', NULL),
(74, 'admin', 1, 'admin', 1, 122, 'info', 'Order #122 delivery set to: ASSIGNED', NULL, 0, '2026-01-10 11:31:50', NULL),
(75, 'delivery', 2, 'admin', 1, 122, 'assign', 'Order #122 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-10 11:32:09', NULL),
(76, 'admin', 1, 'admin', 1, 122, 'info', 'Order #122 delivery set to: ASSIGNED', NULL, 0, '2026-01-10 11:32:09', NULL),
(77, 'delivery', 1, 'admin', 1, 120, 'assign', 'Order #120 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-11 01:44:29', NULL),
(78, 'admin', 1, 'admin', 1, 120, 'info', 'Order #120 delivery set to: ASSIGNED', NULL, 0, '2026-01-11 01:44:29', NULL),
(79, 'delivery', 2, 'admin', 1, 124, 'assign', 'Order #124 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-11 03:17:23', NULL),
(80, 'admin', 1, 'admin', 1, 124, 'info', 'Order #124 delivery set to: ASSIGNED', NULL, 0, '2026-01-11 03:17:23', NULL),
(81, 'delivery', 1, 'admin', 1, 125, 'assign', 'Order #125 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-12 22:35:42', NULL),
(82, 'admin', 1, 'admin', 1, 125, 'info', 'Order #125 delivery set to: ASSIGNED', NULL, 0, '2026-01-12 22:35:42', NULL),
(83, 'delivery', 1, 'admin', 1, 88, 'assign', 'Order #88 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-12 22:36:15', NULL),
(84, 'admin', 1, 'admin', 1, 88, 'info', 'Order #88 delivery set to: ASSIGNED', NULL, 0, '2026-01-12 22:36:15', NULL),
(85, 'delivery', 2, 'admin', 1, 119, 'assign', 'Order #119 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-12 22:36:46', NULL),
(86, 'admin', 1, 'admin', 1, 119, 'info', 'Order #119 delivery set to: ASSIGNED', NULL, 0, '2026-01-12 22:36:46', NULL),
(87, 'delivery', 3, 'admin', 1, 93, 'assign', 'Order #93 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-12 22:37:12', NULL),
(88, 'admin', 1, 'admin', 1, 93, 'info', 'Order #93 delivery set to: ASSIGNED', NULL, 0, '2026-01-12 22:37:12', NULL),
(89, 'delivery', 3, 'admin', 1, 83, 'assign', 'Order #83 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-13 09:25:01', NULL),
(90, 'admin', 1, 'admin', 1, 83, 'info', 'Order #83 delivery set to: ASSIGNED', NULL, 0, '2026-01-13 09:25:01', NULL),
(91, 'delivery', 3, 'admin', 1, 63, 'assign', 'Order #63 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-13 09:25:25', NULL),
(92, 'admin', 1, 'admin', 1, 63, 'info', 'Order #63 delivery set to: ASSIGNED', NULL, 0, '2026-01-13 09:25:25', NULL),
(93, 'delivery', 3, 'admin', 1, 133, 'assign', 'Order #133 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-14 02:31:11', NULL),
(94, 'admin', 1, 'admin', 1, 133, 'info', 'Order #133 delivery set to: ASSIGNED', NULL, 0, '2026-01-14 02:31:11', NULL),
(95, 'delivery', 3, 'admin', 1, 138, 'assign', 'Order #138 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-14 03:36:34', NULL),
(96, 'admin', 1, 'admin', 1, 138, 'info', 'Order #138 delivery set to: ASSIGNED', NULL, 0, '2026-01-14 03:36:34', NULL),
(97, 'delivery', 3, 'admin', 1, 137, 'assign', 'Order #137 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-14 03:40:56', NULL),
(98, 'admin', 1, 'admin', 1, 137, 'info', 'Order #137 delivery set to: ASSIGNED', NULL, 0, '2026-01-14 03:40:56', NULL),
(99, 'delivery', 1, 'admin', 1, 136, 'assign', 'Order #136 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-14 03:45:10', NULL),
(100, 'admin', 1, 'admin', 1, 136, 'info', 'Order #136 delivery set to: ASSIGNED', NULL, 0, '2026-01-14 03:45:10', NULL),
(101, 'delivery', 1, 'admin', 1, 135, 'assign', 'Order #135 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-14 09:58:57', NULL),
(102, 'admin', 1, 'admin', 1, 135, 'info', 'Order #135 delivery set to: ASSIGNED', NULL, 0, '2026-01-14 09:58:57', NULL),
(103, 'delivery', 1, 'admin', 1, 129, 'assign', 'Order #129 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-14 10:21:14', NULL),
(104, 'admin', 1, 'admin', 1, 129, 'info', 'Order #129 delivery set to: ASSIGNED', NULL, 0, '2026-01-14 10:21:14', NULL),
(105, 'delivery', 3, 'admin', 1, 127, 'assign', 'Order #127 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-14 10:29:04', NULL),
(106, 'admin', 1, 'admin', 1, 127, 'info', 'Order #127 delivery set to: ASSIGNED', NULL, 0, '2026-01-14 10:29:04', NULL),
(107, 'delivery', 3, 'admin', 1, 128, 'assign', 'Order #128 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-14 10:30:11', NULL),
(108, 'admin', 1, 'admin', 1, 128, 'info', 'Order #128 delivery set to: ASSIGNED', NULL, 0, '2026-01-14 10:30:11', NULL),
(109, 'delivery', 3, 'admin', 1, 131, 'assign', 'Order #131 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-16 03:17:25', NULL),
(110, 'admin', 1, 'admin', 1, 131, 'info', 'Order #131 delivery set to: ASSIGNED', NULL, 0, '2026-01-16 03:17:25', NULL),
(111, 'delivery', 3, 'admin', 1, 141, 'assign', 'Order #141 updated: ASSIGNED. Please check your dashboard.', NULL, 0, '2026-01-16 03:22:17', NULL),
(112, 'admin', 1, 'admin', 1, 141, 'info', 'Order #141 delivery set to: ASSIGNED', NULL, 0, '2026-01-16 03:22:17', NULL),
(113, 'delivery', 3, 'admin', 1, 141, 'assign', 'Order #141 updated: OUT FOR DELIVERY. Please check your dashboard.', NULL, 0, '2026-01-16 15:29:59', NULL),
(114, 'admin', 1, 'admin', 1, 141, 'info', 'Order #141 delivery set to: OUT FOR DELIVERY', NULL, 0, '2026-01-16 15:29:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `stock_logs`
--

CREATE TABLE `stock_logs` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity_change` int(11) NOT NULL,
  `action_type` varchar(50) DEFAULT NULL,
  `session_id` varchar(128) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_logs`
--

INSERT INTO `stock_logs` (`id`, `product_id`, `quantity_change`, `action_type`, `session_id`, `notes`, `created_at`) VALUES
(1, 25, 1, 'cart_clear', NULL, 'Cart cleared: Parachute Just For Baby - Baby Oil', '2025-12-16 09:31:34'),
(2, 24, 2, 'cart_clear', NULL, 'Cart cleared: Parachute Coconut Oil', '2025-12-16 09:31:34'),
(3, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2025-12-16 09:32:14'),
(4, 21, -1, 'cart_add', NULL, 'User added to cart: PRAN Hajom Candy Lozenge', '2025-12-16 09:32:19'),
(5, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-16 09:32:24'),
(6, 10, -1, 'cart_add', NULL, 'User added to cart: Avocados', '2025-12-16 09:32:31'),
(7, 20, 1, 'cart_remove', NULL, 'Item removed from cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-16 09:38:19'),
(8, 21, -8, 'cart_update', NULL, 'Increased quantity by 8: PRAN Hajom Candy Lozenge', '2025-12-16 09:38:40'),
(9, 21, -1, 'cart_update', NULL, 'Increased quantity by 1: PRAN Hajom Candy Lozenge', '2025-12-16 09:38:51'),
(10, 24, -2, 'cart_update', NULL, 'Increased quantity by 2: Parachute Coconut Oil', '2025-12-16 09:39:01'),
(11, 12, -1, 'cart_add', NULL, 'User added to cart: Nic Nac', '2025-12-16 10:02:51'),
(12, 23, -1, 'cart_add', NULL, 'User added to cart: Livon Hair Serum', '2025-12-16 10:15:49'),
(13, 17, -1, 'cart_add', NULL, 'User added to cart: Munch chocolate', '2025-12-16 10:16:04'),
(14, 23, -1, 'cart_update', NULL, 'Increased quantity by 1: Livon Hair Serum', '2025-12-16 10:16:08'),
(15, 23, 1, 'cart_update', NULL, 'Decreased quantity by 1: Livon Hair Serum', '2025-12-16 10:16:16'),
(16, 23, 1, 'cart_remove', NULL, 'Item removed from cart: Livon Hair Serum', '2025-12-16 10:16:20'),
(17, 17, 1, 'cart_remove', NULL, 'Item removed from cart: Munch chocolate', '2025-12-16 10:16:29'),
(18, 16, -1, 'cart_add', NULL, 'User added to cart: Cadbury 5 Star Chocolate', '2025-12-16 10:16:50'),
(19, 12, -1, 'cart_add', NULL, 'User added to cart: Nic Nac', '2025-12-16 10:16:57'),
(20, 8, -1, 'cart_add', NULL, 'User added to cart: Green Olive', '2025-12-16 10:17:02'),
(21, 16, -1, 'cart_update', NULL, 'Increased quantity by 1: Cadbury 5 Star Chocolate', '2025-12-16 10:17:06'),
(22, 16, 2, 'cart_clear', NULL, 'Cart cleared: Cadbury 5 Star Chocolate', '2025-12-16 10:18:10'),
(23, 12, 1, 'cart_clear', NULL, 'Cart cleared: Nic Nac', '2025-12-16 10:18:10'),
(24, 8, 1, 'cart_clear', NULL, 'Cart cleared: Green Olive', '2025-12-16 10:18:10'),
(26, 16, -1, 'cart_add', NULL, 'User added to cart: Cadbury 5 Star Chocolate', '2025-12-16 10:24:33'),
(27, 24, -2, 'cart_update', NULL, 'Increased quantity by 2: Parachute Coconut Oil', '2025-12-16 10:24:39'),
(28, 25, -1, 'cart_add', NULL, 'User added to cart: Parachute Just For Baby - Baby Oil', '2025-12-16 10:25:45'),
(29, 24, 1, 'cart_update', NULL, 'Decreased quantity by 1: Parachute Coconut Oil', '2025-12-16 10:26:05'),
(30, 25, -2, 'cart_update', NULL, 'Increased quantity by 2: Parachute Just For Baby - Baby Oil', '2025-12-16 10:26:05'),
(31, 25, 2, 'cart_update', NULL, 'Decreased quantity by 2: Parachute Just For Baby - Baby Oil', '2025-12-16 10:27:50'),
(32, 24, -1, 'cart_update', NULL, 'Increased quantity by 1: Parachute Coconut Oil', '2025-12-16 10:31:52'),
(33, 24, 1, 'cart_update', NULL, 'Decreased quantity by 1: Parachute Coconut Oil', '2025-12-16 10:34:31'),
(34, 25, -1, 'cart_update', NULL, 'Increased quantity by 1: Parachute Just For Baby - Baby Oil', '2025-12-16 10:34:52'),
(35, 24, -1, 'cart_update', NULL, 'Increased quantity by 1: Parachute Coconut Oil', '2025-12-16 10:35:13'),
(36, 24, 1, 'cart_update', NULL, 'Decreased quantity by 1: Parachute Coconut Oil', '2025-12-16 10:38:50'),
(37, 24, -1, 'cart_update', NULL, 'Increased quantity by 1: Parachute Coconut Oil', '2025-12-16 10:38:54'),
(38, 16, -1, 'cart_update', NULL, 'Increased quantity by 1: Cadbury 5 Star Chocolate', '2025-12-16 10:39:35'),
(39, 24, 1, 'cart_update', NULL, 'Decreased quantity by 1: Parachute Coconut Oil', '2025-12-16 10:39:35'),
(40, 24, -1, 'cart_update', NULL, 'Increased quantity by 1: Parachute Coconut Oil', '2025-12-16 10:39:41'),
(41, 16, -2, 'cart_update', NULL, 'Increased quantity by 2: Cadbury 5 Star Chocolate', '2025-12-16 10:39:55'),
(42, 24, 1, 'cart_update', NULL, 'Decreased quantity by 1: Parachute Coconut Oil', '2025-12-16 10:39:55'),
(43, 24, -1, 'cart_update', NULL, 'Increased quantity by 1: Parachute Coconut Oil', '2025-12-16 10:40:10'),
(44, 24, 3, 'cart_clear', NULL, 'Cart cleared: Parachute Coconut Oil', '2025-12-16 10:40:17'),
(45, 16, 4, 'cart_clear', NULL, 'Cart cleared: Cadbury 5 Star Chocolate', '2025-12-16 10:40:17'),
(46, 25, 2, 'cart_clear', NULL, 'Cart cleared: Parachute Just For Baby - Baby Oil', '2025-12-16 10:40:18'),
(47, 25, -1, 'cart_add', NULL, 'User added to cart: Parachute Just For Baby - Baby Oil', '2025-12-16 14:16:18'),
(48, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2025-12-16 14:16:24'),
(49, 19, -1, 'cart_add', NULL, 'User added to cart: Lay\'s Thai Style Spicy Chicken Potato Chips', '2025-12-16 14:16:28'),
(50, 17, -1, 'cart_add', NULL, 'User added to cart: Munch chocolate', '2025-12-16 14:16:32'),
(51, 10, -1, 'cart_add', NULL, 'User added to cart: Avocados', '2025-12-16 14:16:41'),
(52, 10, -2, 'cart_update', NULL, 'Increased quantity by 2: Avocados', '2025-12-16 14:17:05'),
(53, 19, -1, 'cart_update', NULL, 'Increased quantity by 1: Lay\'s Thai Style Spicy Chicken Potato Chips', '2025-12-16 14:17:05'),
(54, 24, -2, 'cart_update', NULL, 'Increased quantity by 2: Parachute Coconut Oil', '2025-12-16 14:17:05'),
(55, 25, -3, 'cart_update', NULL, 'Increased quantity by 3: Parachute Just For Baby - Baby Oil', '2025-12-16 14:17:05'),
(56, 25, 4, 'cart_clear', NULL, 'Cart cleared: Parachute Just For Baby - Baby Oil', '2025-12-16 14:38:26'),
(57, 24, 3, 'cart_clear', NULL, 'Cart cleared: Parachute Coconut Oil', '2025-12-16 14:38:26'),
(58, 19, 2, 'cart_clear', NULL, 'Cart cleared: Lay\'s Thai Style Spicy Chicken Potato Chips', '2025-12-16 14:38:26'),
(59, 17, 1, 'cart_clear', NULL, 'Cart cleared: Munch chocolate', '2025-12-16 14:38:26'),
(60, 10, 3, 'cart_clear', NULL, 'Cart cleared: Avocados', '2025-12-16 14:38:26'),
(61, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2025-12-16 15:52:05'),
(62, 24, -2, 'cart_update', NULL, 'Increased quantity by 2: Parachute Coconut Oil', '2025-12-16 15:52:14'),
(63, 21, -1, 'cart_add', NULL, 'User added to cart: PRAN Hajom Candy Lozenge', '2025-12-16 15:55:28'),
(64, 21, -3, 'cart_update', NULL, 'Increased quantity by 3: PRAN Hajom Candy Lozenge', '2025-12-16 15:55:36'),
(65, 24, 1, 'cart_update', NULL, 'Decreased quantity by 1: Parachute Coconut Oil', '2025-12-16 15:55:36'),
(66, 24, -1, 'cart_update', NULL, 'Increased quantity by 1: Parachute Coconut Oil', '2025-12-16 15:55:59'),
(67, 21, -4, 'cart_update', NULL, 'Increased quantity by 4: PRAN Hajom Candy Lozenge', '2025-12-16 15:56:07'),
(68, 24, 1, 'cart_update', NULL, 'Decreased quantity by 1: Parachute Coconut Oil', '2025-12-16 15:56:08'),
(69, 24, 1, 'cart_update', NULL, 'Decreased quantity by 1: Parachute Coconut Oil', '2025-12-16 15:56:14'),
(70, 24, 1, 'cart_clear', NULL, 'Cart cleared: Parachute Coconut Oil', '2025-12-16 15:56:42'),
(71, 21, 8, 'cart_clear', NULL, 'Cart cleared: PRAN Hajom Candy Lozenge', '2025-12-16 15:56:42'),
(72, 25, -1, 'cart_add', NULL, 'User added to cart: Parachute Just For Baby - Baby Oil', '2025-12-16 15:56:46'),
(73, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-16 15:56:54'),
(74, 20, -3, 'cart_update', NULL, 'Increased quantity by 3: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-16 15:56:59'),
(75, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-16 16:05:58'),
(76, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-16 16:06:11'),
(77, 16, -1, 'cart_add', NULL, 'User added to cart: Cadbury 5 Star Chocolate', '2025-12-16 16:06:23'),
(78, 16, -1, 'cart_update', NULL, 'Increased quantity by 1: Cadbury 5 Star Chocolate', '2025-12-16 16:06:27'),
(79, 16, -1, 'cart_update', NULL, 'Increased quantity by 1: Cadbury 5 Star Chocolate', '2025-12-16 16:06:38'),
(80, 25, 1, 'cart_remove', NULL, 'Item removed from cart: Parachute Just For Baby - Baby Oil', '2025-12-16 16:12:53'),
(81, 20, 6, 'cart_clear', NULL, 'Cart cleared: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-16 16:13:21'),
(82, 16, 3, 'cart_clear', NULL, 'Cart cleared: Cadbury 5 Star Chocolate', '2025-12-16 16:13:21'),
(83, 26, -1, 'cart_add', NULL, 'User added to cart: Kodomo Baby  Shampoo Original', '2025-12-16 16:20:45'),
(84, 26, -1, 'cart_update', NULL, 'Increased quantity by 1: Kodomo Baby  Shampoo Original', '2025-12-16 16:20:52'),
(85, 16, -1, 'cart_add', NULL, 'User added to cart: Cadbury 5 Star Chocolate', '2025-12-16 16:21:03'),
(86, 17, -1, 'cart_add', NULL, 'User added to cart: Munch chocolate', '2025-12-16 16:21:09'),
(87, 17, -3, 'cart_update', NULL, 'Increased quantity by 3: Munch chocolate', '2025-12-16 16:21:13'),
(88, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2025-12-16 16:47:46'),
(89, 26, 2, 'cart_clear', NULL, 'Cart cleared: Kodomo Baby  Shampoo Original', '2025-12-16 17:38:24'),
(90, 16, 1, 'cart_clear', NULL, 'Cart cleared: Cadbury 5 Star Chocolate', '2025-12-16 17:38:24'),
(91, 17, 4, 'cart_clear', NULL, 'Cart cleared: Munch chocolate', '2025-12-16 17:38:24'),
(92, 24, 1, 'cart_clear', NULL, 'Cart cleared: Parachute Coconut Oil', '2025-12-16 17:38:24'),
(93, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2025-12-16 18:23:13'),
(94, 23, -1, 'cart_add', NULL, 'User added to cart: Livon Hair Serum', '2025-12-16 18:23:20'),
(95, 17, -1, 'cart_add', NULL, 'User added to cart: Munch chocolate', '2025-12-16 18:23:27'),
(96, 14, -1, 'cart_add', NULL, 'User added to cart: Cadbury Dairy Milk Silk Chocolate Bar', '2025-12-16 18:23:32'),
(97, 14, -1, 'cart_update', NULL, 'Increased quantity by 1: Cadbury Dairy Milk Silk Chocolate Bar', '2025-12-16 18:23:40'),
(98, 23, -1, 'cart_update', NULL, 'Increased quantity by 1: Livon Hair Serum', '2025-12-16 18:23:40'),
(99, 24, -1, 'cart_update', NULL, 'Increased quantity by 1: Parachute Coconut Oil', '2025-12-16 18:23:48'),
(100, 21, -1, 'cart_add', NULL, 'User added to cart: PRAN Hajom Candy Lozenge', '2025-12-16 18:50:52'),
(101, 21, -4, 'cart_update', NULL, 'Increased quantity by 4: PRAN Hajom Candy Lozenge', '2025-12-16 18:50:56'),
(102, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-16 18:51:00'),
(103, 21, -1, 'cart_update', NULL, 'Increased quantity by 1: PRAN Hajom Candy Lozenge', '2025-12-16 18:51:10'),
(104, 20, -2, 'cart_update', NULL, 'Increased quantity by 2: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-16 18:51:18'),
(105, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2025-12-16 18:51:23'),
(106, 23, -1, 'cart_add', NULL, 'User added to cart: Livon Hair Serum', '2025-12-16 18:51:42'),
(107, 23, -1, 'cart_update', NULL, 'Increased quantity by 1: Livon Hair Serum', '2025-12-16 18:51:48'),
(108, 26, -1, 'cart_add', NULL, 'User added to cart: Kodomo Baby  Shampoo Original', '2025-12-16 19:17:12'),
(109, 25, -1, 'cart_add', NULL, 'User added to cart: Parachute Just For Baby - Baby Oil', '2025-12-16 19:17:18'),
(110, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2025-12-16 19:17:22'),
(111, 10, -1, 'cart_add', NULL, 'User added to cart: Avocados', '2025-12-16 19:17:41'),
(112, 10, -1, 'cart_update', NULL, 'Increased quantity by 1: Avocados', '2025-12-16 19:17:46'),
(113, 26, -1, 'cart_add', NULL, 'User added to cart: Kodomo Baby  Shampoo Original', '2025-12-16 19:20:49'),
(114, 26, -1, 'cart_update', NULL, 'Increased quantity by 1: Kodomo Baby  Shampoo Original', '2025-12-16 19:20:54'),
(115, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2025-12-16 19:20:57'),
(116, 23, -1, 'cart_add', NULL, 'User added to cart: Livon Hair Serum', '2025-12-16 19:21:22'),
(117, 13, -1, 'cart_add', NULL, 'User added to cart: Cadbury Bournville Dark Chocolate Bar', '2025-12-16 19:21:36'),
(118, 18, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Stix Bar-B-Q Chips', '2025-12-16 19:21:51'),
(119, 24, 1, 'cart_remove', NULL, 'Item removed from cart: Parachute Coconut Oil', '2025-12-16 19:22:11'),
(120, 18, -1, 'cart_update', NULL, 'Increased quantity by 1: Ifad Eggy Stix Bar-B-Q Chips', '2025-12-16 19:22:18'),
(121, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2025-12-16 19:42:18'),
(122, 24, 1, 'cart_remove', NULL, 'Item removed from cart: Parachute Coconut Oil', '2025-12-16 19:42:25'),
(123, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-16 19:42:29'),
(124, 20, -2, 'cart_update', NULL, 'Increased quantity by 2: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-16 19:42:33'),
(125, 16, -1, 'cart_add', NULL, 'User added to cart: Cadbury 5 Star Chocolate', '2025-12-16 19:42:41'),
(126, 20, -1, 'cart_update', NULL, 'Increased quantity by 1: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-16 19:42:45'),
(127, 16, -2, 'cart_update', NULL, 'Increased quantity by 2: Cadbury 5 Star Chocolate', '2025-12-16 19:42:49'),
(128, 16, -1, 'cart_add', NULL, 'User added to cart: Cadbury 5 Star Chocolate', '2025-12-16 19:50:28'),
(129, 26, -1, 'cart_add', NULL, 'User added to cart: Kodomo Baby  Shampoo Original', '2025-12-16 20:06:56'),
(130, 26, -1, 'cart_update', NULL, 'Increased quantity by 1: Kodomo Baby  Shampoo Original', '2025-12-16 20:07:01'),
(131, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2025-12-16 20:07:05'),
(132, 24, 1, 'cart_remove', NULL, 'Item removed from cart: Parachute Coconut Oil', '2025-12-16 20:07:09'),
(133, 15, -1, 'cart_add', NULL, 'User added to cart: Amul Dark Chocolate', '2025-12-16 20:07:12'),
(134, 15, -1, 'cart_update', NULL, 'Increased quantity by 1: Amul Dark Chocolate', '2025-12-16 20:07:16'),
(135, 26, -1, 'cart_add', NULL, 'User added to cart: Kodomo Baby  Shampoo Original', '2025-12-16 21:05:28'),
(136, 26, 1, 'cart_remove', NULL, 'Item removed from cart: Kodomo Baby  Shampoo Original', '2025-12-16 21:05:38'),
(137, 21, -1, 'cart_add', NULL, 'User added to cart: PRAN Hajom Candy Lozenge', '2025-12-16 21:05:45'),
(138, 21, -3, 'cart_update', NULL, 'Increased quantity by 3: PRAN Hajom Candy Lozenge', '2025-12-16 21:05:49'),
(139, 16, -1, 'cart_add', NULL, 'User added to cart: Cadbury 5 Star Chocolate', '2025-12-16 21:05:55'),
(140, 16, -1, 'cart_update', NULL, 'Increased quantity by 1: Cadbury 5 Star Chocolate', '2025-12-16 21:06:00'),
(141, 21, -1, 'cart_update', NULL, 'Increased quantity by 1: PRAN Hajom Candy Lozenge', '2025-12-16 21:06:00'),
(142, 7, -1, 'cart_add', NULL, 'User added to cart: Pineapple', '2025-12-16 21:06:11'),
(143, 16, -1, 'cart_add', NULL, 'User added to cart: Cadbury 5 Star Chocolate', '2025-12-16 21:10:40'),
(144, 13, -1, 'cart_add', NULL, 'User added to cart: Cadbury Bournville Dark Chocolate Bar', '2025-12-16 21:10:47'),
(145, 4, -1, 'cart_add', NULL, 'User added to cart: Banana', '2025-12-16 21:10:56'),
(146, 19, -1, 'cart_add', NULL, 'User added to cart: Lay\'s Thai Style Spicy Chicken Potato Chips', '2025-12-17 05:37:57'),
(147, 12, -1, 'cart_add', NULL, 'User added to cart: Nic Nac', '2025-12-17 05:38:13'),
(148, 25, -1, 'cart_add', NULL, 'User added to cart: Parachute Just For Baby - Baby Oil', '2025-12-17 05:42:10'),
(149, 12, 1, 'cart_remove', NULL, 'Item removed from cart: Nic Nac', '2025-12-17 05:42:19'),
(150, 25, -1, 'cart_update', NULL, 'Increased quantity by 1: Parachute Just For Baby - Baby Oil', '2025-12-17 05:46:30'),
(151, 25, -1, 'cart_update', NULL, 'Increased quantity by 1: Parachute Just For Baby - Baby Oil', '2025-12-17 05:46:35'),
(152, 19, 1, 'cart_clear', NULL, 'Cart cleared: Lay\'s Thai Style Spicy Chicken Potato Chips', '2025-12-17 05:54:45'),
(153, 25, 3, 'cart_clear', NULL, 'Cart cleared: Parachute Just For Baby - Baby Oil', '2025-12-17 05:54:45'),
(154, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-17 05:55:09'),
(155, 12, -1, 'cart_add', NULL, 'User added to cart: Nic Nac', '2025-12-17 05:55:16'),
(156, 23, -1, 'cart_add', NULL, 'User added to cart: Livon Hair Serum', '2025-12-17 05:58:53'),
(157, 23, 1, 'cart_clear', NULL, 'Cart cleared: Livon Hair Serum', '2025-12-17 05:59:12'),
(158, 21, -1, 'cart_add', NULL, 'User added to cart: PRAN Hajom Candy Lozenge', '2025-12-17 06:00:14'),
(159, 21, -5, 'cart_update', NULL, 'Increased quantity by 5: PRAN Hajom Candy Lozenge', '2025-12-17 06:00:19'),
(160, 4, -1, 'cart_add', NULL, 'User added to cart: Banana', '2025-12-17 06:00:24'),
(161, 16, -1, 'cart_add', NULL, 'User added to cart: Cadbury 5 Star Chocolate', '2025-12-17 06:00:29'),
(162, 4, -2, 'cart_update', NULL, 'Increased quantity by 2: Banana', '2025-12-17 06:00:45'),
(163, 21, 4, 'cart_update', NULL, 'Decreased quantity by 4: PRAN Hajom Candy Lozenge', '2025-12-17 06:00:45'),
(164, 25, -1, 'cart_add', NULL, 'User added to cart: Parachute Just For Baby - Baby Oil', '2025-12-17 06:19:47'),
(165, 16, -1, 'cart_add', NULL, 'User added to cart: Cadbury 5 Star Chocolate', '2025-12-17 06:19:53'),
(166, 16, -2, 'cart_update', NULL, 'Increased quantity by 2: Cadbury 5 Star Chocolate', '2025-12-17 06:19:56'),
(167, 25, -1, 'cart_add', NULL, 'User added to cart: Parachute Just For Baby - Baby Oil', '2025-12-17 06:34:40'),
(168, 25, -1, 'cart_add', NULL, 'User added to cart: Parachute Just For Baby - Baby Oil', '2025-12-17 06:48:33'),
(169, 25, -1, 'cart_update', NULL, 'Increased quantity by 1: Parachute Just For Baby - Baby Oil', '2025-12-17 06:48:37'),
(170, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-17 06:48:41'),
(171, 25, -1, 'cart_add', NULL, 'User added to cart: Parachute Just For Baby - Baby Oil', '2025-12-17 06:49:02'),
(172, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2025-12-17 07:00:46'),
(173, 16, -1, 'cart_add', NULL, 'User added to cart: Cadbury 5 Star Chocolate', '2025-12-17 07:00:53'),
(174, 26, -1, 'cart_add', NULL, 'User added to cart: Kodomo Baby  Shampoo Original', '2025-12-17 15:40:20'),
(175, 21, -1, 'cart_add', NULL, 'User added to cart: PRAN Hajom Candy Lozenge', '2025-12-17 15:40:27'),
(176, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-17 15:40:32'),
(177, 20, -1, 'cart_update', NULL, 'Increased quantity by 1: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-17 15:40:38'),
(178, 28, -1, 'cart_add', NULL, 'User added to cart: International Germany product Nivea Shea Smooth Body Lotion  ', '2025-12-19 18:37:42'),
(179, 54, -1, 'cart_add', NULL, 'User added to cart: Nivea Soft Moisturizing Cream', '2025-12-19 18:38:43'),
(180, 54, -1, 'cart_add', NULL, 'User added to cart: Nivea Soft Moisturizing Cream', '2025-12-19 19:07:49'),
(181, 23, -1, 'cart_add', NULL, 'User added to cart: Livon Hair Serum', '2025-12-19 19:07:58'),
(182, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2025-12-19 20:53:29'),
(183, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2025-12-23 00:36:11'),
(184, 7, -1, 'cart_add', NULL, 'User added to cart: Pineapple', '2025-12-23 02:09:42'),
(185, 21, -1, 'cart_add', NULL, 'User added to cart: PRAN Hajom Candy Lozenge', '2025-12-23 02:11:04'),
(186, 21, -16, 'cart_update', NULL, 'Increased quantity by 16: PRAN Hajom Candy Lozenge', '2025-12-23 02:11:16'),
(187, 21, 4, 'cart_update', NULL, 'Decreased quantity by 4: PRAN Hajom Candy Lozenge', '2025-12-23 02:11:24'),
(188, 7, 2, 'cart_clear', NULL, 'Cart cleared: Pineapple', '2025-12-23 02:17:23'),
(189, 21, 14, 'cart_clear', NULL, 'Cart cleared: PRAN Hajom Candy Lozenge', '2025-12-23 02:17:23'),
(190, 21, -1, 'cart_add', NULL, 'User added to cart: PRAN Hajom Candy Lozenge', '2025-12-23 02:17:46'),
(191, 21, -4, 'cart_update', NULL, 'Increased quantity by 4: PRAN Hajom Candy Lozenge', '2025-12-23 02:17:50'),
(192, 21, -4, 'cart_update', NULL, 'Increased quantity by 4: PRAN Hajom Candy Lozenge', '2025-12-23 02:18:12'),
(193, 21, -25, 'cart_update', NULL, 'Increased quantity by 25: PRAN Hajom Candy Lozenge', '2025-12-23 02:18:44'),
(194, 21, 25, 'cart_update', NULL, 'Decreased quantity by 25: PRAN Hajom Candy Lozenge', '2025-12-23 02:18:55'),
(195, 21, -1, 'cart_update', NULL, 'Increased quantity by 1: PRAN Hajom Candy Lozenge', '2025-12-23 02:18:58'),
(196, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2025-12-23 02:19:32'),
(197, 18, 1, 'cart_clear', NULL, 'Cart cleared: Ifad Eggy Stix Bar-B-Q Chips', '2025-12-23 06:34:22'),
(198, 16, 1, 'cart_clear', NULL, 'Cart cleared: Cadbury 5 Star Chocolate', '2025-12-23 06:34:23'),
(199, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2025-12-24 19:52:25'),
(200, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2025-12-24 19:59:19'),
(201, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2025-12-24 20:08:55'),
(202, 19, -1, 'cart_add', NULL, 'User added to cart: Lay\'s Thai Style Spicy Chicken Potato Chips', '2025-12-24 20:29:23'),
(203, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2025-12-24 22:03:24'),
(204, 28, -1, 'cart_update', NULL, 'Increased quantity by 1: Enchanteur Charming Perfumed Body Lotion ', '2025-12-24 22:03:29'),
(205, 15, -1, 'cart_add', NULL, 'User added to cart: Amul Dark Chocolate', '2025-12-25 07:03:01'),
(206, 17, -1, 'cart_add', NULL, 'User added to cart: Munch chocolate', '2025-12-25 07:03:07'),
(207, 26, -1, 'cart_add', NULL, 'User added to cart: Kodomo Baby  Shampoo Original', '2025-12-25 07:06:53'),
(208, 15, -1, 'cart_add', NULL, 'User added to cart: Amul Dark Chocolate', '2025-12-25 07:11:44'),
(209, 15, -1, 'cart_add', NULL, 'User added to cart: Amul Dark Chocolate', '2025-12-25 08:36:42'),
(210, 16, -1, 'cart_add', NULL, 'User added to cart: Cadbury 5 Star Chocolate', '2025-12-25 08:43:49'),
(211, 6, -1, 'cart_add', NULL, 'User added to cart: Strawberry', '2025-12-25 08:43:57'),
(212, 16, 1, 'cart_clear', NULL, 'Cart cleared: Cadbury 5 Star Chocolate', '2025-12-25 14:09:10'),
(213, 6, 1, 'cart_clear', NULL, 'Cart cleared: Strawberry', '2025-12-25 14:09:10'),
(214, 19, -1, 'cart_add', NULL, 'User added to cart: Lay\'s Thai Style Spicy Chicken Potato Chips', '2025-12-25 14:09:18'),
(215, 19, 1, 'cart_clear', NULL, 'Cart cleared: Lay\'s Thai Style Spicy Chicken Potato Chips', '2025-12-25 14:19:46'),
(216, 15, -1, 'cart_add', NULL, 'User added to cart: Amul Dark Chocolate', '2025-12-25 14:19:55'),
(217, 16, -1, 'cart_add', NULL, 'User added to cart: Cadbury 5 Star Chocolate', '2025-12-25 15:58:22'),
(218, 17, -1, 'cart_add', NULL, 'User added to cart: Munch chocolate', '2025-12-25 15:58:30'),
(219, 11, -1, 'cart_add', NULL, 'User added to cart: Pomegranet', '2025-12-25 15:58:53'),
(220, 10, -1, 'cart_add', NULL, 'User added to cart: Avocados', '2025-12-25 17:42:24'),
(221, 13, -1, 'cart_add', NULL, 'User added to cart: Cadbury Bournville Dark Chocolate Bar', '2025-12-25 17:44:00'),
(222, 17, -1, 'cart_add', NULL, 'User added to cart: Munch chocolate', '2025-12-25 17:44:05'),
(223, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2025-12-25 17:48:52'),
(224, 8, -1, 'cart_add', NULL, 'User added to cart: Green Olive', '2025-12-25 17:55:52'),
(225, 21, -1, 'cart_add', NULL, 'User added to cart: cavendish and harvey candy', '2025-12-26 07:27:58'),
(226, 23, -1, 'cart_add', NULL, 'User added to cart: Livon Hair Serum', '2025-12-26 15:12:03'),
(227, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2025-12-26 15:50:28'),
(228, 28, -1, 'cart_update', NULL, 'Increased quantity by 1: Enchanteur Charming Perfumed Body Lotion ', '2025-12-26 15:50:34'),
(229, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2025-12-26 15:57:58'),
(230, 22, -1, 'cart_add', NULL, 'User added to cart: The Belgain Heart Chocolate', '2025-12-26 16:05:16'),
(231, 22, -1, 'cart_update', NULL, 'Increased quantity by 1: The Belgain Heart Chocolate', '2025-12-26 16:05:21'),
(232, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2025-12-26 16:11:29'),
(233, 21, -1, 'cart_add', NULL, 'User added to cart: cavendish and harvey candy', '2025-12-26 16:11:35'),
(234, 21, -1, 'cart_add', NULL, 'User added to cart: cavendish and harvey candy', '2025-12-26 16:35:43'),
(235, 6, -1, 'cart_add', NULL, 'User added to cart: Strawberry', '2025-12-26 16:35:51'),
(236, 6, -1, 'cart_update', NULL, 'Increased quantity by 1: Strawberry', '2025-12-26 16:35:58'),
(237, 21, -1, 'cart_add', NULL, 'User added to cart: cavendish and harvey candy', '2025-12-27 02:39:44'),
(238, 25, -1, 'cart_add', NULL, 'User added to cart: Parachute Just For Baby - Baby Oil', '2025-12-27 02:50:06'),
(239, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-27 02:55:11'),
(240, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2025-12-27 03:04:50'),
(241, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2025-12-27 09:00:32'),
(242, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2025-12-27 09:00:57'),
(243, 23, -1, 'cart_add', NULL, 'User added to cart: Livon Hair Serum', '2025-12-27 09:01:05'),
(244, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2025-12-27 09:02:08'),
(245, 28, -2, 'cart_update', NULL, 'Increased quantity by 2: Enchanteur Charming Perfumed Body Lotion ', '2025-12-27 09:03:25'),
(246, 28, 1, 'cart_update', NULL, 'Decreased quantity by 1: Enchanteur Charming Perfumed Body Lotion ', '2025-12-27 09:03:41'),
(247, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2025-12-28 05:22:27'),
(248, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2025-12-28 05:30:22'),
(249, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2025-12-28 06:28:03'),
(250, 25, -1, 'cart_add', NULL, 'User added to cart: Parachute Just For Baby - Baby Oil', '2025-12-28 06:33:51'),
(251, 28, 1, 'cart_remove', NULL, 'Item removed from cart: Enchanteur Charming Perfumed Body Lotion ', '2025-12-28 07:00:15'),
(252, 29, 1, 'cart_remove', NULL, 'Item removed from cart: Enchanteur Romantic Deo spray', '2025-12-28 07:00:20'),
(253, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2025-12-28 11:38:03'),
(254, 21, -1, 'cart_add', NULL, 'User added to cart: cavendish and harvey candy', '2025-12-28 11:51:16'),
(255, 23, -1, 'cart_add', NULL, 'User added to cart: Livon Hair Serum', '2025-12-28 11:56:02'),
(256, 19, -1, 'cart_add', NULL, 'User added to cart: Lay\'s Thai Style Spicy Chicken Potato Chips', '2025-12-28 12:24:00'),
(257, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2025-12-28 13:37:53'),
(258, 21, -1, 'cart_add', NULL, 'User added to cart: cavendish and harvey candy', '2025-12-28 13:39:57'),
(259, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2025-12-28 13:40:59'),
(260, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-28 13:57:03'),
(261, 21, -1, 'cart_add', NULL, 'User added to cart: cavendish and harvey candy', '2025-12-28 14:14:23'),
(262, 25, -1, 'cart_add', NULL, 'User added to cart: Parachute Just For Baby - Baby Oil', '2025-12-28 15:16:47'),
(263, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2025-12-28 15:28:33'),
(264, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2025-12-28 15:30:48'),
(265, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-28 17:03:12'),
(266, 15, -1, 'cart_add', NULL, 'User added to cart: Amul Dark Chocolate', '2025-12-28 17:03:19'),
(267, 15, -1, 'cart_update', NULL, 'Increased quantity by 1: Amul Dark Chocolate', '2025-12-28 17:03:51'),
(268, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-28 18:08:11'),
(269, 18, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Stix Bar-B-Q Chips', '2025-12-28 18:08:19'),
(270, 20, 2, 'cart_clear', NULL, 'Cart cleared: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-28 18:29:44'),
(271, 15, 2, 'cart_clear', NULL, 'Cart cleared: Amul Dark Chocolate', '2025-12-28 18:29:44'),
(272, 18, 1, 'cart_clear', NULL, 'Cart cleared: Ifad Eggy Stix Bar-B-Q Chips', '2025-12-28 18:29:44'),
(273, 19, -1, 'cart_add', NULL, 'User added to cart: Lay\'s Thai Style Spicy Chicken Potato Chips', '2025-12-28 18:30:12'),
(274, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-28 18:30:20'),
(275, 20, -1, 'cart_update', NULL, 'Increased quantity by 1: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-28 18:41:25'),
(276, 16, -1, 'cart_add', NULL, 'User added to cart: Cadbury 5 Star Chocolate', '2025-12-28 18:42:41'),
(277, 19, 1, 'cart_remove', NULL, 'Item removed from cart: Lay\'s Thai Style Spicy Chicken Potato Chips', '2025-12-28 18:42:44'),
(278, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-28 18:58:37'),
(279, 23, -1, 'cart_add', NULL, 'User added to cart: Livon Hair Serum', '2025-12-28 18:58:42'),
(280, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-28 19:05:33'),
(281, 19, -1, 'cart_add', NULL, 'User added to cart: Lay\'s Thai Style Spicy Chicken Potato Chips', '2025-12-28 19:05:37'),
(282, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2025-12-28 19:06:54'),
(283, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2025-12-28 19:17:50'),
(284, 26, -1, 'cart_add', NULL, 'User added to cart: Kodomo Baby  Shampoo Original', '2025-12-28 19:27:45'),
(285, 26, -1, 'cart_add', NULL, 'User added to cart: Kodomo Baby  Shampoo Original', '2025-12-28 19:34:50'),
(286, 22, -1, 'cart_add', NULL, 'User added to cart: The Belgain Heart Chocolate', '2025-12-28 19:34:59'),
(287, 25, -1, 'cart_add', NULL, 'User added to cart: Parachute Just For Baby - Baby Oil', '2025-12-28 19:47:44'),
(288, 26, 1, 'cart_remove', NULL, 'Item removed from cart: Kodomo Baby  Shampoo Original', '2025-12-28 19:47:53'),
(289, 16, -1, 'cart_add', NULL, 'User added to cart: Cadbury 5 Star Chocolate', '2025-12-28 19:55:21'),
(290, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2025-12-28 19:57:01'),
(291, 11, -1, 'cart_add', NULL, 'User added to cart: Pomegranet', '2025-12-28 20:06:30'),
(292, 16, -1, 'cart_add', NULL, 'User added to cart: Cadbury 5 Star Chocolate', '2025-12-28 20:11:04'),
(293, 15, -1, 'cart_add', NULL, 'User added to cart: Amul Dark Chocolate', '2025-12-28 20:14:41'),
(294, 3, -1, 'cart_add', NULL, 'User added to cart: Red Apple', '2025-12-28 20:25:00'),
(295, 17, -1, 'cart_add', NULL, 'User added to cart: Munch chocolate', '2025-12-28 20:27:50'),
(296, 17, -2, 'cart_update', NULL, 'Increased quantity by 2: Munch chocolate', '2025-12-28 20:27:54'),
(297, 19, -1, 'cart_add', NULL, 'User added to cart: Lay\'s Thai Style Spicy Chicken Potato Chips', '2025-12-28 20:40:07'),
(298, 19, -1, 'cart_update', NULL, 'Increased quantity by 1: Lay\'s Thai Style Spicy Chicken Potato Chips', '2025-12-28 20:40:10'),
(299, 10, -1, 'cart_add', NULL, 'User added to cart: Avocados', '2025-12-28 20:48:42'),
(300, 19, -1, 'cart_add', NULL, 'User added to cart: Lay\'s Thai Style Spicy Chicken Potato Chips', '2025-12-28 21:17:27'),
(301, 11, -1, 'cart_add', NULL, 'User added to cart: Pomegranet', '2025-12-28 21:38:01'),
(302, 14, -1, 'cart_add', NULL, 'User added to cart: Cadbury Dairy Milk Silk Chocolate Bar', '2025-12-28 21:39:45'),
(303, 14, 1, 'cart_clear', NULL, 'Cart cleared: Cadbury Dairy Milk Silk Chocolate Bar', '2025-12-28 21:39:58'),
(304, 15, -1, 'cart_add', NULL, 'User added to cart: Amul Dark Chocolate', '2025-12-28 21:40:23'),
(305, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2025-12-28 21:45:00'),
(306, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2025-12-28 21:45:06'),
(307, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2025-12-28 22:21:02'),
(308, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2025-12-28 22:21:05'),
(309, 29, -1, 'cart_update', NULL, 'Increased quantity by 1: Enchanteur Romantic Deo spray', '2025-12-28 22:21:09'),
(310, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2025-12-28 22:24:29'),
(311, 19, -1, 'cart_add', NULL, 'User added to cart: Lay\'s Thai Style Spicy Chicken Potato Chips', '2025-12-28 22:24:33'),
(312, 21, -1, 'cart_add', NULL, 'User added to cart: cavendish and harvey candy', '2025-12-28 22:27:41'),
(313, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2025-12-28 22:28:04'),
(314, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2025-12-29 05:35:42'),
(315, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2025-12-29 05:35:47'),
(316, 29, -1, 'cart_update', NULL, 'Increased quantity by 1: Enchanteur Romantic Deo spray', '2025-12-29 05:36:01'),
(317, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2025-12-30 05:51:40'),
(318, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2025-12-30 05:51:45'),
(319, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2025-12-30 06:28:39'),
(320, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2025-12-30 06:50:21'),
(321, 25, -1, 'cart_add', NULL, 'User added to cart: Parachute Just For Baby - Baby Oil', '2025-12-30 06:50:26'),
(322, 7, -1, 'cart_add', NULL, 'User added to cart: Pineapple', '2025-12-30 08:03:24'),
(323, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2025-12-30 10:11:58'),
(324, 26, -1, 'cart_add', NULL, 'User added to cart: Kodomo Baby  Shampoo Original', '2025-12-30 10:25:40'),
(325, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2025-12-30 15:02:55'),
(326, 25, -1, 'cart_add', NULL, 'User added to cart: Parachute Just For Baby - Baby Oil', '2025-12-30 16:23:30'),
(327, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-01 09:43:47'),
(328, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-01 09:48:18'),
(329, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2026-01-01 09:48:56'),
(330, 27, -1, 'cart_update', NULL, 'Increased quantity by 1: NIVEA Body Milk Intensive Moisture', '2026-01-01 09:49:08'),
(331, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2026-01-01 09:49:18'),
(332, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-01 10:07:52'),
(333, 2, -1, 'cart_add', NULL, 'User added to cart: Fresh Orange', '2026-01-01 10:08:00'),
(334, 10, -1, 'cart_add', NULL, 'User added to cart: Avocados', '2026-01-01 10:14:17'),
(335, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-01 10:19:16'),
(336, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2026-01-01 10:24:21'),
(337, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2026-01-01 10:25:10'),
(338, 21, -1, 'cart_add', NULL, 'User added to cart: cavendish and harvey candy', '2026-01-01 10:30:17'),
(339, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2026-01-01 10:30:25'),
(340, 24, -1, 'cart_update', NULL, 'Increased quantity by 1: Parachute Coconut Oil', '2026-01-01 10:30:28'),
(341, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2026-01-01 10:36:54'),
(342, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2026-01-01 11:01:15'),
(343, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-01 11:06:36'),
(344, 12, -1, 'cart_add', NULL, 'User added to cart: Nic Nac', '2026-01-01 11:09:08'),
(345, 15, -1, 'cart_add', NULL, 'User added to cart: Amul Dark Chocolate', '2026-01-01 11:21:55'),
(346, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-01 11:23:28'),
(347, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-01 11:28:42'),
(348, 11, -1, 'cart_add', NULL, 'User added to cart: Pomegranet', '2026-01-02 14:52:47'),
(349, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-02 15:02:25'),
(350, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-02 17:39:10'),
(351, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-02 17:40:24'),
(352, 28, 1, 'cart_update', NULL, 'Decreased quantity by 1: Enchanteur Charming Perfumed Body Lotion ', '2026-01-02 17:45:59'),
(353, 28, -1, 'cart_update', NULL, 'Increased quantity by 1: Enchanteur Charming Perfumed Body Lotion ', '2026-01-02 17:46:04'),
(354, 28, -1, 'cart_update', NULL, 'Increased quantity by 1: Enchanteur Charming Perfumed Body Lotion ', '2026-01-02 17:46:05'),
(355, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2026-01-02 17:46:12'),
(356, 29, -1, 'cart_update', NULL, 'Increased quantity by 1: Enchanteur Romantic Deo spray', '2026-01-02 17:46:15'),
(357, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-02 17:46:34'),
(358, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2026-01-02 17:46:44'),
(359, 20, -1, 'cart_update', NULL, 'Increased quantity by 1: Ifad Eggy Pillow Bar-B-Q Chips', '2026-01-02 17:46:47'),
(360, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-02 17:51:50'),
(361, 12, -1, 'cart_add', NULL, 'User added to cart: Nic Nac', '2026-01-02 17:51:55'),
(362, 15, -1, 'cart_add', NULL, 'User added to cart: Amul Dark Chocolate', '2026-01-02 18:18:34'),
(363, 15, -1, 'cart_update', NULL, 'Increased quantity by 1: Amul Dark Chocolate', '2026-01-02 18:18:37'),
(364, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2026-01-02 20:03:55'),
(365, 21, -1, 'cart_add', NULL, 'User added to cart: cavendish and harvey candy', '2026-01-02 20:04:03'),
(366, 21, -1, 'cart_update', NULL, 'Increased quantity by 1: cavendish and harvey candy', '2026-01-02 20:04:06'),
(367, 21, -1, 'cart_update', NULL, 'Increased quantity by 1: cavendish and harvey candy', '2026-01-02 20:04:09'),
(368, 17, -1, 'cart_add', NULL, 'User added to cart: Munch chocolate', '2026-01-02 20:04:15'),
(369, 17, -1, 'cart_update', NULL, 'Increased quantity by 1: Munch chocolate', '2026-01-02 20:04:23'),
(370, 17, -1, 'cart_update', NULL, 'Increased quantity by 1: Munch chocolate', '2026-01-02 20:04:27'),
(371, 13, -1, 'cart_add', NULL, 'User added to cart: Cadbury Bournville Dark Chocolate Bar', '2026-01-02 20:06:26'),
(372, 13, -1, 'cart_update', NULL, 'Increased quantity by 1: Cadbury Bournville Dark Chocolate Bar', '2026-01-02 20:06:28'),
(373, 3, -1, 'cart_add', NULL, 'User added to cart: Red Apple', '2026-01-02 20:06:33'),
(374, 3, -2, 'cart_update', NULL, 'Increased quantity by 2: Red Apple', '2026-01-02 20:06:36'),
(375, 3, -1, 'cart_update', NULL, 'Increased quantity by 1: Red Apple', '2026-01-02 20:06:38'),
(376, 22, -1, 'cart_add', NULL, 'User added to cart: The Belgain Heart Chocolate', '2026-01-02 20:06:42'),
(377, 22, -1, 'cart_update', NULL, 'Increased quantity by 1: The Belgain Heart Chocolate', '2026-01-02 20:06:47'),
(378, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2026-01-02 20:06:53'),
(379, 20, -2, 'cart_update', NULL, 'Increased quantity by 2: Ifad Eggy Pillow Bar-B-Q Chips', '2026-01-02 20:06:56'),
(380, 20, -4, 'cart_update', NULL, 'Increased quantity by 4: Ifad Eggy Pillow Bar-B-Q Chips', '2026-01-02 20:07:02'),
(381, 3, -1, 'cart_update', NULL, 'Increased quantity by 1: Red Apple', '2026-01-02 20:07:03'),
(382, 6, -1, 'cart_add', NULL, 'User added to cart: Strawberry', '2026-01-02 20:07:11'),
(383, 6, -2, 'cart_update', NULL, 'Increased quantity by 2: Strawberry', '2026-01-02 20:07:15'),
(384, 15, -1, 'cart_add', NULL, 'User added to cart: Amul Dark Chocolate', '2026-01-02 20:09:50'),
(385, 15, -2, 'cart_update', NULL, 'Increased quantity by 2: Amul Dark Chocolate', '2026-01-02 20:09:52'),
(386, 8, -1, 'cart_add', NULL, 'User added to cart: Green Olive', '2026-01-02 20:09:57'),
(387, 8, -2, 'cart_update', NULL, 'Increased quantity by 2: Green Olive', '2026-01-02 20:10:00'),
(388, 8, -2, 'cart_update', NULL, 'Increased quantity by 2: Green Olive', '2026-01-02 20:10:02'),
(389, 2, -1, 'cart_add', NULL, 'User added to cart: Fresh Orange', '2026-01-02 20:10:07'),
(390, 2, -4, 'cart_update', NULL, 'Increased quantity by 4: Fresh Orange', '2026-01-02 20:10:09'),
(391, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2026-01-02 20:10:15'),
(392, 27, -2, 'cart_update', NULL, 'Increased quantity by 2: NIVEA Body Milk Intensive Moisture', '2026-01-02 20:10:18'),
(393, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2026-01-02 20:10:25'),
(394, 18, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Stix Bar-B-Q Chips', '2026-01-02 20:10:41'),
(395, 18, -3, 'cart_update', NULL, 'Increased quantity by 3: Ifad Eggy Stix Bar-B-Q Chips', '2026-01-02 20:10:45'),
(396, 18, -2, 'cart_update', NULL, 'Increased quantity by 2: Ifad Eggy Stix Bar-B-Q Chips', '2026-01-02 20:10:48'),
(397, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2026-01-02 20:12:38'),
(398, 24, -1, 'cart_update', NULL, 'Increased quantity by 1: Parachute Coconut Oil', '2026-01-02 20:12:39'),
(399, 26, -1, 'cart_add', NULL, 'User added to cart: Kodomo Baby  Shampoo Original', '2026-01-02 20:12:49'),
(400, 26, -2, 'cart_update', NULL, 'Increased quantity by 2: Kodomo Baby  Shampoo Original', '2026-01-02 20:12:52'),
(401, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-02 22:07:50'),
(402, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2026-01-03 08:08:28'),
(403, 31, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Liquid', '2026-01-03 08:08:36'),
(404, 31, -1, 'cart_update', NULL, 'Increased quantity by 1: Vim Dishwashing Liquid', '2026-01-03 08:08:39'),
(405, 14, -1, 'cart_add', NULL, 'User added to cart: Cadbury Dairy Milk Silk Chocolate Bar', '2026-01-03 08:11:29'),
(406, 14, -1, 'cart_update', NULL, 'Increased quantity by 1: Cadbury Dairy Milk Silk Chocolate Bar', '2026-01-03 08:11:35'),
(407, 26, -1, 'cart_add', NULL, 'User added to cart: Kodomo Baby  Shampoo Original', '2026-01-03 08:11:42'),
(408, 32, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Bar', '2026-01-03 08:14:08'),
(409, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-03 08:14:15'),
(410, 28, -1, 'cart_update', NULL, 'Increased quantity by 1: Enchanteur Charming Perfumed Body Lotion ', '2026-01-03 08:14:19'),
(411, 25, -1, 'cart_add', NULL, 'User added to cart: Parachute Just For Baby - Baby Oil', '2026-01-03 08:14:39'),
(412, 17, -1, 'cart_add', NULL, 'User added to cart: Munch chocolate', '2026-01-03 08:14:47'),
(413, 17, -2, 'cart_update', NULL, 'Increased quantity by 2: Munch chocolate', '2026-01-03 08:14:52'),
(414, 17, -1, 'cart_update', NULL, 'Increased quantity by 1: Munch chocolate', '2026-01-03 08:14:53'),
(415, 31, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Liquid', '2026-01-03 08:46:46'),
(416, 31, -1, 'cart_update', NULL, 'Increased quantity by 1: Vim Dishwashing Liquid', '2026-01-03 08:46:49'),
(417, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2026-01-03 08:46:54'),
(418, 31, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Liquid', '2026-01-03 08:52:39'),
(419, 31, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Liquid', '2026-01-03 22:44:11'),
(420, 31, -1, 'cart_update', NULL, 'Increased quantity by 1: Vim Dishwashing Liquid', '2026-01-03 22:44:17'),
(421, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-03 22:44:25'),
(422, 32, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Bar', '2026-01-03 23:55:37'),
(423, 32, -1, 'cart_update', NULL, 'Increased quantity by 1: Vim Dishwashing Bar', '2026-01-03 23:55:39'),
(424, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2026-01-03 23:55:43'),
(425, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2026-01-03 23:55:48'),
(426, 24, -1, 'cart_update', NULL, 'Increased quantity by 1: Parachute Coconut Oil', '2026-01-03 23:55:51'),
(427, 15, -1, 'cart_add', NULL, 'User added to cart: Amul Dark Chocolate', '2026-01-03 23:56:00'),
(428, 15, -1, 'cart_add', NULL, 'User added to cart: Amul Dark Chocolate', '2026-01-03 23:56:04'),
(429, 15, -1, 'cart_update', NULL, 'Increased quantity by 1: Amul Dark Chocolate', '2026-01-03 23:56:08'),
(430, 19, -1, 'cart_add', NULL, 'User added to cart: Lay\'s Thai Style Spicy Chicken Potato Chips', '2026-01-03 23:56:12'),
(431, 19, -2, 'cart_update', NULL, 'Increased quantity by 2: Lay\'s Thai Style Spicy Chicken Potato Chips', '2026-01-03 23:56:14'),
(432, 7, -1, 'cart_add', NULL, 'User added to cart: Pineapple', '2026-01-03 23:56:20'),
(433, 7, -2, 'cart_update', NULL, 'Increased quantity by 2: Pineapple', '2026-01-03 23:56:22'),
(434, 7, -1, 'cart_update', NULL, 'Increased quantity by 1: Pineapple', '2026-01-03 23:56:24'),
(435, 27, 1, 'cart_clear', NULL, 'Cart cleared: NIVEA Body Milk Intensive Moisture', '2026-01-04 00:10:45'),
(436, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2026-01-04 07:31:30'),
(437, 29, -1, 'cart_update', NULL, 'Increased quantity by 1: Enchanteur Romantic Deo spray', '2026-01-04 07:31:33'),
(438, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-04 07:31:42'),
(439, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2026-01-04 07:32:01'),
(440, 20, -4, 'cart_update', NULL, 'Increased quantity by 4: Ifad Eggy Pillow Bar-B-Q Chips', '2026-01-04 07:32:04'),
(441, 10, -1, 'cart_add', NULL, 'User added to cart: Avocados', '2026-01-04 15:13:08'),
(442, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-04 15:26:22'),
(443, 28, -1, 'cart_update', NULL, 'Increased quantity by 1: Enchanteur Charming Perfumed Body Lotion ', '2026-01-04 15:26:24'),
(444, 19, -1, 'cart_add', NULL, 'User added to cart: Lay\'s Thai Style Spicy Chicken Potato Chips', '2026-01-04 15:33:17'),
(445, 19, -1, 'cart_update', NULL, 'Increased quantity by 1: Lay\'s Thai Style Spicy Chicken Potato Chips', '2026-01-04 15:33:19'),
(446, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2026-01-04 15:33:23'),
(447, 24, -1, 'cart_update', NULL, 'Increased quantity by 1: Parachute Coconut Oil', '2026-01-04 15:33:25'),
(448, 30, -1, 'cart_add', NULL, 'User added to cart: Trix Lemon Dish Washing Liquid 1Ltr.', '2026-01-04 17:58:12'),
(449, 30, -1, 'cart_update', NULL, 'Increased quantity by 1: Trix Lemon Dish Washing Liquid 1Ltr.', '2026-01-04 17:58:15'),
(450, 30, -1, 'cart_update', NULL, 'Increased quantity by 1: Trix Lemon Dish Washing Liquid 1Ltr.', '2026-01-04 17:58:17'),
(451, 24, -1, 'cart_add', NULL, 'User added to cart: Parachute Coconut Oil', '2026-01-04 17:58:25'),
(452, 24, -1, 'cart_update', NULL, 'Increased quantity by 1: Parachute Coconut Oil', '2026-01-04 17:58:29'),
(453, 31, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Liquid', '2026-01-04 18:53:38'),
(454, 31, -2, 'cart_update', NULL, 'Increased quantity by 2: Vim Dishwashing Liquid', '2026-01-04 18:53:41'),
(455, 31, 1, 'cart_update', NULL, 'Decreased quantity by 1: Vim Dishwashing Liquid', '2026-01-04 18:53:54'),
(456, 31, 1, 'cart_update', NULL, 'Decreased quantity by 1: Vim Dishwashing Liquid', '2026-01-04 18:53:56'),
(457, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2026-01-04 18:54:02'),
(458, 32, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Bar', '2026-01-04 21:27:54'),
(459, 32, -1, 'cart_update', NULL, 'Increased quantity by 1: Vim Dishwashing Bar', '2026-01-04 21:27:57'),
(460, 15, -1, 'cart_add', NULL, 'User added to cart: Amul Dark Chocolate', '2026-01-04 21:28:08'),
(461, 31, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Liquid', '2026-01-06 14:51:24'),
(462, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-06 14:51:35'),
(463, 32, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Bar', '2026-01-06 19:35:51'),
(464, 32, -2, 'cart_update', NULL, 'Increased quantity by 2: Vim Dishwashing Bar', '2026-01-06 19:35:54'),
(465, 17, -1, 'cart_add', NULL, 'User added to cart: Munch chocolate', '2026-01-06 19:36:02'),
(466, 17, -3, 'cart_update', NULL, 'Increased quantity by 3: Munch chocolate', '2026-01-06 19:36:05'),
(467, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2026-01-06 20:38:12'),
(468, 20, 1, 'cart_clear', NULL, 'Cart cleared: Ifad Eggy Pillow Bar-B-Q Chips', '2026-01-06 20:41:02'),
(469, 31, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Liquid', '2026-01-06 22:07:39'),
(470, 32, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Bar', '2026-01-06 22:30:51'),
(471, 31, 1, 'cart_remove', NULL, 'Item removed from cart: Vim Dishwashing Liquid', '2026-01-06 22:31:04'),
(472, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2026-01-06 22:31:24');
INSERT INTO `stock_logs` (`id`, `product_id`, `quantity_change`, `action_type`, `session_id`, `notes`, `created_at`) VALUES
(473, 30, -1, 'cart_update', NULL, 'Increased quantity by 1: Trix Lemon Dish Washing Liquid 1Ltr.', '2026-01-06 22:40:44'),
(474, 27, 1, 'cart_clear', NULL, 'Cart cleared: NIVEA Body Milk Intensive Moisture', '2026-01-06 22:41:57'),
(475, 30, 2, 'cart_clear', NULL, 'Cart cleared: Trix Lemon Dish Washing Liquid 1Ltr.', '2026-01-06 22:41:57'),
(476, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2026-01-06 22:56:51'),
(477, 30, -1, 'cart_add', NULL, 'User added to cart: Trix Lemon Dish Washing Liquid 1Ltr.', '2026-01-06 23:24:25'),
(478, 30, 2, 'cart_remove', NULL, 'Item removed from cart: Trix Lemon Dish Washing Liquid 1Ltr.', '2026-01-06 23:24:37'),
(479, 32, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Bar', '2026-01-07 05:57:31'),
(480, 32, -3, 'cart_update', NULL, 'Increased quantity by 3: Vim Dishwashing Bar', '2026-01-07 05:57:39'),
(481, 3, -1, 'cart_add', NULL, 'User added to cart: Red Apple', '2026-01-08 00:51:41'),
(482, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-08 01:46:38'),
(483, 31, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Liquid', '2026-01-08 01:46:42'),
(484, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2026-01-08 01:46:55'),
(485, 33, -1, 'cart_add', NULL, 'User added to cart: Smart Heart Kitten Cat Food 450gm', '2026-01-10 05:13:17'),
(486, 35, -1, 'cart_add', NULL, 'User added to cart: Mop Cotton Refill (17\") China', '2026-01-10 05:13:26'),
(487, 34, -1, 'cart_add', NULL, 'User added to cart: WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', '2026-01-10 05:15:06'),
(488, 28, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Charming Perfumed Body Lotion ', '2026-01-10 05:15:13'),
(489, 37, -1, 'cart_add', NULL, 'User added to cart: Dove Men Care Fresh Clean 2 in 1 Shampoo + Conditioner', '2026-01-10 05:18:34'),
(490, 36, -1, 'cart_add', NULL, 'User added to cart: Rok Dishwashing Steel Scourer', '2026-01-10 05:18:38'),
(491, 37, -1, 'cart_add', NULL, 'User added to cart: Dove Men Care Fresh Clean 2 in 1 Shampoo + Conditioner', '2026-01-10 05:26:14'),
(492, 38, -1, 'cart_add', NULL, 'User added to cart: Layer\'r Shot Red Stallion Body Spray', '2026-01-10 05:41:11'),
(493, 38, 1, 'cart_remove', NULL, 'Item removed from cart: Layer\'r Shot Red Stallion Body Spray', '2026-01-10 05:41:22'),
(494, 37, -1, 'cart_add', NULL, 'User added to cart: Dove Men Care Fresh Clean 2 in 1 Shampoo + Conditioner', '2026-01-10 05:41:26'),
(495, 37, 1, 'cart_remove', NULL, 'Item removed from cart: Dove Men Care Fresh Clean 2 in 1 Shampoo + Conditioner', '2026-01-10 05:45:23'),
(496, 33, -1, 'cart_add', NULL, 'User added to cart: Smart Heart Kitten Cat Food 450gm', '2026-01-10 06:20:53'),
(497, 11, 1, 'cart_clear', NULL, 'Cart cleared: Pomegranet', '2026-01-10 18:47:41'),
(498, 29, 1, 'cart_clear', NULL, 'Cart cleared: Enchanteur Romantic Deo spray', '2026-01-10 18:47:41'),
(499, 33, -1, 'cart_add', NULL, 'User added to cart: Smart Heart Kitten Cat Food 450gm', '2026-01-10 20:34:51'),
(500, 33, -1, 'cart_update', NULL, 'Increased quantity by 1: Smart Heart Kitten Cat Food 450gm', '2026-01-10 20:34:54'),
(501, 13, -1, 'cart_add', NULL, 'User added to cart: Cadbury Bournville Dark Chocolate Bar', '2026-01-11 14:48:08'),
(502, 22, -1, 'cart_add', NULL, 'User added to cart: The Belgain Heart Chocolate', '2026-01-11 14:48:31'),
(503, 39, -1, 'cart_add', NULL, 'User added to cart: Cotton Duster', '2026-01-13 07:01:21'),
(504, 12, -1, 'cart_add', NULL, 'User added to cart: Nic Nac', '2026-01-13 13:17:46'),
(505, 39, -1, 'cart_add', NULL, 'User added to cart: Cotton Duster', '2026-01-13 13:18:16'),
(506, 39, -1, 'cart_update', NULL, 'Increased quantity by 1: Cotton Duster', '2026-01-13 13:18:18'),
(507, 39, -1, 'cart_update', NULL, 'Increased quantity by 1: Cotton Duster', '2026-01-13 13:18:20'),
(508, 34, -1, 'cart_add', NULL, 'User added to cart: WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', '2026-01-13 13:18:25'),
(509, 34, -1, 'cart_update', NULL, 'Increased quantity by 1: WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', '2026-01-13 13:18:28'),
(510, 12, 1, 'cart_remove', NULL, 'Item removed from cart: Nic Nac', '2026-01-13 13:22:19'),
(511, 40, -1, 'cart_add', NULL, 'User added to cart: Royal Canin Adult Persian 2', '2026-01-13 17:01:06'),
(512, 39, -1, 'cart_add', NULL, 'User added to cart: Cotton Duster', '2026-01-13 17:10:41'),
(513, 39, -1, 'cart_update', NULL, 'Increased quantity by 1: Cotton Duster', '2026-01-13 17:11:40'),
(514, 36, -1, 'cart_add', NULL, 'User added to cart: Rok Dishwashing Steel Scourer', '2026-01-13 17:11:51'),
(515, 39, 2, 'cart_clear', NULL, 'Cart cleared: Cotton Duster', '2026-01-13 17:12:45'),
(516, 36, 1, 'cart_clear', NULL, 'Cart cleared: Rok Dishwashing Steel Scourer', '2026-01-13 17:12:45'),
(517, 34, -1, 'cart_add', NULL, 'User added to cart: WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', '2026-01-13 17:12:49'),
(518, 34, -1, 'cart_update', NULL, 'Increased quantity by 1: WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', '2026-01-13 17:12:59'),
(519, 38, -1, 'cart_add', NULL, 'User added to cart: Layer\'r Shot Red Stallion Body Spray', '2026-01-13 17:45:18'),
(520, 34, -1, 'cart_add', NULL, 'User added to cart: WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', '2026-01-13 18:22:05'),
(521, 31, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Liquid', '2026-01-13 18:22:46'),
(522, 39, -1, 'cart_add', NULL, 'User added to cart: Cotton Duster', '2026-01-13 18:36:50'),
(523, 30, -1, 'cart_add', NULL, 'User added to cart: Trix Lemon Dish Washing Liquid 1Ltr.', '2026-01-13 18:36:56'),
(524, 34, -1, 'cart_add', NULL, 'User added to cart: WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', '2026-01-13 18:37:02'),
(525, 29, -1, 'cart_add', NULL, 'User added to cart: Enchanteur Romantic Deo spray', '2026-01-13 18:37:08'),
(526, 29, -1, 'cart_update', NULL, 'Increased quantity by 1: Enchanteur Romantic Deo spray', '2026-01-13 18:37:17'),
(527, 30, -1, 'cart_add', NULL, 'User added to cart: Trix Lemon Dish Washing Liquid 1Ltr.', '2026-01-13 19:46:25'),
(528, 31, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Liquid', '2026-01-13 19:46:30'),
(529, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2026-01-13 19:46:34'),
(530, 20, -1, 'cart_add', NULL, 'User added to cart: Ifad Eggy Pillow Bar-B-Q Chips', '2026-01-13 19:46:41'),
(531, 20, -2, 'cart_update', NULL, 'Increased quantity by 2: Ifad Eggy Pillow Bar-B-Q Chips', '2026-01-13 19:46:45'),
(532, 20, -2, 'cart_update', NULL, 'Increased quantity by 2: Ifad Eggy Pillow Bar-B-Q Chips', '2026-01-13 19:46:46'),
(533, 34, -1, 'cart_add', NULL, 'User added to cart: WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', '2026-01-13 19:48:38'),
(534, 33, -1, 'cart_add', NULL, 'User added to cart: Smart Heart Kitten Cat Food 450gm', '2026-01-13 19:48:42'),
(535, 26, -1, 'cart_add', NULL, 'User added to cart: Kodomo Baby  Shampoo Original', '2026-01-13 19:48:49'),
(536, 22, -1, 'cart_add', NULL, 'User added to cart: The Belgain Heart Chocolate', '2026-01-13 19:48:55'),
(537, 22, -4, 'cart_update', NULL, 'Increased quantity by 4: The Belgain Heart Chocolate', '2026-01-13 19:49:00'),
(538, 37, -1, 'cart_add', NULL, 'User added to cart: Dove Men Care Fresh Clean 2 in 1 Shampoo + Conditioner', '2026-01-13 19:49:16'),
(539, 34, -1, 'cart_add', NULL, 'User added to cart: WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', '2026-01-13 20:33:30'),
(540, 40, -1, 'cart_add', NULL, 'User added to cart: Royal Canin Adult Persian 2', '2026-01-13 20:33:35'),
(541, 40, -2, 'cart_update', NULL, 'Increased quantity by 2: Royal Canin Adult Persian 2', '2026-01-13 20:33:39'),
(542, 40, 2, 'cart_update', NULL, 'Decreased quantity by 2: Royal Canin Adult Persian 2', '2026-01-13 20:33:55'),
(543, 34, 1, 'cart_remove', NULL, 'Item removed from cart: WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', '2026-01-13 20:34:08'),
(544, 23, -1, 'cart_add', NULL, 'User added to cart: Livon Hair Serum', '2026-01-13 20:34:15'),
(545, 30, -1, 'cart_add', NULL, 'User added to cart: Trix Lemon Dish Washing Liquid 1Ltr.', '2026-01-13 20:53:31'),
(546, 40, -1, 'cart_add', NULL, 'User added to cart: Royal Canin Adult Persian 2', '2026-01-13 20:53:35'),
(547, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2026-01-13 20:59:21'),
(548, 38, -1, 'cart_add', NULL, 'User added to cart: Layer\'r Shot Red Stallion Body Spray', '2026-01-13 21:18:57'),
(549, 39, -1, 'cart_add', NULL, 'User added to cart: Cotton Duster', '2026-01-14 06:43:33'),
(550, 40, -1, 'cart_add', NULL, 'User added to cart: Royal Canin Adult Persian 2', '2026-01-14 13:14:09'),
(551, 40, 1, 'cart_remove', NULL, 'Item removed from cart: Royal Canin Adult Persian 2', '2026-01-14 13:14:31'),
(552, 32, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Bar', '2026-01-14 13:14:39'),
(553, 40, -1, 'cart_add', NULL, 'User added to cart: Royal Canin Adult Persian 2', '2026-01-14 13:17:44'),
(554, 39, -1, 'cart_add', NULL, 'User added to cart: Cotton Duster', '2026-01-14 13:18:56'),
(555, 39, -1, 'cart_add', NULL, 'User added to cart: Cotton Duster', '2026-01-14 14:05:19'),
(556, 36, -1, 'cart_add', NULL, 'User added to cart: Rok Dishwashing Steel Scourer', '2026-01-14 14:05:24'),
(557, 34, -1, 'cart_add', NULL, 'User added to cart: WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', '2026-01-14 14:09:08'),
(558, 40, -1, 'cart_add', NULL, 'User added to cart: Royal Canin Adult Persian 2', '2026-01-14 14:17:59'),
(559, 32, -1, 'cart_add', NULL, 'User added to cart: Vim Dishwashing Bar', '2026-01-16 11:42:35'),
(560, 34, -1, 'cart_add', NULL, 'User added to cart: WHISKAS Adult Dry Cat Food with Chicken & Rabbit Flavours ', '2026-01-16 11:42:40'),
(561, 33, -1, 'cart_add', NULL, 'User added to cart: Smart Heart Kitten Cat Food 450gm', '2026-01-17 04:29:24'),
(562, 27, -1, 'cart_add', NULL, 'User added to cart: NIVEA Body Milk Intensive Moisture', '2026-01-17 04:29:31');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` varchar(50) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `division` varchar(50) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `preferences` varchar(50) DEFAULT NULL,
  `newsletter` tinyint(4) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` tinyint(1) DEFAULT 1,
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expire` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone`, `address`, `city`, `zip_code`, `division`, `birth_date`, `profile_pic`, `preferences`, `newsletter`, `created_at`, `status`, `reset_token`, `reset_expire`) VALUES
(1, 'Mahbuba Rahman Jerin', 'rahmanmahbuba989@gmail.com', '$2y$10$k4VzBaFONXYkA3mbrV6ygeT/Cnl9JUt0qbNaI.ql1LObSKfwF7Pzu', '01312380824', 'Tongi', 'Dhaka', '1215', 'Dhaka', '2025-01-17', NULL, 'all', 0, '2025-11-27 11:21:52', 1, '1312053e5095b02e5bd55711ab6bd94e0a32ceffe84b2e7afef6c87234bf0bc8', '2025-12-23 22:17:27'),
(2, 'Anisa Moni', 'anisa78@gmail.com', '$2y$10$5bBtLegLZlJ/qrW909iMOue3LQEhuPeIxPkLhgP/5augNokw6oBOq', '01998746321', 'Tongi', 'Dhaka', '1217', 'Dhaka', '2003-02-13', '1764518007_anisa.jpg', 'all', 0, '2025-11-27 11:27:02', 1, NULL, NULL),
(3, 'Jui Rahman Pranti', 'jui111@gmail.com', '$2y$10$TVsKodT2KCVFY2UlE7I.BOWJzkZY//A/TMivhsZy1G3mR5AnfGNhC', '01998746354', 'Tongi', 'Dhaka', '1217', 'Dhaka', '2004-07-21', '1766474433_1764516047_IMG_20251130_090457_240x300.jpg', 'all', 0, '2025-11-27 11:31:45', 1, NULL, NULL),
(4, 'Maymuna Sikder', 'maymuna1@gmail.com', '$2y$10$8M.PZcbrDsdSxrmhx1w8q.P/6wPPXIqyZ9uuTcDDoap4E2c2gsvlu', '01997846372', 'Tongi', 'Dhaka', '1215', 'Dhaka', '2006-02-16', NULL, 'all', 0, '2025-11-28 07:51:21', 1, NULL, NULL),
(5, 'Fahim Sen', 'fahim12@gmail.com', '$2y$10$pFTcD3r2T/24l1Nji5EQHeByiKIiwLqeitBFrTlH5o7ebvZ9lu8qO', '01998834210', 'Tongi', 'Dhaka', '1215', 'Dhaka', '2002-06-05', NULL, 'all', 0, '2025-11-29 14:54:02', 0, NULL, NULL),
(6, 'Yeasmin Akter', 'yeasmin1@gmail.com', '$2y$10$1wgvhlPVrSpaws6sxRLfCubHfJl4BgIMLouhNdIUlu.dBuRNbb6u2', '01974647273', 'Malibug', 'Dhaka', '1215', 'Dhaka', '2003-06-20', NULL, 'all', 0, '2025-11-30 01:09:03', 1, NULL, NULL),
(7, 'Maliha Akter Munni', 'maliha@gmail.com', '$2y$10$zNT8R5L24/jBkIkHGSMsQeVdclRTwWBHxdCrVcGo3DQakdq8mSEum', '01998733344', 'Uttara,Dhaka', 'Dhaka', '1464', 'Dhaka', '2002-02-20', '1767123974_maliha.png', 'all', 0, '2025-12-06 12:45:39', 1, NULL, NULL),
(8, 'Anamika Akter', 'anamika@gmail.com', '$2y$10$saCE8fQ//JnLtEWtGu5Areo.hA2eEzk97UDl.ayTao5exPZOepF5q', '01998714402', 'House Building', 'Dhaka', '1218', 'Dhaka', '2005-02-12', NULL, 'all', 0, '2025-12-12 19:11:46', 1, NULL, NULL),
(9, 'Prime Das', 'prime@gmail.com', '$2y$10$C7a/KA/OLfEmmUbf7q7HUeZksC1HcocVk24WMEXRCxIccHetkwt46', '01883677845', 'Rampura', 'Dhaka', '1215', 'Dhaka', '2001-06-21', '1767484922_prime.png', 'all', 0, '2025-12-13 12:12:48', 1, NULL, NULL),
(10, 'Piyas Hoq', 'piyas@gmail.com', '$2y$10$oJTUV8WSBVZkTGjFJh5ne.gBSxA8ezbJZbWCFj3x9PYUUimAYUx7C', '01998588121', 'House Building', 'Dhaka', '1215', 'Dhaka', '1998-07-01', NULL, 'all', 0, '2025-12-19 06:44:27', 1, NULL, NULL),
(11, 'Sadia Akter', 'sadia@gmail.com', '$2y$10$R5Tc06B0HnfufrLomiw6Iuin.PUKheIQdo5kHlmr5Ix/OMn1w6BbC', '01557002431', 'Diya Bari', 'Dhaka', '1215', 'Dhaka', '1994-11-09', NULL, 'all', 0, '2025-12-19 06:46:04', 1, NULL, NULL),
(12, 'Moon Akter', 'moon@gmail.com', '$2y$10$yLjxhHCX2xW4f14TwWp0iOa60wFVC.3l8CAw3F2eiFai0kuQDPm..', '01993433391', 'Rampura', 'Dhaka', '1840', 'Dhaka', '1999-07-15', '1766514129_moon.png', 'all', 0, '2025-12-19 06:47:51', 1, NULL, NULL),
(13, 'Humayan Hossain', 'humayan@gmail.com', '$2y$10$u8lp5h0K5AoxW7rCAP4GZORuem3YrfFZO2tXvTgv2UOdBIrOvd3Ji', '01998373337', 'Tongi', 'Dhaka', '1215', 'Dhaka', '1998-06-18', NULL, 'all', 0, '2025-12-30 19:48:48', 1, NULL, NULL),
(14, 'Rahim Rahman', 'rahim@gmail.com', '$2y$10$DXQgWVJ9DaE3R4Uz/AFKAOG3joB4tgsGPFSfPQ6EDKZf9puENVwT2', '01833390287', 'Sirajganj', 'Rajshahi', '1736', 'Rajshahi', '1997-11-12', NULL, 'all', 0, '2025-12-30 19:51:18', 1, NULL, NULL),
(15, 'Piku Hoq', 'piku@gmail.com', '$2y$10$kVfpwIGZh0ntfXzTka.l3uUzRwc3aix/9XkYwqHUXoIGKlMGcfaqa', '01998434352', 'Station Road', 'Uttara', '1215', 'Dhaka', '2002-06-11', NULL, 'all', 0, '2025-12-30 19:53:05', 1, NULL, NULL),
(16, 'Jishan Islam', 'jishan@gmail.com', '$2y$10$FmFcGcoE576gP.bKXF9f8OfBFx3e5K62JPIXr4dJr3MX2WNrb80lG', '0198374657', 'Rajendrapur', 'Dhaka', '1267', 'Dhaka', '2003-01-07', NULL, 'all', 0, '2026-01-07 20:04:05', 1, NULL, NULL),
(17, 'Lima Akter', 'lima@gmail.com', '$2y$10$pArzg5DsRzmStYKU8p6ubugwI3cxVP9ExK4llzO6ZuiLIZzcQjb5W', '01928374710', 'Rajshahi Sadar', 'Rajshahi', '1234', 'Rajshahi', '2005-02-03', NULL, 'local', 0, '2026-01-14 13:32:02', 1, NULL, NULL),
(18, 'Jannat Rahman', 'jannat@gmail.com', '$2y$10$NlgvTQ2xGE65.hUykHLopejwCdw6RDk38CmZdqXUNddRibRGykJQG', '01992737787', 'Malibug', 'Dhaka', '1215', 'Dhaka', '2009-03-05', NULL, 'all', 0, '2026-01-17 03:57:44', 1, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_assignments`
--
ALTER TABLE `delivery_assignments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_locations`
--
ALTER TABLE `delivery_locations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_order` (`order_id`);

--
-- Indexes for table `delivery_persons`
--
ALTER TABLE `delivery_persons`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_delivery_email` (`email`);

--
-- Indexes for table `delivery_tracking_history`
--
ALTER TABLE `delivery_tracking_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `offer_products`
--
ALTER TABLE `offer_products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_offer_product` (`offer_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_delivery_status` (`delivery_status`),
  ADD KEY `idx_delivery_person_id` (`delivery_person_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `staff_notifications`
--
ALTER TABLE `staff_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stock_logs`
--
ALTER TABLE `stock_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_product_id` (`product_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `delivery_assignments`
--
ALTER TABLE `delivery_assignments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `delivery_locations`
--
ALTER TABLE `delivery_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=492;

--
-- AUTO_INCREMENT for table `delivery_persons`
--
ALTER TABLE `delivery_persons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `delivery_tracking_history`
--
ALTER TABLE `delivery_tracking_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=214;

--
-- AUTO_INCREMENT for table `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `offer_products`
--
ALTER TABLE `offer_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `staff_notifications`
--
ALTER TABLE `staff_notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `stock_logs`
--
ALTER TABLE `stock_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=563;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `delivery_locations`
--
ALTER TABLE `delivery_locations`
  ADD CONSTRAINT `delivery_locations_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `delivery_tracking_history`
--
ALTER TABLE `delivery_tracking_history`
  ADD CONSTRAINT `delivery_tracking_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `offer_products`
--
ALTER TABLE `offer_products`
  ADD CONSTRAINT `offer_products_ibfk_1` FOREIGN KEY (`offer_id`) REFERENCES `offers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `offer_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `reset_delivery_status_midnight` ON SCHEDULE EVERY 1 DAY STARTS '2026-01-03 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE delivery_persons
  SET status = 'available'
  WHERE status = 'busy' AND active = 1$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
