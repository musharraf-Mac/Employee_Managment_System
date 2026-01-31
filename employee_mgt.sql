-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 31, 2026 at 11:37 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `employee_mgt`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_info`
--

CREATE TABLE `admin_info` (
  `E_id` varchar(100) NOT NULL,
  `First_Name` varchar(100) NOT NULL,
  `Last_Name` varchar(100) NOT NULL,
  `phone` int(15) NOT NULL,
  `email` varchar(100) NOT NULL,
  `Position` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_info`
--

INSERT INTO `admin_info` (`E_id`, `First_Name`, `Last_Name`, `phone`, `email`, `Position`, `password_hash`, `created_at`) VALUES
('1234', 'musha', 'sdfsdf', 2147483647, 'test2@outlook.com', 'HR', '$2y$10$0iDxr9qe5PM02YGChjuUq.uB6Nmd0eQBZaOMwkTRP/j7Ncwgj7bvG', '2026-01-20 17:47:35'),
('AB345', 'mush(App)', 'abd', 715451054, 'abdmusharraf2001@gmail.com', 'HR', '$2y$10$GOK2lA5Mb4oU9roeQ4ACBe5/r5o68HqH4SgKIup635EMn/Ro7NDWu', '2025-11-12 21:03:14'),
('E1234', 'Abd', 'Mush', 715451054, 'test@outlook.com', 'CEO', '$2y$10$3jOoZV9/hof5koKW1EKWQuVQTr5FnDO6NBKovF53S671DCQVsO8g.', '2025-12-01 11:39:22');

-- --------------------------------------------------------

--
-- Table structure for table `admin_info_temp`
--

CREATE TABLE `admin_info_temp` (
  `First_Name` varchar(100) NOT NULL,
  `Last_Name` varchar(100) DEFAULT NULL,
  `Phone` int(15) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `E_id` varchar(100) NOT NULL,
  `Position` varchar(100) NOT NULL,
  `admin_token` char(64) NOT NULL,
  `user_token` char(64) DEFAULT NULL,
  `expires_at` datetime NOT NULL,
  `status` enum('pending','approved','denied','') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_info_temp`
--

INSERT INTO `admin_info_temp` (`First_Name`, `Last_Name`, `Phone`, `Email`, `E_id`, `Position`, `admin_token`, `user_token`, `expires_at`, `status`, `created_at`) VALUES
('musha', 'sdfsdf', 2147483647, 'test2@outlook.com', '1234', 'HR', '4e0253734322c7c35cb0b4100d564e2218363f8da1f0c387134461e2614cc23c', '23c36fe7892472d50acba419f07949dac8019795027c323fb657579a1637bb0e', '2026-01-22 23:17:02', 'approved', '2026-01-20 17:46:40'),
('mush(den)', 'abd', 715451054, 'abdmusharraf2001@gmail.com', 'AB346', 'HR', 'ee7b166dfd35c9f1f3da6f0ba12621ef89d691f3bcaf0eed3de6cbd0807f1c7b', NULL, '2025-11-14 22:16:46', 'denied', '2025-11-12 21:16:46'),
('Abd', 'Mush', 715451054, 'test@outlook.com', 'E1234', 'CEO', 'fd4043eb268fee7bc4231a968fba61139e09cffdbb9b006e4e8136493277109f', 'c73812cc8253aaf56ef300d4f8ec74a359fca07a543204d35d40ce9125d46691', '2025-12-03 17:08:11', 'approved', '2025-12-01 11:37:46');

-- --------------------------------------------------------

--
-- Table structure for table `employee_details`
--

CREATE TABLE `employee_details` (
  `E_id` varchar(100) NOT NULL,
  `First_Name` varchar(100) NOT NULL,
  `Last_Name` varchar(100) DEFAULT NULL,
  `Department` varchar(100) NOT NULL,
  `Phone` int(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `Modified_by` varchar(100) DEFAULT NULL,
  `E_Leave` int(11) NOT NULL,
  `Working_hour` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_details`
--

INSERT INTO `employee_details` (`E_id`, `First_Name`, `Last_Name`, `Department`, `Phone`, `email`, `Modified_by`, `E_Leave`, `Working_hour`) VALUES
('E0001', 'Musharraf', 'Abd', 'IT', 77444444, 'test3@outlook.com', '1234', 0, 0),
('E003', 'Thiyageraja', 'Devanivethitha', 'IT', 77123456, 'nivedha@gamil.com', '1234', 0, 0),
('HB3456', 'MSH', 'ABD', 'sasf', 755455, '', NULL, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_info`
--
ALTER TABLE `admin_info`
  ADD PRIMARY KEY (`E_id`);

--
-- Indexes for table `admin_info_temp`
--
ALTER TABLE `admin_info_temp`
  ADD PRIMARY KEY (`E_id`);

--
-- Indexes for table `employee_details`
--
ALTER TABLE `employee_details`
  ADD PRIMARY KEY (`E_id`),
  ADD KEY `admin_info` (`Modified_by`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `employee_details`
--
ALTER TABLE `employee_details`
  ADD CONSTRAINT `admin_info` FOREIGN KEY (`Modified_by`) REFERENCES `admin_info` (`E_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
