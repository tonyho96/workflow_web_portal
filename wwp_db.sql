-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1:3306
-- Thời gian đã tạo: Th10 06, 2018 lúc 05:23 AM
-- Phiên bản máy phục vụ: 5.7.19
-- Phiên bản PHP: 7.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `wwp_db`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `files`
--

DROP TABLE IF EXISTS `files`;
CREATE TABLE IF NOT EXISTS `files` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `form_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `forms`
--

DROP TABLE IF EXISTS `forms`;
CREATE TABLE IF NOT EXISTS `forms` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `raised_by_user_id` int(11) NOT NULL,
  `contact_no` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `change_type` int(11) NOT NULL,
  `change_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `proposed_change_owner_user_id` int(11) NOT NULL,
  `date_raised` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `business_priority` int(11) NOT NULL,
  `change_start_date` date NOT NULL,
  `change_start_time` time NOT NULL,
  `change_end_date` date NOT NULL,
  `change_end_time` time NOT NULL,
  `proposed_change` text COLLATE utf8_unicode_ci NOT NULL,
  `reason` text COLLATE utf8_unicode_ci NOT NULL,
  `risk_assessment` text COLLATE utf8_unicode_ci NOT NULL,
  `rollback_strategy` text COLLATE utf8_unicode_ci NOT NULL,
  `test_plan` text COLLATE utf8_unicode_ci NOT NULL,
  `authorisation_signature` text COLLATE utf8_unicode_ci NOT NULL,
  `authorisation_signature_date` text COLLATE utf8_unicode_ci NOT NULL,
  `completion_notes` text COLLATE utf8_unicode_ci,
  `completion_signature` text COLLATE utf8_unicode_ci,
  `completion_signature_date` text COLLATE utf8_unicode_ci,
  `author_user_id` int(11) NOT NULL,
  `reference` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `complete_status` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `planned_date` date NOT NULL,
  `approved_rejected_reason` text COLLATE utf8_unicode_ci,
  `approved_rejected_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approved_rejected_by` int(11) DEFAULT NULL,
  `is_closed` int(11) NOT NULL DEFAULT '0',
  `complete_date` date DEFAULT NULL,
  `smart_hub_impact` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `forms`
--

