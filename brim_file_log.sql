-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 16, 2023 at 09:18 PM
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
-- Database: `brim`
--

-- --------------------------------------------------------

--
-- Table structure for table `brim_file_log`
--

CREATE TABLE `brim_file_log` (
  `id` int(11) NOT NULL,
  `fileName` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `fileSize` int(11) NOT NULL,
  `fileTimestamp` datetime NOT NULL,
  `encryptionStatus` enum('encrypt','decrypt') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `fileLocation` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brim_file_log`
--

INSERT INTO `brim_file_log` (`id`, `fileName`, `fileSize`, `fileTimestamp`, `encryptionStatus`, `fileLocation`, `timestamp`) VALUES
(1, 'testfile', 12, '2023-01-15 04:46:39', 'encrypt', 1, '2023-01-15 03:47:19'),
(2, 'File1.20230116', 6350, '2023-01-15 19:06:51', 'encrypt', 1, '2023-01-16 04:03:36'),
(3, 'File2.20230116', 6350, '2023-01-15 19:06:51', 'encrypt', 1, '2023-01-16 04:03:36'),
(4, 'File3.20230116', 6350, '2023-01-15 19:06:51', 'encrypt', 1, '2023-01-16 04:03:36'),
(5, 'File4.20230116', 6350, '2023-01-15 19:06:51', 'encrypt', 1, '2023-01-16 04:03:36'),
(6, 'File5.20230116', 6350, '2023-01-15 19:06:51', 'encrypt', 1, '2023-01-16 04:03:36'),
(7, 'File6.20230116', 6350, '2023-01-15 19:06:51', 'encrypt', 1, '2023-01-16 04:03:36'),
(8, 'File7.20230116', 6350, '2023-01-15 19:06:51', 'encrypt', 1, '2023-01-16 04:03:36'),
(9, 'File8.20230116', 6350, '2023-01-15 19:06:51', 'encrypt', 1, '2023-01-16 04:03:36'),
(10, 'File9.20230116', 6350, '2023-01-15 19:06:51', 'encrypt', 1, '2023-01-16 04:03:36'),
(11, 'File10.20230116', 6350, '2023-01-15 19:06:51', 'encrypt', 1, '2023-01-16 04:03:36'),
(12, 'File11.20230116', 6350, '2023-01-15 19:06:51', 'encrypt', 1, '2023-01-16 04:03:36'),
(13, 'File12.20230116', 6350, '2023-01-15 19:06:51', 'encrypt', 1, '2023-01-16 04:03:36'),
(14, 'File1.20230116', 0, '1970-01-01 01:00:00', 'encrypt', 1, '2023-01-16 04:31:33'),
(15, 'File2.20230116', 0, '1970-01-01 01:00:00', 'encrypt', 1, '2023-01-16 04:31:33'),
(16, 'File3.20230116', 0, '1970-01-01 01:00:00', 'encrypt', 1, '2023-01-16 04:31:33'),
(17, 'File4.20230116', 0, '1970-01-01 01:00:00', 'encrypt', 1, '2023-01-16 04:31:33'),
(18, 'File5.20230116', 0, '1970-01-01 01:00:00', 'encrypt', 1, '2023-01-16 04:31:33'),
(19, 'File6.20230116', 0, '1970-01-01 01:00:00', 'encrypt', 1, '2023-01-16 04:31:33'),
(20, 'File7.20230116', 0, '1970-01-01 01:00:00', 'encrypt', 1, '2023-01-16 04:31:33'),
(21, 'File8.20230116', 0, '1970-01-01 01:00:00', 'encrypt', 1, '2023-01-16 04:31:33'),
(22, 'File9.20230116', 0, '1970-01-01 01:00:00', 'encrypt', 1, '2023-01-16 04:31:33'),
(23, 'File10.20230116', 0, '1970-01-01 01:00:00', 'encrypt', 1, '2023-01-16 04:31:33'),
(24, 'File11.20230116', 0, '1970-01-01 01:00:00', 'encrypt', 1, '2023-01-16 04:31:33'),
(25, 'File12.20230116', 0, '1970-01-01 01:00:00', 'encrypt', 1, '2023-01-16 04:31:33'),
(26, 'File1.20230116', 4, '2023-01-16 06:02:09', 'encrypt', 1, '2023-01-16 05:05:46'),
(27, 'File2.20230116', 4, '2023-01-16 06:04:04', 'encrypt', 1, '2023-01-16 05:05:46'),
(28, 'File3.20230116', 4, '2023-01-16 06:04:10', 'encrypt', 1, '2023-01-16 05:05:46'),
(29, 'File4.20230116', 4, '2023-01-16 06:04:18', 'encrypt', 1, '2023-01-16 05:05:46'),
(30, 'File5.20230116', 4, '2023-01-16 06:04:24', 'encrypt', 1, '2023-01-16 05:05:46'),
(31, 'File6.20230116', 4, '2023-01-16 06:04:31', 'encrypt', 1, '2023-01-16 05:05:46'),
(32, 'File7.20230116', 4, '2023-01-16 06:04:37', 'encrypt', 1, '2023-01-16 05:05:46'),
(33, 'File8.20230116', 4, '2023-01-16 06:04:44', 'encrypt', 1, '2023-01-16 05:05:46'),
(34, 'File9.20230116', 4, '2023-01-16 06:04:51', 'encrypt', 1, '2023-01-16 05:05:46'),
(35, 'File10.20230116', 4, '2023-01-16 06:05:09', 'encrypt', 1, '2023-01-16 05:05:46'),
(36, 'File11.20230116', 4, '2023-01-16 06:05:17', 'encrypt', 1, '2023-01-16 05:05:46'),
(37, 'File12.20230116', 4, '2023-01-16 06:05:23', 'encrypt', 1, '2023-01-16 05:05:46'),
(38, 'FileA.20230116', 28, '2023-01-16 09:31:10', 'decrypt', 1, '2023-01-16 08:31:11'),
(39, 'FileB.20230116', 4, '2023-01-16 09:27:20', 'decrypt', 1, '2023-01-16 08:31:11'),
(40, 'FileC.20230116', 4, '2023-01-16 09:27:20', 'decrypt', 1, '2023-01-16 08:31:11'),
(41, 'FileD.20230116', 4, '2023-01-16 09:27:20', 'decrypt', 1, '2023-01-16 08:31:11'),
(42, 'FileE.20230116', 4, '2023-01-16 09:27:20', 'decrypt', 1, '2023-01-16 08:31:11'),
(43, 'FileF.20230116', 4, '2023-01-16 09:27:20', 'decrypt', 1, '2023-01-16 08:31:11'),
(44, 'FileA.20230116', 28, '2023-01-16 20:02:52', 'decrypt', 2, '2023-01-16 19:02:52'),
(45, 'FileB.20230116', 4, '2023-01-16 20:02:51', 'decrypt', 2, '2023-01-16 19:02:52'),
(46, 'FileC.20230116', 4, '2023-01-16 20:02:51', 'decrypt', 2, '2023-01-16 19:02:52'),
(47, 'FileD.20230116', 4, '2023-01-16 20:02:51', 'decrypt', 2, '2023-01-16 19:02:52'),
(48, 'FileE.20230116', 4, '2023-01-16 20:02:51', 'decrypt', 2, '2023-01-16 19:02:52'),
(49, 'FileF.20230116', 4, '2023-01-16 20:02:51', 'decrypt', 2, '2023-01-16 19:02:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brim_file_log`
--
ALTER TABLE `brim_file_log`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brim_file_log`
--
ALTER TABLE `brim_file_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
