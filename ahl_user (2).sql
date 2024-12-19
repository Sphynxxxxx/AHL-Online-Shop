-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2024 at 05:31 PM
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
(11, 'Juan Dela Cruz 11', 'larrydenverbiaco@gmail.com', '$2y$10$q8lS3lranOcw3LOGWnTbiOMthb/3PwZ37C2K74rH4pFdKUjgjjJ2K', 'Jaro, Iloilo City', '09123456789', 'approved', 'uploads_img/67583153c8503_person1.jpg', 'bamboo hoop chair.jpg'),
(13, 'Juan Dela Cruz', 'qkidzlet5@gmail.com', '$2y$10$hwPBEsjHAgKB/IKqo4jURerd1HUlD/X5NrnLHsMH6FbMHh74SCtv2', 'Cebu City', '09212125657', 'declined', 'uploads_img/67583267942fb_person2.jpg', NULL),
(14, 'Juan Dela Cruz 11', 'lry4750@gmail.com', '$2y$10$Jvl3NaKypZQAnvvCyhOV3.vltXRTSlY8RMCsKxmNhFG8ebx9KKk4S', 'jaro', '09123456789', 'pending', 'uploads_img/676417a41d888_person1.jpg', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `order_id`, `title`, `message`, `date_created`) VALUES
(11, 152, 'Order Status Update', 'You have successfully placed your order.', '2024-12-19 08:35:19'),
(12, 156, 'Order Status Update', 'You have successfully placed your order.', '2024-12-19 08:37:53'),
(13, 157, 'Order Status Update', 'You have successfully placed your order.', '2024-12-19 08:38:00'),
(14, 158, 'Order Status Update', 'You have successfully placed your order.', '2024-12-19 08:38:09'),
(15, 159, 'Order Status Update', 'You have successfully placed your order.', '2024-12-19 08:53:27');

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
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','ready_to_pick_up','canceled','received') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `order_date`, `delivery_method`, `reference_number`, `total_price`, `status`) VALUES
(152, 11, '2024-12-19 15:35:19', 'pickup', 'AHL-20241219-310B', 88.00, 'received'),
(156, 11, '2024-12-19 15:37:53', 'pickup', 'AHL-20241219-0E6E', 44.00, 'canceled'),
(157, 11, '2024-12-19 15:38:00', 'pickup', 'AHL-20241219-655A', 22.00, 'received'),
(158, 11, '2024-12-19 15:38:09', 'pickup', 'AHL-20241219-FC8F', 70.00, 'canceled'),
(159, 11, '2024-12-19 15:53:27', 'pickup', 'AHL-20241219-7A50', 44.00, 'received');

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
(133, 152, 11, 4, 22.00, 0.00),
(137, 156, 11, 2, 22.00, 0.00),
(138, 157, 11, 1, 22.00, 0.00),
(139, 158, 12, 2, 13.00, 0.00),
(140, 158, 11, 2, 22.00, 0.00),
(141, 159, 11, 2, 22.00, 0.00);

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
(10, 'scissors', NULL, 12.00, 0, 'scissors.jpg', 'Scissors, Glue', '2024-11-24 13:28:49'),
(11, 'eraser', NULL, 22.00, 68, 'eraser.jpg', 'Pencils, Sharpeners, Erasers', '2024-11-24 13:29:08'),
(12, 'notebook', NULL, 13.00, 91, 'notebooks.jpg', 'Notebooks', '2024-11-24 13:31:38'),
(13, 'marker 1', NULL, 30.00, 96, 'marker1.jpg', 'Markers', '2024-12-03 19:45:42'),
(14, 'marker 2', NULL, 35.00, 99, 'marker2.jpg', 'Markers', '2024-12-03 19:46:09'),
(15, 'marker 3', NULL, 40.00, 99, 'marker3.jpg', 'Markers', '2024-12-03 19:46:22'),
(16, 'art supplies 1', NULL, 100.00, 98, 'art supplies1.jpg', 'Art Supplies', '2024-12-03 19:46:52'),
(17, 'art supplies 2', NULL, 110.00, 97, 'art supplies2.jpg', 'Art Supplies', '2024-12-03 19:47:07'),
(18, 'backpack 1', NULL, 100.00, 96, 'backpack1.jpg', 'Backpacks', '2024-12-03 19:47:29'),
(19, 'backpack 2', NULL, 120.00, 100, 'backpack2.jpg', 'Backpacks', '2024-12-03 19:47:55'),
(20, 'backpack 3', NULL, 150.00, 99, 'backpack3.jpg', 'Backpacks', '2024-12-03 19:48:17'),
(21, 'eraser 1', NULL, 20.00, 99, 'eraser1.jpg', 'Pencils, Sharpeners, Erasers', '2024-12-03 19:48:39'),
(22, 'notebook 1', NULL, 30.00, 96, 'notebooks1.jpg', 'Notebooks', '2024-12-03 19:48:59'),
(23, 'notebook 2', NULL, 20.00, 97, 'notebooks2.jpg', 'Notebooks', '2024-12-03 19:49:14'),
(24, 'notebook 3', NULL, 33.00, 96, 'notebooks3.jpg', 'Notebooks', '2024-12-03 19:49:30'),
(28, 'notebook 44', NULL, 33.00, 96, 'notebooks4.jpg', 'Notebooks', '2024-12-04 03:06:52'),
(30, 'notebook 123', NULL, 10.00, 11, 'acce wallet.jpg', 'Papers', '2024-12-19 13:05:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `fk_product_id` (`product_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=142;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_product_id` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_details_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
