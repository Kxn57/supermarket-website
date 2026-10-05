-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 19, 2026 at 02:48 PM
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
-- Database: `db10`
--

-- --------------------------------------------------------

--
-- Table structure for table `item`
--

CREATE TABLE `item` (
  `order_id` int(11) NOT NULL,
  `product_id` char(4) NOT NULL,
  `price` decimal(4,2) NOT NULL,
  `unit` int(11) NOT NULL,
  `subtotal` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `item`
--

INSERT INTO `item` (`order_id`, `product_id`, `price`, `unit`, `subtotal`) VALUES
(1, 'P002', 2.00, 1, 2.00),
(1, 'P003', 3.00, 1, 3.00),
(1, 'P004', 4.00, 1, 4.00),
(2, 'P001', 1.00, 1, 1.00),
(2, 'P002', 2.00, 1, 2.00),
(2, 'P003', 3.00, 1, 3.00),
(2, 'P004', 4.00, 1, 4.00),
(2, 'P005', 5.00, 1, 5.00),
(2, 'P006', 6.00, 1, 6.00),
(2, 'P007', 7.00, 1, 7.00),
(2, 'P008', 8.00, 1, 8.00),
(3, 'P001', 1.00, 2, 2.00),
(3, 'P002', 2.00, 1, 2.00),
(3, 'P003', 3.00, 1, 3.00),
(3, 'P004', 4.00, 1, 4.00),
(3, 'P005', 5.00, 1, 5.00),
(3, 'P006', 6.00, 1, 6.00),
(4, 'P002', 2.00, 1, 2.00),
(4, 'P003', 3.00, 1, 3.00),
(5, 'P002', 2.00, 1, 2.00),
(5, 'P003', 3.00, 1, 3.00),
(5, 'P004', 4.00, 1, 4.00),
(5, 'P005', 5.00, 1, 5.00),
(6, 'P002', 2.00, 1, 2.00),
(6, 'P003', 3.00, 1, 3.00),
(6, 'P004', 4.00, 1, 4.00),
(7, 'P007', 7.00, 5, 35.00),
(7, 'P008', 8.00, 1, 8.00),
(8, 'P001', 1.00, 1, 1.00),
(8, 'P002', 2.00, 1, 2.00),
(8, 'P003', 3.00, 1, 3.00),
(8, 'P004', 4.00, 1, 4.00),
(8, 'P005', 5.00, 5, 25.00);

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `id` int(11) NOT NULL,
  `datetime` datetime NOT NULL,
  `count` int(11) NOT NULL,
  `total` decimal(8,2) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT 0,
  `paid_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id`, `datetime`, `count`, `total`, `user_id`, `is_paid`, `paid_at`) VALUES
(1, '2026-04-19 19:12:03', 3, 9.00, 2, 1, '2026-04-19 19:12:03'),
(2, '2026-04-19 19:13:26', 8, 36.00, 5, 1, '2026-04-19 19:13:26'),
(3, '2026-04-19 19:16:34', 7, 22.00, 5, 1, '2026-04-19 19:16:34'),
(4, '2026-04-19 19:26:45', 2, 5.00, 5, 1, '2026-04-19 19:26:45'),
(5, '2026-04-19 19:29:58', 4, 14.00, 5, 1, '2026-04-19 19:29:58'),
(6, '2026-04-19 19:32:02', 3, 9.00, 5, 1, '2026-04-19 19:32:02'),
(7, '2026-04-19 20:11:16', 6, 43.00, 6, 1, '2026-04-19 20:11:16'),
(8, '2026-04-19 20:18:57', 9, 35.00, 7, 1, '2026-04-19 20:18:57');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` char(4) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(4,2) NOT NULL,
  `photo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id`, `name`, `price`, `photo`) VALUES
('P001', 'Banana', 1.00, 'banana.jpg'),
('P002', 'Cherries', 2.00, 'cherries.jpg'),
('P003', 'Grapes', 3.00, 'grapes.jpg'),
('P004', 'Green Apple', 4.00, 'green-apple.jpg'),
('P005', 'Kiwi', 5.00, 'kiwi.jpg'),
('P006', 'Lemon', 6.00, 'lemon.jpg'),
('P007', 'Mango', 7.00, 'mango.jpg'),
('P008', 'Melon', 8.00, 'melon.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `token`
--

CREATE TABLE `token` (
  `id` varchar(100) NOT NULL,
  `expire` datetime NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `photo` varchar(100) NOT NULL,
  `role` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `email`, `password`, `name`, `photo`, `role`) VALUES
(1, '1@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'sohai', '69e4b09788dc5.jpg', 'Admin'),
(2, '2@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Kim Jisoo', '2.jpg', 'Member'),
(4, '4@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Roseanne Park', '4.jpg', 'Member'),
(5, 'kxn2379@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'Shuaige', '69e4b8bd500f4.jpg', 'Member'),
(6, 'ziyuen0423@gmail.com', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'jojo', '69e4c772e2724.jpg', 'Member'),
(7, 'chongyw-wm25@student.tarc.edu.my', '7c4a8d09ca3762af61e59520943dc26494f8941b', 'yw', '69e4c80a72dd2.jpg', 'Member');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `item`
--
ALTER TABLE `item`
  ADD PRIMARY KEY (`order_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `item`
--
ALTER TABLE `item`
  ADD CONSTRAINT `item_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `order` (`id`),
  ADD CONSTRAINT `item_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id`);

--
-- Constraints for table `order`
--
ALTER TABLE `order`
  ADD CONSTRAINT `order_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);

--
-- Constraints for table `token`
--
ALTER TABLE `token`
  ADD CONSTRAINT `token_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
