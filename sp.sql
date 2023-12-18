-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2023 at 04:38 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sp`
--

-- --------------------------------------------------------

--
-- Table structure for table `communications`
--

CREATE TABLE `communications` (
  `id` int(11) NOT NULL,
  `sender_id` int(10) UNSIGNED NOT NULL,
  `recipient_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(64) NOT NULL,
  `message` text NOT NULL,
  `send_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `attachment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `communications`
--

INSERT INTO `communications` (`id`, `sender_id`, `recipient_id`, `title`, `message`, `send_at`, `attachment`) VALUES
(13, 1, 1, 'asdfasdf', 'a a a a a a', '2023-11-20 03:02:45', 'uploads/655b20b52da01_osi-layer.png'),
(18, 1, 1, 'test', 'test test test test test test test test test', '2023-11-21 10:00:12', 'uploads/655cd40c7d771_Before 1.png'),
(19, 1, 1, 'asdf', 'a a a a a a', '2023-12-09 06:15:59', 'uploads/65745a7f27061_osi-layer.png'),
(20, 1, 1, 'test trim', 'df    dfdf         df          df          df', '2023-12-17 21:37:13', 'uploads/657fbe6971f91_After 1.png');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `session_id` varchar(255) NOT NULL,
  `user_id` int(11) DEFAULT -1,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `data` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`session_id`, `user_id`, `timestamp`, `data`) VALUES
('vg3o777ierqa4ok5u2r5ufp3uq', 1, '2023-12-18 03:37:54', 'login|b:1;username|s:5:\"test1\";role|s:4:\"user\";user_id|i:1;email|s:14:\"test1@test.com\";last_access|i:1702870674;user_agent|s:80:\"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:120.0) Gecko/20100101 Firefox/120.0\";ip_address|s:9:\"127.0.0.1\";');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(16) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `failed_attempts` int(11) DEFAULT 0,
  `last_failed_login` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `role`, `password`, `created_at`, `updated_at`, `failed_attempts`, `last_failed_login`) VALUES
(1, 'test1', 'test1', 'test1@test.com', 'user', '$2y$10$Ev3jPLij.MPwnZJSw79O2OPeTemoj8HUfM1mWvfBTdK7wTFNxkL52', '2019-04-30 07:13:37', '2023-12-18 03:36:34', 0, '2023-12-17 21:36:06'),
(5, 'admin', 'admin', 'admin@gmail.com', 'admin', '$2y$10$C5wX9Tff3Pb9sKylW58S4.IWCCv3ZCqKM1Kry3CnlQg9KYtQ25tX2', '2019-04-30 07:13:37', '2023-11-20 15:38:24', 0, '2023-12-18 02:12:08'),
(6, 'asdf', 'asdf1234', 'asdf@gmail.com', 'user', '$2y$10$./cqq8RulF3qFNpDt1S.MuFPqnmwc/HEWvmf0GltyNfa8fnt0xzHi', '2023-12-09 12:08:18', '2023-12-09 12:08:18', 0, '2023-12-18 02:12:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `communications`
--
ALTER TABLE `communications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `foreign_constraint_recipient` (`recipient_id`),
  ADD KEY `foreign_constraint_sender` (`sender_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `communications`
--
ALTER TABLE `communications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `communications`
--
ALTER TABLE `communications`
  ADD CONSTRAINT `foreign_constraint_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
