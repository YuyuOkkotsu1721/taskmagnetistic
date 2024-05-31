-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2024 at 01:19 AM
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
-- Database: `taskmagnet`
--

-- --------------------------------------------------------

--
-- Table structure for table `buzzchats`
--

CREATE TABLE `buzzchats` (
  `BuzzChatID` int(11) NOT NULL,
  `SubtaskID` varchar(255) NOT NULL,
  `UserID` varchar(255) NOT NULL,
  `BuzzTimeStamp` datetime NOT NULL,
  `BuzzMessage` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buzzchats`
--

INSERT INTO `buzzchats` (`BuzzChatID`, `SubtaskID`, `UserID`, `BuzzTimeStamp`, `BuzzMessage`) VALUES
(1, '65f95fe5e8efcv1t8s14', '65f95fe5e8efc', '2024-05-16 21:30:47', 'Hello'),
(2, '65f95fe5e8efcv1t8s14', '65f95fe5e8efc', '2024-05-16 21:32:44', 'Wassuo');

-- --------------------------------------------------------

--
-- Table structure for table `buzznotify`
--

CREATE TABLE `buzznotify` (
  `BuzzNotifyID` int(11) NOT NULL,
  `SubtaskID` varchar(255) DEFAULT NULL,
  `BuzzNotifiedStatus` varchar(255) DEFAULT NULL,
  `TaskID` varchar(255) DEFAULT NULL,
  `UserID` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buzznotify`
--

INSERT INTO `buzznotify` (`BuzzNotifyID`, `SubtaskID`, `BuzzNotifiedStatus`, `TaskID`, `UserID`) VALUES
(1, '65f95fe5e8efcv1t8s14', 'false', '65f95fe5e8efcv1t8', '65f95fe5e8efc');

-- --------------------------------------------------------

--
-- Table structure for table `collaborators`
--

CREATE TABLE `collaborators` (
  `CollaboratorIDkey` int(11) NOT NULL,
  `CollaboratorID` varchar(255) NOT NULL,
  `CollaboratorMaker` varchar(255) NOT NULL,
  `CollaboratorMember` varchar(255) NOT NULL,
  `SharedDateTime` datetime NOT NULL,
  `TaskID` varchar(255) NOT NULL,
  `CollaboratorManager` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `collaborators`
--

INSERT INTO `collaborators` (`CollaboratorIDkey`, `CollaboratorID`, `CollaboratorMaker`, `CollaboratorMember`, `SharedDateTime`, `TaskID`, `CollaboratorManager`) VALUES
(1, '65f95fe5e8efcv1t8clb', 'mtsu3300', 'mtsu3301', '2024-05-14 18:05:34', '65f95fe5e8efcv1t8', '');

-- --------------------------------------------------------

--
-- Table structure for table `editlogs`
--

CREATE TABLE `editlogs` (
  `EditLogID` varchar(255) NOT NULL,
  `SubtaskID` varchar(255) DEFAULT NULL,
  `UserID` varchar(255) DEFAULT NULL,
  `OriginalData` varchar(255) DEFAULT NULL,
  `UpdatedData` varchar(255) DEFAULT NULL,
  `EditTimestamp` datetime DEFAULT NULL,
  `EditFieldType` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `editlogs`
--

INSERT INTO `editlogs` (`EditLogID`, `SubtaskID`, `UserID`, `OriginalData`, `UpdatedData`, `EditTimestamp`, `EditFieldType`) VALUES
('65f95fe5e8efcv1t10s1ed0', '65f95fe5e8efcv1t10s1', '65f95fe5e8efc', NULL, NULL, '2024-05-15 01:16:33', NULL),
('65f95fe5e8efcv1t13s1ed0', '65f95fe5e8efcv1t13s1', '65f95fe5e8efc', NULL, NULL, '2024-05-18 11:31:11', NULL),
('65f95fe5e8efcv1t13s1ed1', '65f95fe5e8efcv1t13s1', '65f95fe5e8efc', 'Pending', 'Ongoing', '2024-05-24 14:33:55', 'SubtaskStatus'),
('65f95fe5e8efcv1t13s1ed2', '65f95fe5e8efcv1t13s1', '65f95fe5e8efc', 'Ongoing', 'Done', '2024-05-24 14:33:59', 'SubtaskStatus'),
('65f95fe5e8efcv1t16s1ed0', '65f95fe5e8efcv1t16s1', '65f95fe5e8efc', NULL, NULL, '2024-05-21 19:06:08', NULL),
('65f95fe5e8efcv1t16s2ed0', '65f95fe5e8efcv1t16s2', '65f95fe5e8efc', NULL, NULL, '2024-05-21 19:06:18', NULL),
('65f95fe5e8efcv1t16s2ed1', '65f95fe5e8efcv1t16s2', '65f95fe5e8efc', '', '1', '2024-05-21 23:31:36', 'SubtaskMarkNumber'),
('65f95fe5e8efcv1t16s2ed2', '65f95fe5e8efcv1t16s2', '65f95fe5e8efc', 'Pending', 'Ongoing', '2024-05-24 14:34:13', 'SubtaskStatus'),
('65f95fe5e8efcv1t16s2ed3', '65f95fe5e8efcv1t16s2', '65f95fe5e8efc', 'Ongoing', 'Done', '2024-05-24 14:34:16', 'SubtaskStatus'),
('65f95fe5e8efcv1t16s3ed0', '65f95fe5e8efcv1t16s3', '65f95fe5e8efc', NULL, NULL, '2024-05-21 19:07:27', NULL),
('65f95fe5e8efcv1t16s3ed1', '65f95fe5e8efcv1t16s3', '65f95fe5e8efc', 'Webflow animations', 'Framer Motion and Lottie Files', '2024-05-22 00:27:25', 'SubtaskTitle'),
('65f95fe5e8efcv1t16s3ed2', '65f95fe5e8efcv1t16s3', '65f95fe5e8efc', '', '4', '2024-05-22 00:27:31', 'SubtaskMarkNumber'),
('65f95fe5e8efcv1t16s3ed3', '65f95fe5e8efcv1t16s3', '65f95fe5e8efc', '4', '5', '2024-05-22 00:27:36', 'SubtaskMarkNumber'),
('65f95fe5e8efcv1t16s3ed4', '65f95fe5e8efcv1t16s3', '65f95fe5e8efc', '5', '4', '2024-05-22 12:28:55', 'SubtaskMarkNumber'),
('65f95fe5e8efcv1t16s4ed0', '65f95fe5e8efcv1t16s4', '65f95fe5e8efc', NULL, NULL, '2024-05-21 20:25:14', NULL),
('65f95fe5e8efcv1t16s4ed1', '65f95fe5e8efcv1t16s4', '65f95fe5e8efc', 'Sign Up', 'Sign Up and Login', '2024-05-21 20:26:42', 'SubtaskTitle'),
('65f95fe5e8efcv1t16s5ed0', '65f95fe5e8efcv1t16s5', '65f95fe5e8efc', NULL, NULL, '2024-05-21 20:25:23', NULL),
('65f95fe5e8efcv1t16s5ed1', '65f95fe5e8efcv1t16s5', '65f95fe5e8efc', '', '2', '2024-05-21 23:31:52', 'SubtaskMarkNumber'),
('65f95fe5e8efcv1t16s6ed0', '65f95fe5e8efcv1t16s6', '65f95fe5e8efc', NULL, NULL, '2024-05-21 20:36:07', NULL),
('65f95fe5e8efcv1t16s7ed0', '65f95fe5e8efcv1t16s7', '65f95fe5e8efc', NULL, NULL, '2024-05-21 20:36:22', NULL),
('65f95fe5e8efcv1t16s7ed1', '65f95fe5e8efcv1t16s7', '65f95fe5e8efc', '', '2', '2024-05-21 23:31:44', 'SubtaskMarkNumber'),
('65f95fe5e8efcv1t16s7ed2', '65f95fe5e8efcv1t16s7', '65f95fe5e8efc', '2', '3', '2024-05-21 23:32:03', 'SubtaskMarkNumber'),
('65f95fe5e8efcv1t16s7ed3', '65f95fe5e8efcv1t16s7', '65f95fe5e8efc', '3', '5', '2024-05-22 12:29:02', 'SubtaskMarkNumber'),
('65f95fe5e8efcv1t16s8ed0', '65f95fe5e8efcv1t16s8', '65f95fe5e8efc', NULL, NULL, '2024-05-21 20:36:42', NULL),
('65f95fe5e8efcv1t16s8ed1', '65f95fe5e8efcv1t16s8', '65f95fe5e8efc', '', '4', '2024-05-21 23:36:44', 'SubtaskMarkNumber'),
('65f95fe5e8efcv1t16s8ed2', '65f95fe5e8efcv1t16s8', '65f95fe5e8efc', '4', '6', '2024-05-22 12:29:09', 'SubtaskMarkNumber'),
('65f95fe5e8efcv1t16s9ed0', '65f95fe5e8efcv1t16s9', '65f95fe5e8efc', NULL, NULL, '2024-05-22 12:28:38', NULL),
('65f95fe5e8efcv1t16s9ed1', '65f95fe5e8efcv1t16s9', '65f95fe5e8efc', '', '3', '2024-05-22 12:28:50', 'SubtaskMarkNumber'),
('65f95fe5e8efcv1t17s1ed0', '65f95fe5e8efcv1t17s1', '65f95fe5e8efc', NULL, NULL, '2024-05-22 21:21:53', NULL),
('65f95fe5e8efcv1t17s1ed1', '65f95fe5e8efcv1t17s1', '65f95fe5e8efc', 'N/A', 'Explain this more in step by step ...\\r\\n\\r\\n', '2024-05-28 23:09:27', 'SubtaskDescription'),
('65f95fe5e8efcv1t17s2ed0', '65f95fe5e8efcv1t17s2', '65f95fe5e8efc', NULL, NULL, '2024-05-28 23:05:01', NULL),
('65f95fe5e8efcv1t17s2ed1', '65f95fe5e8efcv1t17s2', '65f95fe5e8efc', 'N/A', 'Explain this more in step by step ...\\r\\n\\r\\n', '2024-05-28 23:09:24', 'SubtaskDescription'),
('65f95fe5e8efcv1t17s3ed0', '65f95fe5e8efcv1t17s3', '65f95fe5e8efc', NULL, NULL, '2024-05-28 23:09:19', NULL),
('65f95fe5e8efcv1t17s3ed1', '65f95fe5e8efcv1t17s3', '65f95fe5e8efc', 'News', 'Table of Content like fandom wikia', '2024-05-28 23:09:41', 'SubtaskTitle'),
('65f95fe5e8efcv1t7s1ed0', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', NULL, NULL, '2024-05-14 16:26:35', NULL),
('65f95fe5e8efcv1t7s1ed1', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', 'N/A', 'Apply the Calculate night time and night price\\r\\nfrom the end time and start time', '2024-05-14 17:50:55', 'SubtaskDescription'),
('65f95fe5e8efcv1t7s1ed10', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', 'Apply the Calculate night time and night price\r\nfrom the end time and start time', 'Apply the Calculate night time and night price\\r\\nfrom the end time and start time', '2024-05-15 00:54:22', 'SubtaskDescription'),
('65f95fe5e8efcv1t7s1ed11', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', '#8100c2', '#5c008a', '2024-05-15 00:54:22', 'SubtaskBackgroundColor'),
('65f95fe5e8efcv1t7s1ed12', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', 'Apply the Calculate night time and night price\r\nfrom the end time and start time', 'Apply the Calculate night time and night price\\r\\nfrom the end time and start time', '2024-05-15 10:44:45', 'SubtaskDescription'),
('65f95fe5e8efcv1t7s1ed13', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', '1', '2', '2024-05-15 10:44:45', 'SubtaskMarkNumber'),
('65f95fe5e8efcv1t7s1ed14', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', 'Apply the Calculate night time and night price\r\nfrom the end time and start time', 'Apply the Calculate night time and night price\\r\\nfrom the end time and start time', '2024-05-15 19:08:14', 'SubtaskDescription'),
('65f95fe5e8efcv1t7s1ed15', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', 'Pending', 'Ongoing', '2024-05-15 19:08:14', 'SubtaskStatus'),
('65f95fe5e8efcv1t7s1ed16', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', 'Apply the Calculate night time and night price\r\nfrom the end time and start time', 'Apply the Calculate night time and night price\\r\\nfrom the end time and start time', '2024-05-15 19:08:36', 'SubtaskDescription'),
('65f95fe5e8efcv1t7s1ed17', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', 'Ongoing', 'Done', '2024-05-15 19:08:36', 'SubtaskStatus'),
('65f95fe5e8efcv1t7s1ed2', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', 'Apply the Calculate night time and night price\r\nfrom the end time and start time', 'Apply the Calculate night time and night price\\r\\nfrom the end time and start time', '2024-05-14 18:20:56', 'SubtaskDescription'),
('65f95fe5e8efcv1t7s1ed3', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', '', '1', '2024-05-14 18:20:56', 'SubtaskMarkNumber'),
('65f95fe5e8efcv1t7s1ed4', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', 'Apply the Calculate night time and night price\r\nfrom the end time and start time', 'Apply the Calculate night time and night price\\r\\nfrom the end time and start time', '2024-05-15 00:54:05', 'SubtaskDescription'),
('65f95fe5e8efcv1t7s1ed5', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', '#2d3748', '#c495db', '2024-05-15 00:54:05', 'SubtaskBackgroundColor'),
('65f95fe5e8efcv1t7s1ed6', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', 'Apply the Calculate night time and night price\r\nfrom the end time and start time', 'Apply the Calculate night time and night price\\r\\nfrom the end time and start time', '2024-05-15 00:54:10', 'SubtaskDescription'),
('65f95fe5e8efcv1t7s1ed7', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', '#c495db', '#6e119c', '2024-05-15 00:54:10', 'SubtaskBackgroundColor'),
('65f95fe5e8efcv1t7s1ed8', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', 'Apply the Calculate night time and night price\r\nfrom the end time and start time', 'Apply the Calculate night time and night price\\r\\nfrom the end time and start time', '2024-05-15 00:54:15', 'SubtaskDescription'),
('65f95fe5e8efcv1t7s1ed9', '65f95fe5e8efcv1t7s1', '65f95fe5e8efc', '#6e119c', '#8100c2', '2024-05-15 00:54:15', 'SubtaskBackgroundColor'),
('65f95fe5e8efcv1t7s2ed0', '65f95fe5e8efcv1t7s2', '65f95fe5e8efc', NULL, NULL, '2024-05-14 18:20:44', NULL),
('65f95fe5e8efcv1t7s2ed1', '65f95fe5e8efcv1t7s2', '65f95fe5e8efc', 'Multiple Time Picker', 'Multiple Time Picker plus removal', '2024-05-14 18:21:48', 'SubtaskTitle'),
('65f95fe5e8efcv1t7s2ed2', '65f95fe5e8efcv1t7s2', '65f95fe5e8efc', '#2d3748', '#5c008a', '2024-05-15 00:54:54', 'SubtaskBackgroundColor'),
('65f95fe5e8efcv1t7s2ed3', '65f95fe5e8efcv1t7s2', '65f95fe5e8efc', 'Pending', 'Ongoing', '2024-05-15 19:08:31', 'SubtaskStatus'),
('65f95fe5e8efcv1t7s2ed4', '65f95fe5e8efcv1t7s2', '65f95fe5e8efc', 'Ongoing', 'Done', '2024-05-15 19:08:43', 'SubtaskStatus'),
('65f95fe5e8efcv1t7s3ed0', '65f95fe5e8efcv1t7s3', '65f95fe5e8efc', NULL, NULL, '2024-05-14 18:20:50', NULL),
('65f95fe5e8efcv1t7s3ed1', '65f95fe5e8efcv1t7s3', '65f95fe5e8efc', 'Multiple Date Picker', 'Multiple Date Picker plus removal', '2024-05-14 18:21:52', 'SubtaskTitle'),
('65f95fe5e8efcv1t7s3ed2', '65f95fe5e8efcv1t7s3', '65f95fe5e8efc', '#2d3748', '#5c008a', '2024-05-15 00:54:49', 'SubtaskBackgroundColor'),
('65f95fe5e8efcv1t7s3ed3', '65f95fe5e8efcv1t7s3', '65f95fe5e8efc', 'Pending', 'Ongoing', '2024-05-15 19:11:39', 'SubtaskStatus'),
('65f95fe5e8efcv1t7s3ed4', '65f95fe5e8efcv1t7s3', '65f95fe5e8efc', 'Ongoing', 'Done', '2024-05-15 19:12:03', 'SubtaskStatus'),
('65f95fe5e8efcv1t7s4ed0', '65f95fe5e8efcv1t7s4', '65f95fe5e8efc', NULL, NULL, '2024-05-15 10:44:27', NULL),
('65f95fe5e8efcv1t7s4ed1', '65f95fe5e8efcv1t7s4', '65f95fe5e8efc', '#2d3748', '#5c008a', '2024-05-15 10:44:39', 'SubtaskBackgroundColor'),
('65f95fe5e8efcv1t7s4ed2', '65f95fe5e8efcv1t7s4', '65f95fe5e8efc', '', '1', '2024-05-15 10:44:50', 'SubtaskMarkNumber'),
('65f95fe5e8efcv1t7s4ed3', '65f95fe5e8efcv1t7s4', '65f95fe5e8efc', 'Pending', 'Ongoing', '2024-05-15 11:56:38', 'SubtaskStatus'),
('65f95fe5e8efcv1t7s4ed4', '65f95fe5e8efcv1t7s4', '65f95fe5e8efc', 'Ongoing', 'Done', '2024-05-15 11:56:48', 'SubtaskStatus'),
('65f95fe5e8efcv1t8s10ed0', '65f95fe5e8efcv1t8s10', '65f95fe5e8efc', NULL, NULL, '2024-05-14 16:52:19', NULL),
('65f95fe5e8efcv1t8s11ed0', '65f95fe5e8efcv1t8s11', '65f95fe5e8efc', NULL, NULL, '2024-05-14 16:58:37', NULL),
('65f95fe5e8efcv1t8s12ed0', '65f95fe5e8efcv1t8s12', '65f95fe5e8efc', NULL, NULL, '2024-05-14 17:30:33', NULL),
('65f95fe5e8efcv1t8s13ed0', '65f95fe5e8efcv1t8s13', '65f95fe5e8efc', NULL, NULL, '2024-05-14 17:34:08', NULL),
('65f95fe5e8efcv1t8s14ed0', '65f95fe5e8efcv1t8s14', '65f95fe5e8efc', NULL, NULL, '2024-05-15 01:10:24', NULL),
('65f95fe5e8efcv1t8s15ed0', '65f95fe5e8efcv1t8s15', '65f95fe5e8efc', NULL, NULL, '2024-05-21 19:01:05', NULL),
('65f95fe5e8efcv1t8s1ed0', '65f95fe5e8efcv1t8s1', '65f95fe5e8efc', NULL, NULL, '2024-05-14 11:43:57', NULL),
('65f95fe5e8efcv1t8s1ed1', '65f95fe5e8efcv1t8s1', '65f95fe5e8efc', 'Back reloaded', 'Back reloaded by redirecting to it forwardly by saving up windows of previous page.', '2024-05-14 12:02:46', 'SubtaskTitle'),
('65f95fe5e8efcv1t8s2ed0', '65f95fe5e8efcv1t8s2', '65f95fe5e8efc', NULL, NULL, '2024-05-14 12:03:08', NULL),
('65f95fe5e8efcv1t8s3ed0', '65f95fe5e8efcv1t8s3', '65f95fe5e8efc', NULL, NULL, '2024-05-14 12:03:20', NULL),
('65f95fe5e8efcv1t8s4ed0', '65f95fe5e8efcv1t8s4', '65f95fe5e8efc', NULL, NULL, '2024-05-14 12:05:05', NULL),
('65f95fe5e8efcv1t8s5ed0', '65f95fe5e8efcv1t8s5', '65f95fe5e8efc', NULL, NULL, '2024-05-14 12:05:22', NULL),
('65f95fe5e8efcv1t8s6ed0', '65f95fe5e8efcv1t8s6', '65f95fe5e8efc', NULL, NULL, '2024-05-14 12:05:31', NULL),
('65f95fe5e8efcv1t8s7ed0', '65f95fe5e8efcv1t8s7', '65f95fe5e8efc', NULL, NULL, '2024-05-14 12:05:40', NULL),
('65f95fe5e8efcv1t8s8ed0', '65f95fe5e8efcv1t8s8', '65f95fe5e8efc', NULL, NULL, '2024-05-14 12:15:52', NULL),
('65f95fe5e8efcv1t8s9ed0', '65f95fe5e8efcv1t8s9', '65f95fe5e8efc', NULL, NULL, '2024-05-14 12:16:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `subtaskaccess`
--

CREATE TABLE `subtaskaccess` (
  `SubtaskID` varchar(255) DEFAULT NULL,
  `TaskID` varchar(255) DEFAULT NULL,
  `AssignedTo` varchar(255) DEFAULT NULL,
  `AccessOption` varchar(255) DEFAULT NULL,
  `SubtaskAccessID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subtasks`
--

CREATE TABLE `subtasks` (
  `SubtaskID` varchar(255) NOT NULL,
  `SubtaskTitle` varchar(255) NOT NULL,
  `SubtaskDescription` longtext NOT NULL,
  `SubtaskDuration` int(11) NOT NULL,
  `SubtaskDifficulty` varchar(255) NOT NULL,
  `SubtaskStatus` varchar(255) NOT NULL,
  `TaskID` varchar(255) NOT NULL,
  `SubtaskCreationDate` datetime NOT NULL,
  `SubtaskBackgroundColor` varchar(255) NOT NULL,
  `SubtaskTextColor` varchar(255) NOT NULL,
  `SubtaskMarkNumber` varchar(255) NOT NULL,
  `SubtaskImage` varchar(255) NOT NULL,
  `SubtaskStartTime` datetime NOT NULL,
  `SubtaskEndTime` datetime NOT NULL,
  `SubtaskPausedDuration` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subtasks`
--

INSERT INTO `subtasks` (`SubtaskID`, `SubtaskTitle`, `SubtaskDescription`, `SubtaskDuration`, `SubtaskDifficulty`, `SubtaskStatus`, `TaskID`, `SubtaskCreationDate`, `SubtaskBackgroundColor`, `SubtaskTextColor`, `SubtaskMarkNumber`, `SubtaskImage`, `SubtaskStartTime`, `SubtaskEndTime`, `SubtaskPausedDuration`) VALUES
('65f95fe5e8efcv1t10s1', 'Hottest Hand Indicator', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t10', '2024-05-15 01:16:33', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t13s1', 'On the go chair, foldable', 'N/A', 30, 'Easy', 'Done', '65f95fe5e8efcv1t13', '2024-05-18 11:31:11', '#2d3748', '#ffffff', '', '', '2024-05-24 14:33:55', '2024-05-24 14:33:59', NULL),
('65f95fe5e8efcv1t16s1', 'Rest API', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t16', '2024-05-21 19:06:08', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t16s2', 'Tailwind CSS and flowbite', 'N/A', 30, 'Easy', 'Done', '65f95fe5e8efcv1t16', '2024-05-21 19:06:18', '#2d3748', '#ffffff', '', '', '2024-05-24 14:34:13', '2024-05-24 14:34:16', NULL),
('65f95fe5e8efcv1t16s3', 'Framer Motion and Lottie Files', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t16', '2024-05-21 19:07:27', '#2d3748', '#ffffff', '4', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t16s4', 'Sign Up and Login', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t16', '2024-05-21 20:25:14', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t16s5', 'Navigation', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t16', '2024-05-21 20:25:23', '#2d3748', '#ffffff', '2', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t16s6', 'Firebase and Express Js', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t16', '2024-05-21 20:36:07', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t16s7', 'Landing Page', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t16', '2024-05-21 20:36:22', '#2d3748', '#ffffff', '5', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t16s8', 'Node mailer Contact Us email and send email through event trigger', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t16', '2024-05-21 20:36:42', '#2d3748', '#ffffff', '6', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t16s9', 'Feather icon and Font Awesome Icon', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t16', '2024-05-22 12:28:38', '#2d3748', '#ffffff', '3', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t17s1', 'News Content', 'Explain this more in step by step ...\r\n\r\n', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t17', '2024-05-22 21:21:53', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t17s2', 'Drag and Drop', 'Explain this more in step by step ...\r\n\r\n', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t17', '2024-05-28 23:05:01', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t17s3', 'Table of Content like fandom wikia', 'Explain this more in step by step ...', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t17', '2024-05-28 23:09:19', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t7s1', 'Revise the booking history to render the changes in edit modal for night time immediately', 'Apply the Calculate night time and night price\r\nfrom the end time and start time', 30, 'Easy', 'Done', '65f95fe5e8efcv1t7', '2024-05-14 16:26:35', '#5c008a', '#ffffff', '', '', '2024-05-15 19:08:14', '2024-05-15 19:08:36', NULL),
('65f95fe5e8efcv1t7s2', 'Multiple Time Picker plus removal', 'N/A', 30, 'Easy', 'Done', '65f95fe5e8efcv1t7', '2024-05-14 18:20:44', '#5c008a', '#ffffff', '', '', '2024-05-15 19:08:31', '2024-05-15 19:08:43', NULL),
('65f95fe5e8efcv1t7s3', 'Multiple Date Picker plus removal', 'N/A', 30, 'Easy', 'Done', '65f95fe5e8efcv1t7', '2024-05-14 18:20:50', '#5c008a', '#ffffff', '', '', '2024-05-15 19:11:39', '2024-05-15 19:12:03', NULL),
('65f95fe5e8efcv1t7s4', 'Hide Night when its empty or 0', 'N/A', 30, 'Easy', 'Done', '65f95fe5e8efcv1t7', '2024-05-15 10:44:27', '#5c008a', '#ffffff', '', '', '2024-05-15 11:56:38', '2024-05-15 11:56:48', NULL),
('65f95fe5e8efcv1t8s1', 'Back reloaded by redirecting to it forwardly by saving up windows of previous page.', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t8', '2024-05-14 11:43:57', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t8s10', 'Motto Listing', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t8', '2024-05-14 16:52:19', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t8s11', 'Get Ongoing Paused', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t8', '2024-05-14 16:58:37', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t8s12', 'Autocomplete', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t8', '2024-05-14 17:30:33', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t8s13', 'Ask for Help Socials Postings', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t8', '2024-05-14 17:34:08', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t8s14', 'Export feature', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t8', '2024-05-15 01:10:24', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t8s15', 'Get the task deadline to be optional', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t8', '2024-05-21 19:01:05', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t8s2', 'World Rankings', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t8', '2024-05-14 12:03:08', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t8s3', 'Text Content with Image', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t8', '2024-05-14 12:03:20', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t8s4', 'Category Tags', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t8', '2024-05-14 12:05:05', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t8s5', 'Date Range', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t8', '2024-05-14 12:05:22', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t8s6', 'Search Filter', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t8', '2024-05-14 12:05:31', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t8s7', 'AI auto generated tasks', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t8', '2024-05-14 12:05:40', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t8s8', 'Timer Fix', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t8', '2024-05-14 12:15:52', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL),
('65f95fe5e8efcv1t8s9', 'Smartphone timer adjustment and signup fix of image upload', 'N/A', 30, 'Easy', 'Pending', '65f95fe5e8efcv1t8', '2024-05-14 12:16:08', '#2d3748', '#ffffff', '', '', '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `TaskID` varchar(255) NOT NULL,
  `VentureID` varchar(255) NOT NULL,
  `TaskTitle` varchar(255) NOT NULL,
  `TaskDescription` varchar(255) NOT NULL,
  `TaskDueDateTime` datetime NOT NULL,
  `TaskPriority` varchar(255) NOT NULL,
  `TaskCreationDate` datetime NOT NULL,
  `TaskBackgroundColor` varchar(255) NOT NULL,
  `TaskTextColor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`TaskID`, `VentureID`, `TaskTitle`, `TaskDescription`, `TaskDueDateTime`, `TaskPriority`, `TaskCreationDate`, `TaskBackgroundColor`, `TaskTextColor`) VALUES
('65f95fe5e8efcv1t10', '65f95fe5e8efcv1', 'Sports betting tool', 'Ideas about a sports betting tool', '2024-05-22 01:10:00', 'Low', '2024-05-15 01:10:04', '#a24411', '#ffffff'),
('65f95fe5e8efcv1t11', '65f95fe5e8efcv1', 'Civio', 'Civio', '2024-05-21 00:17:00', 'Low', '2024-05-18 00:17:09', '#629ba3', '#ffffff'),
('65f95fe5e8efcv1t12', '65f95fe5e8efcv1', 'Video Automation System', 'Video Automation System', '2024-05-28 00:17:00', 'Low', '2024-05-18 00:17:32', '#836ebf', '#ffffff'),
('65f95fe5e8efcv1t13', '65f95fe5e8efcv1', 'Prototypes', 'Prototypes', '2024-05-21 00:17:00', 'Low', '2024-05-18 00:17:46', '#0a5700', '#ffffff'),
('65f95fe5e8efcv1t14', '65f95fe5e8efcv1', 'Jot Down AI', 'Jot Down AI', '2024-05-28 12:11:00', 'Low', '2024-05-18 12:11:12', '#a31446', '#ffffff'),
('65f95fe5e8efcv1t15', '65f95fe5e8efcv1', 'Alternative School', 'Alternative School', '2024-05-28 12:11:00', 'Low', '2024-05-18 12:11:34', '#a36814', '#ffffff'),
('65f95fe5e8efcv1t16', '65f95fe5e8efcv1', 'React Js', 'React Js', '2024-05-28 23:00:00', 'Low', '2024-05-21 19:00:48', '#0b466a', '#ffffff'),
('65f95fe5e8efcv1t17', '65f95fe5e8efcv1', 'Page Builder', 'Page Builder', '2024-05-29 21:21:00', 'Low', '2024-05-22 21:21:38', '#2e607f', '#ffffff'),
('65f95fe5e8efcv1t18', '65f95fe5e8efcv1', 'Movie API', 'Movie API', '2024-05-28 22:57:00', 'Low', '2024-05-22 22:57:08', '#17a314', '#ffffff'),
('65f95fe5e8efcv1t19', '65f95fe5e8efcv1', 'Crowdfunding', 'Crowdfunding', '2024-06-05 15:52:00', 'Low', '2024-05-24 15:52:57', '#8c8218', '#ffffff'),
('65f95fe5e8efcv1t20', '65f95fe5e8efcv1', 'Dating', 'Dating', '2024-06-05 12:30:00', 'Low', '2024-05-26 12:30:23', '#6e111f', '#ffffff'),
('65f95fe5e8efcv1t7', '65f95fe5e8efcv1', 'Nanny Nows', 'For OJTs', '2024-05-21 23:59:00', 'High', '2024-05-14 11:42:23', '#431b50', '#ffffff'),
('65f95fe5e8efcv1t8', '65f95fe5e8efcv1', 'Backlogs, Bugs, and incrementsz', 'Backlogs, Bugs, and incrementsz', '2024-05-30 23:42:00', 'High', '2024-05-14 11:42:56', '#081f72', '#ffffff'),
('65f95fe5e8efcv1t9', '65f95fe5e8efcv1', 'Find Work', 'Find Work', '2024-05-24 18:00:00', 'Medium', '2024-05-14 16:48:18', '#1e7b4b', '#ffffff');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `UserID` varchar(50) NOT NULL,
  `UserFirstName` varchar(50) NOT NULL,
  `UserLastName` varchar(50) NOT NULL,
  `UserPhoneNumber` varchar(15) NOT NULL,
  `UserEmail` varchar(100) NOT NULL,
  `UserUsername` varchar(100) NOT NULL,
  `UserPassword` varchar(100) NOT NULL,
  `UserProfileImage` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`UserID`, `UserFirstName`, `UserLastName`, `UserPhoneNumber`, `UserEmail`, `UserUsername`, `UserPassword`, `UserProfileImage`) VALUES
('65f95fe5e8efc', 'Matthew', 'Abenion', '09773017012', 'matthewabenion22@gmail.com', 'mtsu3300', '$2y$10$le2N.sMFUvdn1oU8MVPTxuct59q3YCkT84Qw6P/Ij4ryeg0X56QQ.', 'profilepics/712715d9e2a1a6a426442aa2f7bbb639.png'),
('65f960a403fcf', 'Matthew', 'Charles', '09773017013', 'charlesmatthew@gmail.com', 'mtsu3301', '$2y$10$sDFkOmj10Ca9MJMkp0cmG.QAAKzuanYUQxbek2d5jGR4QRKNATYpG', 'profilepics/b57010f4be1785ae3b3d2c8776b5e811.png'),
('65f9681e7b750', 'Ivan', 'Bernardez', '123456', 'ivanbernardez@gmail.com', 'ivan888', '$2y$10$YNdblFUlo9fEbCvDO5aLz.cYSxu8fRBT7tlR4bHjLY73TZEI96c7q', ''),
('661df7bc03803', 'Roy', 'Crucillo ', '09773017084', 'roycrucillo@gmail.com', 'roy11 ', '$2y$10$KICR4xC8NcteA0gh4MHM6u0GIqalU63PO6Hy74RY1GaLN2kPB3.Mm', ''),
('66276ac12f0f4', 'Matthew', 'Abenion', '09773017012', 'matthewabenion@gmail.com', 'mtsu3304', '$2y$10$MJBA1lwqXjcVLYRasimvWemO3CpHBsoi0IFJxQ90bp3xVAkGI2ODu', 'profilepics/mmfp3.png'),
('66276bbd8fa06', 'Matthew', 'Abenion', '09773017012', 'matthewabenion@gmail.com', 'mtsu3305', '$2y$10$9HKDw6ysSUDLKnOgbgMUAOKT9rZpnDery9Q/BiAB9izKfO3GMXB2O', 'profilepics/cxvaseescx.png'),
('66276c3883fbf', 'Matthew', 'Abenion', '09773017012', 'matthewabenion@gmail.com', 'mtsu3306', '$2y$10$XjiZWry2pWQOKdfMBvppcOdRR.62i1jdT4ynA31o5OSguO/Lxneqe', 'profilepics/cczxcxase.png'),
('66276c8eeccea', 'Matthew', 'Abenion', '09773017012', 'matthewabenion@gmail.com', 'mtsu3308', '$2y$10$viGVfwKDq2UeWait2RHWl.MicVBIP5dcn/LXZGEx540pVtSuyukBm', 'profilepics/ppcase2.png'),
('6627944643eaa', 'Matthew', 'Abenion', '0977016251', 'matthewabenion21@gmail.com', 'mtsu3311', '$2y$10$EODUBUkqZqGzWoN6Q3wI9eoPRFnj/9cukrPpq3DhD7B16ipPUVc5i', 'profilepics/pfpm1.png'),
('6627c37e7f0a6', 'Matthew ', 'Abenion', '09773014284', 'matthewabenion23@gmail.com', 'mtsu21', '$2y$10$iYw4SfB02TMz5BVZ1PKHCexpHsVB4q7SBep/ZyYstkzeTtJcgwEba', 'profilepics/dp.png'),
('6638623a76735', 'Gojo ', 'Satoru', '09773652142', 'gojosatoru@gmail.com', 'gojo11', '$2y$10$gKawDD3rph5JR61jk.fmKebM3ZmUCx7pUC1XW3tewI7BWOzXKVdXi', 'profilepics/white.png'),
('664047ed4ec0c', 'asf', 'asf', '09773021412', 'asf@gmail.com', 'asf01', '$2y$10$V3nycRHdVs0NX8vhDYRjkuoCWimooEQ3pGrOpfH6vfQ0OrRxzsCjq', 'profilepics/checked.png'),
('664048af475f7', 'sdg', 'sdg', '0977625012', 'sdg@gmail.com', 'sdg01', '$2y$10$6g9p45mJ.0zYRbZdDvmXCeP6kipSYjCtZUJ/zPJAjAOrRbb2xYZ7m', 'profilepics/152996410_767493523879980_9164930743147010783_n (1).jpg'),
('66404a9625627', 'renzie', 'renzie', '09776251412', 'renzie@gmail.com', 'renzie01', '$2y$10$p4RHHBJsxEDGTuM0qHQ4cOewTwQ41oNkMCKuNEztpj0laCDsVHnoy', 'profilepics/received_1383345648466107.jpeg'),
('66404d4df129f', 'renzwie', 'renzwie', '09778450612', 'renzwie@gmail.com', 'renzwie01', '$2y$10$/XrmUsd5HtIpwoHWV1SpNuVshdLWWjRxJrlMROs9NcvSbNIuj59E.', 'profilepics/358341497_992599025317511_8915012578718601331_n.jpg'),
('66404ead130df', 'renziee', 'renziee', '09773017542', 'renziee@gmail.com', 'renziee01', '$2y$10$a.uaJrFQ.9CZiTk75zarSeL0c6mdKrL4PT1NQgKvx1l4yjwDBQUE6', 'profilepics/358341497_992599025317511_8915012578718601331_n.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `venture`
--

CREATE TABLE `venture` (
  `VentureID` varchar(255) NOT NULL,
  `VentureTitle` varchar(255) NOT NULL,
  `UserID` varchar(255) NOT NULL,
  `VentureCreationDate` datetime NOT NULL,
  `VentureDescription` varchar(255) NOT NULL,
  `VentureBackgroundColor` varchar(255) NOT NULL,
  `VentureTextColor` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `venture`
--

INSERT INTO `venture` (`VentureID`, `VentureTitle`, `UserID`, `VentureCreationDate`, `VentureDescription`, `VentureBackgroundColor`, `VentureTextColor`) VALUES
('65f95fe5e8efcv1', 'May 2024', '65f95fe5e8efc', '2024-05-13 19:45:19', 'May 2024', '#0000ff', '#ffffff');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buzzchats`
--
ALTER TABLE `buzzchats`
  ADD PRIMARY KEY (`BuzzChatID`);

--
-- Indexes for table `buzznotify`
--
ALTER TABLE `buzznotify`
  ADD PRIMARY KEY (`BuzzNotifyID`);

--
-- Indexes for table `collaborators`
--
ALTER TABLE `collaborators`
  ADD PRIMARY KEY (`CollaboratorIDkey`);

--
-- Indexes for table `editlogs`
--
ALTER TABLE `editlogs`
  ADD PRIMARY KEY (`EditLogID`);

--
-- Indexes for table `subtaskaccess`
--
ALTER TABLE `subtaskaccess`
  ADD PRIMARY KEY (`SubtaskAccessID`);

--
-- Indexes for table `subtasks`
--
ALTER TABLE `subtasks`
  ADD PRIMARY KEY (`SubtaskID`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`TaskID`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`UserID`);

--
-- Indexes for table `venture`
--
ALTER TABLE `venture`
  ADD PRIMARY KEY (`VentureID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `buzzchats`
--
ALTER TABLE `buzzchats`
  MODIFY `BuzzChatID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `buzznotify`
--
ALTER TABLE `buzznotify`
  MODIFY `BuzzNotifyID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `collaborators`
--
ALTER TABLE `collaborators`
  MODIFY `CollaboratorIDkey` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `subtaskaccess`
--
ALTER TABLE `subtaskaccess`
  MODIFY `SubtaskAccessID` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
