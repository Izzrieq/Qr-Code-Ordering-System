-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 13, 2025 at 04:45 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sup_meletup_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `username`, `password`) VALUES
(1, 'admin', '827ccb0eea8a706c4c34a16891f84e7b');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `menu_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`menu_id`, `name`, `type`, `description`, `price`, `image`) VALUES
(1, 'Sup Kambing Meletup', 'Signature', 'Our Signature Sup ', 7.00, '1760274782_sup_kambing.jpg'),
(2, 'Sirap Limau', 'Beverage', 'Refreshing Drink', 3.00, '1760339665_siraplimau.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `menu_variants`
--

CREATE TABLE `menu_variants` (
  `variant_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price_diff` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_variants`
--

INSERT INTO `menu_variants` (`variant_id`, `menu_id`, `name`, `price_diff`) VALUES
(1, 2, 'Hot', 0.00),
(2, 2, 'Ice', 0.50);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `table_no` varchar(50) NOT NULL,
  `menu_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `order_time` datetime DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tables`
--

CREATE TABLE `tables` (
  `table_id` int(11) NOT NULL,
  `table_number` varchar(20) NOT NULL,
  `qr_image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tables`
--

INSERT INTO `tables` (`table_id`, `table_number`, `qr_image`) VALUES
(1, 'Table 1', 'table_Table 1_1760282050.png'),
(8, 'Table 2', 'table_Table 2_1760340541.png');

-- --------------------------------------------------------

--
-- Table structure for table `table_record`
--

CREATE TABLE `table_record` (
  `record_id` int(11) NOT NULL,
  `table_no` varchar(50) NOT NULL,
  `items` longtext NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `sst` decimal(10,2) NOT NULL,
  `service_charge` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `payment_time` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_record`
--

INSERT INTO `table_record` (`record_id`, `table_no`, `items`, `subtotal`, `sst`, `service_charge`, `total`, `payment_time`) VALUES
(1, 'Table 1', '[{\"menu_name\":\"Sup Kambing Meletup\",\"quantity\":\"2\",\"total\":\"14.00\"}]', 14.00, 0.84, 1.40, 16.24, '2025-10-13 03:58:08'),
(2, 'Table 1', '[{\"menu_name\":\"Sup Kambing Meletup\",\"quantity\":\"1\",\"total\":\"7.00\"},{\"menu_name\":\"Sup Kambing Meletup\",\"quantity\":\"2\",\"total\":\"14.00\"},{\"menu_name\":\"Sup Kambing Meletup\",\"quantity\":\"5\",\"total\":\"35.00\"},{\"menu_name\":\"Sup Kambing Meletup\",\"quantity\":\"1\",\"total\":\"7.00\"}]', 63.00, 3.78, 6.30, 73.08, '2025-10-13 11:04:14'),
(3, 'Table 1', '[{\"menu_name\":\"Sup Kambing Meletup\",\"quantity\":\"1\",\"total\":\"7.00\"},{\"menu_name\":\"Sirap Limau\",\"quantity\":\"2\",\"total\":\"6.00\"}]', 13.00, 0.78, 1.30, 15.08, '2025-10-13 15:50:16'),
(4, 'Table 2', '[{\"menu_name\":\"Sirap Limau\",\"quantity\":\"1\",\"total\":\"3.00\"},{\"menu_name\":\"Sirap Limau\",\"quantity\":\"1\",\"total\":\"3.00\"},{\"menu_name\":\"Sirap Limau\",\"quantity\":\"1\",\"total\":\"3.50\"},{\"menu_name\":\"Sirap Limau\",\"quantity\":\"1\",\"total\":\"3.50\"}]', 13.00, 0.78, 1.30, 15.08, '2025-10-13 15:51:47');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `menu_variants`
--
ALTER TABLE `menu_variants`
  ADD PRIMARY KEY (`variant_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `tables`
--
ALTER TABLE `tables`
  ADD PRIMARY KEY (`table_id`);

--
-- Indexes for table `table_record`
--
ALTER TABLE `table_record`
  ADD PRIMARY KEY (`record_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `menu_variants`
--
ALTER TABLE `menu_variants`
  MODIFY `variant_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tables`
--
ALTER TABLE `tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `table_record`
--
ALTER TABLE `table_record`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `menu_variants`
--
ALTER TABLE `menu_variants`
  ADD CONSTRAINT `menu_variants_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`menu_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
