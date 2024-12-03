-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 03, 2024 at 10:33 PM
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
-- Database: `ahl_user`
--

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `customer_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(1, 9, 10, 7, '2024-12-03 17:55:15', '2024-12-03 21:27:20'),
(3, 9, 12, 4, '2024-12-03 19:16:09', '2024-12-03 21:27:26'),
(4, 9, 11, 1, '2024-12-03 21:27:23', '2024-12-03 21:27:23'),
(5, 9, 13, 1, '2024-12-03 21:27:29', '2024-12-03 21:27:29'),
(6, 9, 14, 1, '2024-12-03 21:27:33', '2024-12-03 21:27:33'),
(7, 9, 15, 1, '2024-12-03 21:27:39', '2024-12-03 21:27:39'),
(8, 9, 16, 1, '2024-12-03 21:27:41', '2024-12-03 21:27:41'),
(9, 9, 17, 1, '2024-12-03 21:27:44', '2024-12-03 21:27:44'),
(10, 9, 18, 1, '2024-12-03 21:27:46', '2024-12-03 21:27:46'),
(11, 9, 19, 1, '2024-12-03 21:27:48', '2024-12-03 21:27:48'),
(12, 9, 20, 1, '2024-12-03 21:27:52', '2024-12-03 21:27:52'),
(13, 9, 21, 1, '2024-12-03 21:27:55', '2024-12-03 21:27:55'),
(14, 9, 22, 1, '2024-12-03 21:27:58', '2024-12-03 21:27:58'),
(15, 9, 23, 1, '2024-12-03 21:28:00', '2024-12-03 21:28:00'),
(16, 9, 24, 1, '2024-12-03 21:28:03', '2024-12-03 21:28:03'),
(17, 9, 25, 1, '2024-12-03 21:28:06', '2024-12-03 21:28:06');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `status` enum('pending','approved','declined') DEFAULT 'pending',
  `images` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_name`, `email`, `password`, `address`, `contact_number`, `status`, `images`, `profile_image`) VALUES
(4, 'larry', 'lianamae.opena@students.isatu.edu.ph', '$2y$10$TZHa6vjRKNqiGTa5s/zJ7OawcrrvwiiRc5SE156sUtsPfXigpIVve', 'jaro', '09165782121', 'approved', 'uploads/674328ce8d7f9_download.png', NULL),
(8, 'larry', 'lry4750@gmail.com', '$2y$10$gjnlvLFzg2HHyVdO.X3DueVg6vXOYgtYeE62vhfWYEsay9/nbF4jG', 'jaro', '09123456777', 'approved', 'uploads_img/674b47192f02f_download.png', 'download.png'),
(9, 'larry Denver', 'larrydenverbiaco@gmail.com', '$2y$10$N58zoM5M32FivJmLtboaGeLuCN69uTr97kbCRH0Ys81wa9xxpnnjK', 'jaroo', '09123456789', 'approved', 'uploads_img/674d85e291945_download.png', 'download.png');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivery_method` enum('pickup','cod') NOT NULL DEFAULT 'pickup',
  `reference_number` varchar(255) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `order_date`, `delivery_method`, `reference_number`, `total_price`) VALUES
(39, 4, '2024-12-02 15:37:23', 'pickup', 'ORDER_674dd433bb740', 36.00),
(40, 4, '2024-12-02 15:37:41', 'pickup', 'ORDER_674dd44583c06', 46.00),
(41, 4, '2024-12-02 15:39:13', 'pickup', 'ORDER_674dd4a14fa1d', 22.00),
(42, 4, '2024-12-02 15:39:33', 'pickup', 'ORDER_674dd4b55890b', 46.00),
(43, 4, '2024-12-02 16:17:35', 'pickup', 'ORDER_674ddd9f3d96c', 24.00),
(44, 4, '2024-12-02 16:17:38', 'pickup', 'ORDER_674ddda287adf', 24.00),
(45, 4, '2024-12-02 16:19:09', 'pickup', 'ORDER_674dddfd17d81', 24.00),
(46, 4, '2024-12-02 16:19:13', 'pickup', 'ORDER_674dde0199677', 24.00),
(47, 4, '2024-12-02 16:20:41', 'pickup', 'ORDER_674dde5936356', 24.00),
(48, 4, '2024-12-02 17:04:39', 'pickup', 'ORDER_674de8a77f851', 24.00),
(49, 9, '2024-12-02 17:55:56', 'pickup', 'AHL-A7E7A304', 36.00),
(50, 9, '2024-12-03 01:52:11', 'pickup', 'AHL-89C03FD0', 58.00),
(61, 9, '2024-12-03 19:56:12', 'pickup', 'AHL-BE201C04', 36.00),
(62, 9, '2024-12-03 20:31:57', 'pickup', 'AHL-955B1BF6', 120.00),
(63, 9, '2024-12-03 20:32:44', 'pickup', 'AHL-C9FC7F42', 36.00),
(64, 9, '2024-12-03 20:34:49', 'pickup', 'AHL-5983B5EB', 22.00),
(65, 9, '2024-12-03 20:36:04', 'pickup', 'AHL-CD4B8314', 30.00),
(66, 9, '2024-12-03 20:36:29', 'pickup', 'AHL-97E6CEBC', 35.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders2`
--

