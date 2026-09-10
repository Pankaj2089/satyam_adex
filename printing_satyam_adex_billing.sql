-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 10, 2026 at 12:57 PM
-- Server version: 8.0.46
-- PHP Version: 8.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `printing_satyam_adex_billing`
--

-- --------------------------------------------------------

--
-- Table structure for table `blood_component_types`
--

CREATE TABLE `blood_component_types` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hsn_code` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blood_component_types`
--

INSERT INTO `blood_component_types` (`id`, `title`, `hsn_code`, `status`, `created_at`) VALUES
(1, 'Canopy', '730619', 1, '2022-10-29 15:25:29'),
(2, 'Vinyl', '39199090', 1, '2022-10-29 15:51:12'),
(3, 'Flex', '39219026', 1, '2022-10-31 10:40:56'),
(4, 'Bond', '35061000', 1, '2022-10-31 10:41:16'),
(5, 'Exhibition Display Stand ', '71619990', 1, '2022-10-31 10:41:58'),
(6, 'Sun Board With Vinyl', '39219025', 1, '2022-10-31 10:42:46'),
(7, 'Service Charges', '995479', 1, '2022-10-31 10:43:51'),
(8, 'Visiting Cards', '49090090', 1, '2022-10-31 10:44:48'),
(9, 'Digital Printout', '998912', 1, '2022-10-31 10:45:12'),
(10, 'A-3 Poster', '998912', 1, '2022-10-31 10:45:31'),
(11, 'Certificate Print', '998912', 1, '2022-10-31 10:45:52'),
(12, 'Advertisement Material Paper ', '998912', 1, '2022-10-31 19:55:39'),
(13, 'Advertisement Material Others ', '39219026', 1, '2022-10-31 19:57:02'),
(14, 'Certificate With Wooden Frame', '998912', 1, '2022-11-02 18:16:02'),
(15, 'Translite ', '998912', 1, '2022-11-02 18:16:30'),
(16, 'Other Print Material', '998912', 1, '2022-11-02 18:17:47'),
(17, 'PromoTable', '39239090', 1, '2022-11-04 12:17:58'),
(18, 'Flyer', '998912', 1, '2022-11-04 12:28:52'),
(19, 'Customer Brochures', '998912', 1, '2022-11-04 12:39:43'),
(20, 'QR Code With Stand', '998912', 1, '2022-11-04 12:43:04'),
(21, 'QR Code Print ', '998912', 1, '2022-11-04 12:50:19'),
(22, 'Phamplet For Camp', '998912', 1, '2022-11-07 15:53:24'),
(23, 'A-3 Gumming Poster', '998912', 1, '2022-11-07 16:03:40'),
(24, 'Handel Bag', '998912', 1, '2022-11-07 16:09:46'),
(25, 'Other Last Dues', '998912', 1, '2022-11-07 16:20:44'),
(26, 'Letter Head', '998912', 1, '2022-11-08 17:36:29'),
(27, 'Discharge Ticket', '998912', 1, '2022-11-08 17:37:16'),
(28, 'Flex Backdrope', '39219026', 1, '2022-11-16 12:02:19'),
(29, 'Flex Other.', '39219026', 1, '2022-11-16 12:02:44'),
(30, 'Flex Other..', '39219026', 1, '2022-11-16 12:03:03'),
(31, 'Name Sinage', '39199090', 1, '2022-11-21 18:02:15'),
(32, 'Tshirt WIth Print', '610910', 1, '2022-11-21 19:02:38'),
(33, 'Half Paint', '610910', 1, '2022-11-21 19:05:46'),
(34, 'I\'d Card ', '998912', 1, '2022-11-22 17:03:46'),
(35, 'ACRYLIC LED  SIGNAGE', '940560', 1, '2022-11-24 11:42:22'),
(36, 'GLOW SIGN BOARD', '940560', 1, '2022-11-24 11:44:15'),
(37, 'Iron Standee (Selfi Point)', '39219025', 1, '2022-12-01 15:14:26'),
(38, 'UltraSound Cover File', '998912', 1, '2022-12-05 13:19:12'),
(39, 'Latter Head Other', '998912', 1, '2022-12-05 14:16:12'),
(40, 'Slip Ped', '998912', 1, '2022-12-05 14:16:23'),
(41, 'Spiral Binding Charges', '998912', 1, '2022-12-09 11:55:53'),
(42, 'Progress Note Pad', '998912', 1, '2022-12-21 12:59:27'),
(43, 'Envelop A-4 Size', '998912', 1, '2022-12-21 12:59:57'),
(44, 'Envelop', '998912', 1, '2022-12-21 13:00:53'),
(45, 'Office File', '998912', 1, '2022-12-21 13:02:13'),
(46, 'Pesting Charge', '995479', 1, '2022-12-24 13:53:16'),
(47, 'Vinyl Pesting Charge', '995479', 1, '2022-12-24 13:54:40'),
(48, 'Flex Pesting Charge', '995479', 1, '2022-12-24 13:54:56'),
(49, 'Office Stationary  ', '998912', 1, '2022-12-27 14:05:08'),
(50, 'League Tag ', '998912', 1, '2022-12-27 14:05:24'),
(51, 'Cap With Print', '610910', 1, '2022-12-31 13:58:08'),
(52, 'Hoodies S/M/L', '610910', 1, '2023-01-06 14:01:47'),
(53, 'Hoodies XL', '610910', 1, '2023-01-06 14:02:07'),
(54, 'Hoodies 2XL', '610910', 1, '2023-01-06 14:02:23'),
(55, 'Hoodies 3XL', '610910', 1, '2023-01-06 14:02:43'),
(56, 'Hoodies 4XL', '610910', 1, '2023-01-06 14:02:57'),
(57, 'New Year Calendar', '998912', 1, '2023-01-06 14:56:10'),
(58, 'Table Calendar', '998912', 1, '2023-01-06 14:56:26'),
(59, 'Round Sticker ', '998912', 1, '2023-01-09 11:35:47'),
(60, 'Welcome Board', '39219025', 1, '2023-01-09 11:36:45'),
(61, 'Welcome Board Small Size', '39219025', 1, '2023-01-09 11:37:06'),
(62, 'Menu Card With Lamination', '998912', 1, '2023-01-18 11:15:54'),
(63, 'Design Charge', '998912', 1, '2023-01-18 11:16:07'),
(64, 'Flex For Internal Branding', '39219025', 1, '2023-01-19 11:57:05'),
(65, 'Hording', '39219025', 1, '2023-01-19 16:34:53'),
(66, 'One-way Vision With Installation', '39219025', 1, '2023-01-19 16:35:17'),
(67, 'Invitation Card With Envelop', '998912', 1, '2023-01-19 16:43:58'),
(68, 'Trademark Process Fees', '998912', 1, '2023-01-19 16:44:43'),
(69, 'Acrylic Logo', '39205111', 1, '2023-01-19 16:48:37'),
(70, 'Memento For Congratulation', '998912', 1, '2023-01-20 12:28:32'),
(71, 'Certificate With Wooden Frame', '998912', 1, '2023-01-20 12:28:46'),
(72, 'Exibition Display Stand Flex', '39219025', 1, '2023-01-25 15:51:25'),
(73, 'Subjective Copy', '998912', 1, '2023-01-26 16:39:25'),
(74, 'Warranty Tag', '998912', 1, '2023-01-27 18:56:53'),
(75, 'Canopy', '39219025', 1, '2023-01-27 18:57:09'),
(76, 'LED Signage Board', '94054090', 1, '2023-01-27 18:58:51'),
(77, 'Job Card Pad', '998912', 1, '2023-01-27 18:59:11'),
(78, 'GSB Board', '39219025', 1, '2023-02-02 13:39:18'),
(79, 'Flex With Iron Frame Of Result', '39219025', 1, '2023-02-04 10:47:08'),
(80, 'Iron Frame ', '998912', 1, '2023-02-07 12:18:53'),
(81, 'Footmate', '998912', 1, '2023-02-07 17:37:31'),
(82, 'Envelop', '998912', 1, '2023-02-07 17:37:47'),
(83, 'Req/Acc Book', '998912', 1, '2023-02-07 17:38:06'),
(84, 'Order Booking Form', '998912', 1, '2023-02-07 17:42:30'),
(85, 'Car Inquiry Form', '998912', 1, '2023-02-07 17:42:51'),
(86, 'Gate Pass', '998912', 1, '2023-02-07 17:43:03'),
(87, 'Documents File', '998912', 1, '2023-02-07 17:43:23'),
(88, 'New Car Gate Pass', '998912', 1, '2023-02-07 17:43:44'),
(89, 'Receipt Book', '998912', 1, '2023-02-07 17:44:03'),
(90, 'Quotation Book', '998912', 1, '2023-02-07 17:44:23'),
(91, 'Payments Voucher Pad', '998912', 1, '2023-02-07 17:45:14'),
(92, 'Receipt Book For Scrab', '998912', 1, '2023-02-07 17:45:40'),
(93, 'Documents File', '998912', 1, '2023-02-07 17:58:43'),
(94, 'Break -In-Survey Report', '998912', 1, '2023-02-07 18:12:52'),
(95, 'Payments Voucher', '998912', 1, '2023-02-07 18:13:10'),
(96, 'Receipt Book Showroom (Cash+Cheque)', '998912', 1, '2023-02-07 18:14:32'),
(97, 'Envelop Small Size', '998912', 1, '2023-02-07 18:14:54'),
(98, 'Register Road Test', '998912', 1, '2023-02-08 11:13:23'),
(99, 'Register Security Master', '998912', 1, '2023-02-08 11:13:41'),
(100, 'Customer Home Visit Form', '998912', 1, '2023-02-08 11:14:20'),
(101, 'Gate  Pass', '998912', 1, '2023-02-08 11:14:36'),
(102, 'Satisfaction Notes', '998912', 1, '2023-02-08 11:15:19'),
(103, 'Requestion Form ', '998912', 1, '2023-02-08 11:16:06'),
(104, 'Road Test Check Sheet', '998912', 1, '2023-02-08 11:16:23'),
(105, 'Accessories Slip', '998912', 1, '2023-02-08 11:16:51'),
(106, 'Body Shop File', '998912', 1, '2023-02-08 11:17:12'),
(107, 'Washing & Cleaning', '998912', 1, '2023-02-08 11:17:39'),
(108, 'Requestion Slip', '998912', 1, '2023-02-08 11:18:05'),
(109, 'SOP Forms', '998912', 1, '2023-02-08 11:18:36'),
(110, 'Books', '998912', 1, '2023-02-14 19:17:48'),
(111, 'NABH Signage ', '39219025', 1, '2023-02-15 15:37:31'),
(112, 'NABH Signage', '39219025', 1, '2023-02-15 15:38:30'),
(113, 'Foundation Certificate With Wooden Frame', '998912', 1, '2023-02-27 15:41:49'),
(114, 'Iron Stand With Flex & Installation Other School Activity ', '39219026', 1, '2023-02-28 17:21:15'),
(115, 'Iron Stand With Flex ', '39219026', 1, '2023-02-28 17:22:14'),
(116, 'ADMISSION RECORD FORM ', '998912', 1, '2023-03-08 17:38:29'),
(117, 'MTP FORM ', '998912', 1, '2023-03-08 17:38:49'),
(118, 'IPD FILE CHECKLIST FORM', '998912', 1, '2023-03-08 17:39:15'),
(119, 'BLOOD TRANSFUSION CONSENT FORM ', '998912', 1, '2023-03-08 17:39:39'),
(120, 'OT NOTES FORM', '998912', 1, '2023-03-08 17:39:52'),
(121, 'VITAL CHARTS 998912', '998912', 1, '2023-03-08 17:40:11'),
(122, 'NURSING NOTES FORM', '998912', 1, '2023-03-08 17:40:36'),
(123, 'INVESTING SHEETS FORM', '998912', 1, '2023-03-08 17:40:55'),
(124, 'ANESTHESIA FORM', '998912', 1, '2023-03-08 17:41:14'),
(125, 'OPERATION ADMISSION FORM', '998912', 1, '2023-03-08 17:41:29'),
(126, 'INITIAL ASSESMENT FORM', '998912', 1, '2023-03-08 17:41:44'),
(127, 'NURSING CARE FORM', '998912', 1, '2023-03-08 17:42:06'),
(128, 'BLOOD TR. FEEDBACK FORM', '998912', 1, '2023-03-08 17:42:30'),
(129, 'BLOOD TR. REACTION FORM', '998912', 1, '2023-03-08 17:42:58'),
(130, 'DEAD BODY HANDLING FORM', '998912', 1, '2023-03-08 17:43:11'),
(131, 'ADMISSION RECORD FORM ', '998912', 1, '2023-03-08 17:43:41'),
(132, 'NABH ANESTHESIA FORM ', '998912', 1, '2023-03-08 17:44:03'),
(133, 'ANESTHESIA SERVICE FORM ', '998912', 1, '2023-03-08 17:44:24'),
(134, 'PATIENT CHECKLIST OT FORM ', '998912', 1, '2023-03-08 17:44:48'),
(135, 'PRE OPERATIVE MEDICINE FORM ', '998912', 1, '2023-03-08 17:45:05'),
(136, 'SPECAIL PROCEDURE FORM ', '998912', 1, '2023-03-08 17:45:21'),
(137, 'VITAL MONITORING FORM', '998912', 1, '2023-03-08 17:45:38'),
(138, 'DAY CARE CONSENT FORM ', '998912', 1, '2023-03-08 17:45:53'),
(139, 'DAILY NURSING/HANDLING FORM ', '998912', 1, '2023-03-08 17:46:19'),
(140, 'Blood Tr. Monitoring Sheet', '998912', 1, '2023-03-08 17:46:59'),
(141, 'Nutrition Assessment Form  ', '998912', 1, '2023-03-08 17:47:51'),
(142, 'In Patient Charge Sheet ', '998912', 1, '2023-03-08 17:48:28'),
(143, 'Surgical Safety Form', '998912', 1, '2023-03-08 17:48:49'),
(144, 'Nursing Ass.& Re Form ', '998912', 1, '2023-03-08 17:49:14'),
(145, 'Progress Report', '998912', 1, '2023-03-08 17:49:46'),
(146, 'Postpartum Record Form', '998912', 1, '2023-03-08 17:50:22'),
(147, 'Nurses Record ', '998912', 1, '2023-03-08 17:50:33'),
(148, 'Restraint Of Patient Form  ', '998912', 1, '2023-03-08 17:50:58'),
(149, 'Laghu Prakriya Form', '998912', 1, '2023-03-08 17:51:11'),
(150, 'Abortion With Pills Form ', '998912', 1, '2023-03-08 17:51:35'),
(151, 'Discharge Summery Form', '998912', 1, '2023-03-08 17:51:49'),
(152, 'OPD Common File ', '998912', 1, '2023-03-08 17:52:44'),
(153, 'Backdrop Flex', '39219025', 1, '2023-03-16 11:53:20'),
(154, 'Facia Name Signage ', '39219025', 1, '2023-03-16 11:54:06'),
(155, 'Hoardings', '39219025', 1, '2023-03-16 11:54:35'),
(156, 'Selfi Point', '39219025', 1, '2023-03-16 11:54:58'),
(157, 'Podium Board', '39219025', 1, '2023-03-16 11:55:22'),
(158, 'Registration Counter Branding', '39219025', 1, '2023-03-16 11:56:05'),
(159, 'Facia Backdrops', '39219025', 1, '2023-03-16 11:56:37'),
(160, 'A.V System', '998912', 1, '2023-03-16 14:07:30'),
(161, 'Stage', '998912', 1, '2023-03-16 14:07:58'),
(162, 'Registration Desk', '998912', 1, '2023-03-16 14:08:42'),
(163, 'Stall', '998912', 1, '2023-03-16 14:08:53'),
(164, 'Welcome Carpet', '998912', 1, '2023-03-16 14:09:30'),
(165, 'Transportation ', '998912', 1, '2023-03-16 14:09:54'),
(166, 'Privilege Card', '998912', 1, '2023-03-20 16:48:34'),
(167, 'Hospital Forms', '998912', 1, '2023-03-20 16:49:02'),
(168, 'News Paper Print', '998912', 1, '2023-03-20 16:51:23'),
(169, 'Flex For Poles', '39219025', 1, '2023-03-20 17:55:28'),
(170, 'AC Diagnostic Check Sheet', '998912', 1, '2023-03-30 11:46:12'),
(171, 'Satisfaction Note', '998912', 1, '2023-03-30 11:46:47'),
(172, 'Engine Oil Consumption', '998912', 1, '2023-03-30 11:58:23'),
(173, 'Gumming Sheet ', '998912', 1, '2023-04-05 13:10:44'),
(174, 'Gumming Sheet For Camp', '998912', 1, '2023-04-05 13:11:01'),
(175, 'Other Camp Material', '998912', 1, '2023-04-05 13:11:21'),
(176, 'Lift Branding', '39219025', 1, '2023-04-08 15:13:10'),
(177, 'Pull/Push', '39219025', 1, '2023-04-08 15:17:21'),
(178, 'NICU Form', '998912', 1, '2023-04-11 11:26:45'),
(179, 'Iron Frame With Flex For Internal Banding', '39219025', 1, '2023-04-17 16:39:06'),
(180, 'Vinyl With Iron Frame', '39219025', 1, '2023-04-25 12:58:15'),
(181, 'Arrow Sign', '39219025', 1, '2023-04-25 17:22:25'),
(182, 'Flyers', '998912', 1, '2023-04-26 18:33:20'),
(183, 'Flyer Multicolor Both Side ', '998912', 1, '2023-04-26 18:34:03'),
(184, 'C.T. Cover File ', '998912', 1, '2023-05-05 16:24:22'),
(185, 'Crain Charges', '995479', 1, '2023-05-15 11:42:17'),
(186, 'Digital Visiting Cards', '998912', 1, '2023-05-17 16:42:19'),
(187, 'Visiting Cards Others Staff', '998912', 1, '2023-05-17 16:42:37'),
(188, 'Tenure Board', '998912', 1, '2023-05-22 16:39:31'),
(189, 'Don\'t Use Mobile Signage ', '39219025', 1, '2023-05-23 13:34:41'),
(190, 'I\'d Card Lanyard', '998912', 1, '2023-05-27 17:39:39'),
(191, 'GST Board', '39219025', 1, '2023-06-05 14:08:32'),
(192, 'Sun Pack Sheet 4 Color With Installation', '39219025', 1, '2023-06-08 14:13:53'),
(193, 'Sun Pack Sheet', '39219025', 1, '2023-06-14 13:59:36'),
(194, 'Tent Card', '998912', 1, '2023-06-22 11:32:26'),
(195, 'Text Message Charge', '995479', 1, '2023-06-22 12:29:12'),
(196, 'Wooden Trophy ', '830621', 1, '2023-07-03 23:32:32'),
(197, 'Flag', '998912', 1, '2023-07-03 23:36:35'),
(198, 'Customer Copy', '998912', 1, '2023-07-11 17:27:25'),
(199, 'Award', '998912', 1, '2023-07-12 16:18:48'),
(200, 'NABH Regular (Thin) Form Pad Single Side Print', '998912', 1, '2023-07-21 14:58:26'),
(201, 'NABH Thick Paper Form Pad Single Side ', '998912', 1, '2023-07-21 14:59:10'),
(202, 'NABH Regular (Thin) Form Pad Both Side Print', '998912', 1, '2023-07-21 15:02:14'),
(203, 'NABH Thick Paper Form Pad Both Side ', '998912', 1, '2023-07-21 15:02:56'),
(204, 'Birth Certificate', '998912', 1, '2023-07-21 15:25:13'),
(205, 'Prescription Pad B/S', '998912', 1, '2023-07-21 15:30:32'),
(206, 'Prescription Pad S/S', '998912', 1, '2023-07-21 15:30:50'),
(207, 'Reflector', '39219025', 1, '2023-07-21 15:31:30'),
(208, 'OPD Prescription Dr. Pad', '998912', 1, '2023-07-21 15:35:18'),
(209, 'Trophy Wooden ', '830621', 1, '2023-08-11 20:05:25'),
(210, 'Sound System', '	995479', 1, '2023-09-04 15:31:41'),
(211, 'Flower Decoration', '	995479', 1, '2023-09-04 15:31:52'),
(212, 'LED Wall ', '	995479', 1, '2023-09-04 15:32:02'),
(213, 'Tent Work', '	995479', 1, '2023-09-04 15:32:16'),
(214, 'Delivery Charge', '	995479', 1, '2023-10-06 11:31:43'),
(215, 'Paper Bag Print', '998912', 1, '2023-11-10 16:22:48'),
(216, 'Auto Rent', '	995479', 1, '2024-01-01 11:29:03'),
(217, 'Arch Gate', '	39219025', 1, '2024-01-01 11:29:34'),
(218, 'Arch Gates', '	39219025', 1, '2024-01-01 11:29:45'),
(219, 'Arch Gates Others', '	39219025', 1, '2024-01-01 11:30:16'),
(220, 'Paper Roll', '3920691', 1, '2024-01-10 19:14:15'),
(221, 'Flex For Camp', '39219025 ', 1, '2024-02-05 15:48:12'),
(222, 'Flex For Camp.', '39219025 ', 1, '2024-02-05 15:48:30'),
(223, 'Other Flex For Branding', '39219025 ', 1, '2024-02-05 15:49:42'),
(224, 'Social Media Management ', '998313', 1, '2024-03-31 20:19:08'),
(225, 'Google Work Space ', '998319', 1, '2024-04-21 16:32:50'),
(226, 'Stamp', '998912', 1, '2024-07-23 15:09:43'),
(227, 'Stamps', '998912', 1, '2024-07-23 15:09:55'),
(228, 'Power Supply', '9405', 1, '2025-01-13 11:33:06'),
(229, 'Power Supply', '9405', 1, '2025-01-13 11:33:26'),
(230, 'Medical Requestion Form', '998912', 1, '2025-04-29 13:11:59'),
(231, 'Others All Pad Mix', '998912', 1, '2025-04-29 13:12:20'),
(232, 'Others All Pad Mix', '998912', 1, '2025-04-29 13:12:20'),
(233, 'Form Pad All', '998912', 1, '2025-04-29 13:12:38'),
(234, 'Track Suit', '610910', 1, '2025-08-06 17:07:59'),
(235, 'Gumming Sticker', '998912', 1, '2026-02-22 20:35:14'),
(236, 'Hard Board Sheet A4', '998912', 1, '2026-02-22 20:35:40'),
(237, 'Hard Board Sheet A3', '998912', 1, '2026-02-22 20:36:20'),
(238, 'Hard Board Sheet Others', '39219025', 1, '2026-02-22 20:37:23'),
(239, 'Ad Management ', '998912', 1, '2026-05-05 17:00:04'),
(240, 'Google SEO', '998912', 1, '2026-05-05 17:00:17'),
(241, 'Web Development ', '998912', 1, '2026-05-05 17:00:46'),
(242, 'Web Management ', '998912', 1, '2026-05-05 17:01:13'),
(243, 'Google SEO/Facebook/Instagram/Etc', '998912', 1, '2026-05-05 17:01:50'),
(244, 'Gift Iteam', '998912', 1, '2026-05-26 16:12:23'),
(245, 'Sandwich Board', '39205111', 1, '2026-08-12 08:11:09'),
(246, 'Clip On', '39205111', 1, '2026-08-12 08:11:26'),
(247, 'Office Stationary ', '998912', 1, '2026-08-12 08:12:14'),
(248, 'Other Office Stationary', '998912', 1, '2026-08-12 08:12:33');

