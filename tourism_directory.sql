-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 14, 2026 at 06:45 AM
-- Server version: 10.11.16-MariaDB
-- PHP Version: 8.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tourism_directory`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2026-11-07 14:35:05');

-- --------------------------------------------------------

--
-- Table structure for table `tourism_spots`
--

CREATE TABLE `tourism_spots` (
  `spot_id` int(11) NOT NULL,
  `spot_name` varchar(150) NOT NULL,
  `location` varchar(255) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `entrance_fee` decimal(10,2) DEFAULT 0.00,
  `image` varchar(255) DEFAULT 'default.jpg',
  `opening_hours` varchar(100) DEFAULT '8:00 AM - 5:00 PM',
  `has_indoor_nav` tinyint(1) DEFAULT 0,
  `indoor_nav_details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tourism_spots`
--

INSERT INTO `tourism_spots` (`spot_id`, `spot_name`, `location`, `category`, `description`, `entrance_fee`, `image`, `opening_hours`, `has_indoor_nav`, `indoor_nav_details`, `created_at`) VALUES
(1, 'Tarangban Falls', 'Calbayog City, Samar', 'Waterfalls', 'Cascading waters flowing down rock formations deep in the lush tropical forest.', 50.00, 'tarangban.jpg', '6:00 AM - 5:00 PM', 0, NULL, '2026-11-07 14:15:46'),
(2, 'Bangon-Bugtong Falls', 'Calbayog City, Samar', 'Waterfalls', 'A circular multi-tiered waterfall featuring a large deep pool ideal for swimming and diving.', 50.00, 'bangon.jpg', '6:00 AM - 5:00 PM', 0, NULL, '2026-11-07 14:15:46'),
(3, 'Malajog Beach', 'Calbayog City, Samar', 'Beaches', 'Famous black sand beach offering scenic views, beach cottages, and zip-line rides.', 20.00, 'malajog.jpg', '24 Hours', 0, NULL, '2026-11-07 14:15:46'),
(4, 'Biri Rock Formations', 'Biri, Northern Samar', 'Rock Formations', 'Monolithic rock sculptures carved over time by ocean waves and tectonic movements.', 100.00, 'biri.jpg', '5:00 AM - 6:00 PM', 0, NULL, '2026-11-07 14:15:46'),
(5, 'Sohoton Natural Bridge & Cave', 'Basey, Samar', 'Caves & Parks', 'National park featuring stone bridges, subterranean rivers, and sprawling cave chambers.', 150.00, 'sohoton.jpg', '8:00 AM - 4:00 PM', 1, 'Chamber 1: Entrance Hall -> Chamber 2: Cathedral Room -> Chamber 3: Subterranean River Dock', '2026-11-07 14:15:46'),
(6, 'Capul Island Lighthouse', 'Brgy. San Luis, Capul, Northern Samar', 'Historical Sites', 'The Capul Island Lighthouse (Faro de Isla Capul), situated on Titoog Point in San Luis, Northern Samar, is a historic 19th-century beacon guarding the western entrance of the San Bernardino Strait.', 0.00, '1794062724_images.jpg', '', 0, '', '2026-11-07 14:45:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `tourism_spots`
--
ALTER TABLE `tourism_spots`
  ADD PRIMARY KEY (`spot_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tourism_spots`
--
ALTER TABLE `tourism_spots`
  MODIFY `spot_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