CREATE TABLE `orders2` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `delivery_method` enum('pickup','cod') NOT NULL DEFAULT 'pickup',
  `reference_number` varchar(255) NOT NULL,
  `total_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `shippingfee` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `order_id`, `product_id`, `quantity`, `price`, `shippingfee`) VALUES
(42, 49, 10, 3, 12.00, 0.00),
(43, 50, 10, 3, 12.00, 0.00),
(44, 50, 11, 1, 22.00, 0.00),
(50, 61, 10, 3, 12.00, 0.00),
(51, 62, 10, 10, 12.00, 0.00),
(52, 63, 10, 3, 12.00, 0.00),
(53, 64, 11, 1, 22.00, 0.00),
(54, 65, 13, 1, 30.00, 0.00),
(55, 66, 14, 1, 35.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `order_details2`
--

CREATE TABLE `order_details2` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `shippingfee` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `image` varchar(100) DEFAULT NULL,
  `category` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_name`, `description`, `price`, `quantity`, `image`, `category`, `created_at`) VALUES
(10, 'scissors', NULL, 12.00, 77, 'scissors.jpg', 'Scissors, Glue', '2024-11-24 13:28:49'),
(11, 'eraser', NULL, 22.00, 97, 'eraser.jpg', 'Pencils, Sharpeners, Erasers', '2024-11-24 13:29:08'),
(12, 'notebook', NULL, 13.00, 97, 'notebooks.jpg', 'Notebooks', '2024-11-24 13:31:38'),
(13, 'marker 1', NULL, 30.00, 99, 'marker1.jpg', 'Markers', '2024-12-03 19:45:42'),
(14, 'marker 2', NULL, 35.00, 99, 'marker2.jpg', 'Markers', '2024-12-03 19:46:09'),
(15, 'marker 3', NULL, 40.00, 100, 'marker3.jpg', 'Markers', '2024-12-03 19:46:22'),
(16, 'art supplies 1', NULL, 100.00, 100, 'art supplies1.jpg', 'Art Supplies', '2024-12-03 19:46:52'),
(17, 'art supplies 2', NULL, 110.00, 100, 'art supplies2.jpg', 'Art Supplies', '2024-12-03 19:47:07'),
(18, 'backpack 1', NULL, 100.00, 100, 'backpack1.jpg', 'Backpacks', '2024-12-03 19:47:29'),
(19, 'backpack 2', NULL, 120.00, 100, 'backpack2.jpg', 'Backpacks', '2024-12-03 19:47:55'),
(20, 'backpack 3', NULL, 150.00, 100, 'backpack3.jpg', 'Backpacks', '2024-12-03 19:48:17'),
(21, 'eraser 1', NULL, 20.00, 100, 'eraser1.jpg', 'Pencils, Sharpeners, Erasers', '2024-12-03 19:48:39'),
(22, 'notebook 1', NULL, 30.00, 100, 'notebooks1.jpg', 'Notebooks', '2024-12-03 19:48:59'),
(23, 'notebook 2', NULL, 20.00, 100, 'notebooks2.jpg', 'Notebooks', '2024-12-03 19:49:14'),
(24, 'notebook 3', NULL, 33.00, 100, 'notebooks3.jpg', 'Notebooks', '2024-12-03 19:49:30'),
(25, 'notebook 4', NULL, 60.00, 100, 'notebooks4.jpg', 'Notebooks', '2024-12-03 19:49:46');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `orders2`
--
ALTER TABLE `orders2`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `order_details2`
--
ALTER TABLE `order_details2`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=67;

--
-- AUTO_INCREMENT for table `orders2`
--
ALTER TABLE `orders2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `order_details2`
--
ALTER TABLE `order_details2`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders2`
--
ALTER TABLE `orders2`
  ADD CONSTRAINT `orders2_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_details2`
--
ALTER TABLE `order_details2`
  ADD CONSTRAINT `order_details2_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details2_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