-- --------------------------------------------------------

--
-- Table structure for table `financial_years`
--

CREATE TABLE `financial_years` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_billing_no` int DEFAULT NULL,
  `current_billing_no` int DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `financial_years`
--

INSERT INTO `financial_years` (`id`, `title`, `start_date`, `end_date`, `start_billing_no`, `current_billing_no`, `status`, `created_at`) VALUES
(1, 'March 2026', '2026-03-31', '2027-03-01', 101, 1, 1, '2022-05-15 15:57:38');

-- --------------------------------------------------------

--
-- Table structure for table `patient_categories`
--

CREATE TABLE `patient_categories` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient_categories`
--

INSERT INTO `patient_categories` (`id`, `title`, `status`, `created_at`) VALUES
(1, 'Senior Citizen', 1, '2022-05-14 17:01:37');

-- --------------------------------------------------------

--
-- Table structure for table `quotations`
--

CREATE TABLE `quotations` (
  `id` int UNSIGNED NOT NULL,
  `quotation_no` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `quotation_date` date NOT NULL,
  `place_of_supply` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_address` text COLLATE utf8mb4_general_ci NOT NULL,
  `customer_contact` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_gstin` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `customer_state` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `feature_text` text COLLATE utf8mb4_general_ci,
  `total_quantity` decimal(14,4) NOT NULL DEFAULT '0.0000',
  `total_gst` decimal(14,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quotations`
--

INSERT INTO `quotations` (`id`, `quotation_no`, `quotation_date`, `place_of_supply`, `customer_name`, `customer_address`, `customer_contact`, `customer_gstin`, `customer_state`, `feature_text`, `total_quantity`, `total_gst`, `total_amount`, `created_at`, `updated_at`) VALUES
(1, 'Q-20260910-111726', '2026-09-10', 'jaipur', 'pawan', 'jaipur', '99999999', 'xxxxxxxx', 'raj', '1.  Samsung Company LED Light (Waterproof - 1yrs Warranty), 70mm raising, 3mm 3D \r\n     Acrelic With Letters Laser cut.\r\n2. Copper wiring 1mm,  3MM ACP Sheet  for Background Support. \r\n3. Waterproof Power Supply with1 yrs warranty. Iron Frame used For Holding The Board.\r\n4. No Guarranty/Warranty of Electric Short circuit.', 7.0000, 12780.18, 83781.18, '2026-09-10 11:19:39', '2026-09-10 12:55:30');

-- --------------------------------------------------------

--
-- Table structure for table `quotation_items`
--

CREATE TABLE `quotation_items` (
  `id` int UNSIGNED NOT NULL,
  `quotation_id` int UNSIGNED NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `size` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `total_sqf` decimal(14,4) DEFAULT NULL,
  `quantity` decimal(14,4) NOT NULL,
  `unit` enum('Sqf','Sqi','Pcs') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `price` decimal(14,2) NOT NULL,
  `gst` decimal(14,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(14,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quotation_items`
--

INSERT INTO `quotation_items` (`id`, `quotation_id`, `description`, `size`, `total_sqf`, `quantity`, `unit`, `price`, `gst`, `amount`) VALUES
(77, 1, '3d led board', '20x5', 100.0000, 1.0000, 'Sqf', 550.00, 9900.00, 64900.00),
(78, 1, 'Vinyl sunboard', '10x4', 40.0000, 5.0000, 'Sqf', 80.00, 2880.00, 18880.00),
(79, 1, 'paper Flyer', 'A4', NULL, 1.0000, 'Pcs', 1.00, 0.18, 1.18);

-- --------------------------------------------------------

--
-- Table structure for table `rates`
--

CREATE TABLE `rates` (
  `id` int NOT NULL,
  `rate_for` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `patient_category` int DEFAULT NULL,
  `blood_components` text COLLATE utf8mb4_general_ci,
  `blood_component_ids` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` tinyint DEFAULT '1',
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int NOT NULL,
  `user_id` int DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fname` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `party_gst_no` varchar(16) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `order_no` varchar(55) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `order_date` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `mobile` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `aadhar_no` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `sale_type` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cgst` varchar(16) COLLATE utf8mb4_general_ci DEFAULT '0',
  `sgst` varchar(16) COLLATE utf8mb4_general_ci DEFAULT '0',
  `igst` varchar(16) COLLATE utf8mb4_general_ci DEFAULT '0',
  `total_amount` decimal(10,2) DEFAULT '0.00',
  `financial_year_id` int DEFAULT NULL,
  `billing_no` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `component_ids` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `valid_certificate_no` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_mode` varchar(255) COLLATE utf8mb4_general_ci DEFAULT 'Credit',
  `note` text COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `user_id`, `name`, `fname`, `party_gst_no`, `order_no`, `order_date`, `address`, `mobile`, `aadhar_no`, `sale_type`, `cgst`, `sgst`, `igst`, `total_amount`, `financial_year_id`, `billing_no`, `component_ids`, `valid_certificate_no`, `payment_mode`, `note`, `created_at`) VALUES
(1, 1, 'AAKASH EDUCATIONAL SERVICES LTD.', '', ' 08AAGCA6863Q1ZW', '', '', 'Vaishali Nagar-Branch, Jaipur ', '8236546781', '', 'State Sale', '9', '9', '0', 236.00, 1, 'AD/26-27/0101', '10', '', 'Account', '', '2026-09-08 14:36:38');

-- --------------------------------------------------------

--
-- Table structure for table `registration_informations`
--

CREATE TABLE `registration_informations` (
  `id` int NOT NULL,
  `reg_id` int DEFAULT NULL,
  `component_id` int DEFAULT NULL,
  `component_quantity` int DEFAULT '1',
  `amount` decimal(10,2) DEFAULT '0.00',
  `total_amount` decimal(10,2) DEFAULT '0.00',
  `note` text COLLATE utf8mb4_general_ci,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registration_informations`
--

INSERT INTO `registration_informations` (`id`, `reg_id`, `component_id`, `component_quantity`, `amount`, `total_amount`, `note`, `created_at`) VALUES
(1, 1, 10, 10, 20.00, 200.00, 'Neet', '2026-09-08 14:36:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `type` varchar(32) COLLATE utf8mb4_general_ci DEFAULT 'User',
  `name` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_permissions` text COLLATE utf8mb4_general_ci,
  `mobile` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pass_string` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `type`, `name`, `email`, `password`, `user_permissions`, `mobile`, `pass_string`, `created_at`) VALUES
(1, 'Admin', 'Satyam Adex', 'satyam_adex_billing_admin@gmail.com', '4ca4bf137f85a4d3f4f1ee98629b5b11', '', '657547474', '123456', '2022-05-14 15:36:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blood_component_types`
--
ALTER TABLE `blood_component_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `financial_years`
--
ALTER TABLE `financial_years`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `patient_categories`
--
ALTER TABLE `patient_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `quotations`
--
ALTER TABLE `quotations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `quotations_quotation_no_unique` (`quotation_no`);

--
-- Indexes for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quotation_items_quotation_id_index` (`quotation_id`);

--
-- Indexes for table `rates`
--
ALTER TABLE `rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registration_informations`
--
ALTER TABLE `registration_informations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blood_component_types`
--
ALTER TABLE `blood_component_types`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=249;

--
-- AUTO_INCREMENT for table `financial_years`
--
ALTER TABLE `financial_years`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `patient_categories`
--
ALTER TABLE `patient_categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quotations`
--
ALTER TABLE `quotations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `quotation_items`
--
ALTER TABLE `quotation_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `rates`
--
ALTER TABLE `rates`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `registration_informations`
--
ALTER TABLE `registration_informations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `quotation_items`
--
ALTER TABLE `quotation_items`
  ADD CONSTRAINT `quotation_items_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