INSERT INTO `forms` (`id`, `raised_by_user_id`, `contact_no`, `change_type`, `change_title`, `proposed_change_owner_user_id`, `date_raised`, `business_priority`, `change_start_date`, `change_start_time`, `change_end_date`, `change_end_time`, `proposed_change`, `reason`, `risk_assessment`, `rollback_strategy`, `test_plan`, `authorisation_signature`, `authorisation_signature_date`, `completion_notes`, `completion_signature`, `completion_signature_date`, `author_user_id`, `reference`, `status`, `complete_status`, `planned_date`, `approved_rejected_reason`, `approved_rejected_date`, `created_at`, `updated_at`, `approved_rejected_by`, `is_closed`, `complete_date`, `smart_hub_impact`) VALUES
(6, 2, '700000', 2, 'aaa', 12, '2018-07-29', 5, '2018-07-29', '19:25:00', '2018-07-31', '20:25:00', 'dfadfad', 'dfasfasf', 'dsfadsfdas', 'adfadsf', 'dsfasdfadsf', 'aaa', '2018-07-29', NULL, NULL, NULL, 2, 'RFC0303', '2', '3', '2018-08-01', 'aaa', '2018-08-07', '2018-07-29 05:27:29', '2018-08-07 05:10:25', 1, 0, '2018-07-31', 0),
(4, 1, '700000', 2, 'aaa', 12, '2018-07-29', 5, '2018-07-29', '19:25:00', '2018-07-31', '20:25:00', 'dfadfad', 'dfasfasf', 'dsfadsfdas', 'adfadsf', 'dsfasdfadsf', 'aaa', '2018-07-29', NULL, NULL, NULL, 2, 'RFC0301', '2', '3', '2018-08-01', NULL, NULL, '2018-07-29 05:27:08', '2018-08-07 05:09:20', NULL, 0, '2018-07-31', 0),
(5, 2, '700000', 2, 'aaa', 2, '2018-07-29', 5, '2018-07-29', '19:25:00', '2018-07-31', '20:25:00', 'dfadfad', 'dfasfasf', 'dsfadsfdas', 'adfadsf', 'dsfasdfadsf', 'aaa', '2018-07-29', NULL, NULL, NULL, 2, 'RFC0302', '2', '2', '2018-08-01', 'aaa', '2018-08-07', '2018-07-29 05:27:18', '2018-07-29 05:27:18', 1, 0, NULL, 0),
(7, 1, '70000', 2, 'aaa', 2, '2018-07-29', 5, '2018-07-29', '22:25:00', '2018-07-30', '22:25:00', 'aaa', 'aaa', 'aaa', 'aaa', 'aaa', 'aaa', '2018-07-29', NULL, NULL, NULL, 1, 'RFC0304', '2', '2', '2018-08-01', NULL, NULL, '2018-07-29 05:28:23', '2018-07-29 05:28:23', NULL, 0, NULL, 0),
(8, 1, '70000', 2, 'aaa', 2, '2018-07-29', 5, '2018-07-29', '22:25:00', '2018-07-30', '22:25:00', 'aaa', 'aaa', 'aaa', 'aaa', 'aaa', 'aaa', '2018-07-29', NULL, NULL, NULL, 1, 'RFC0305', '2', '2', '2018-08-01', NULL, NULL, '2018-07-29 05:28:53', '2018-07-29 05:28:53', NULL, 0, NULL, 0),
(9, 1, '70', 2, 'hello', 1, '2018-08-16', 5, '2018-08-16', '19:15:00', '2018-08-17', '19:15:00', 'hello', 'hello', 'hello', 'hello', 'hello', 'hello', '2018-08-16', NULL, NULL, NULL, 1, 'RFC0306', '2', '2', '2018-08-18', NULL, NULL, '2018-08-16 05:19:11', '2018-08-16 05:19:11', NULL, 0, NULL, 1);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(34, '2014_10_12_000000_create_users_table', 2),
(35, '2014_10_12_100000_create_password_resets_table', 2),
(36, '2018_01_07_082334_CreateFormTable', 2),
(37, '2018_01_18_000000_create_users_table2', 2),
(38, '2018_02_26_152135_AddIsClosedField', 2),
(39, '2018_03_15_125222_CreateFilesTable', 2),
(40, '2018_03_15_134511_AddIsApprovedField', 2),
(24, '2018_05_03_131957_alter_users_table_to_add_team_field', 1),
(41, '2018_05_03_122243_AddFormCompleteDateField', 2),
(42, '2018_05_04_105413_AddUserTeamIdField', 2),
(43, '2018_08_15_155146_add_field_smart_hub_impact_forms_table', 3);

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `role` int(11) NOT NULL,
  `remember_token` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_working` tinyint(1) NOT NULL DEFAULT '0',
  `is_approved` int(11) NOT NULL DEFAULT '0',
  `team` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `remember_token`, `created_at`, `updated_at`, `is_working`, `is_approved`, `team`) VALUES
(1, 'admin', 'causer@mailinator.com', '$2y$10$sA1ZgTtoutLx85xM.eQ/LOZL/uqLH6eg8FvbrF.xteTyg./v4wYpy', 1, 'tVRSrSM42l4rphdD0lOpINy44O1NxMfrNzcCqoNpk2YhDvCmCNMho2rWemgc', NULL, '2018-07-29 05:26:03', 1, 1, 1),
(2, 'aaa', 'tonyho@mailinator.com', '$2y$10$FXBZtbfo1pSt8qGP2ZB1mO3GC2dp673sukZakOy.aoT9Tgn8Yx3.m', 2, 'Xlx9EtQiZd6lvjHPCFhAU6RhANfk0TucE2dbpiHUHAak2qxUwQkEqEFSyq5N', '2018-05-29 20:07:09', '2018-07-29 05:20:39', 0, 1, 1),
(12, 'bbb', 'tonyho1@mailinator.com', '$2y$10$.9Ypa5yUACzFLgWY0jlileqTZkIcNTudnx3iEHlJ4oB1DYG1ESdza', 2, '4Ox1ekNxSjKQsrN1x76bOSH0VXMcYFYybvmhLHFntXEBlQY85AAn8rt8KuOA', '2018-07-29 05:25:30', '2018-07-29 05:25:36', 0, 1, 1);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
