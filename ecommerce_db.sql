-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 09, 2025 at 11:47 PM
-- Server version: 8.3.0
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `userid` int NOT NULL,
  `productid` int NOT NULL,
  `quantity` int NOT NULL,
  `purchaseprice` decimal(10,2) NOT NULL,
  `createdat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updatedat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `orderstatus` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `userid`, `productid`, `quantity`, `purchaseprice`, `createdat`, `updatedat`, `orderstatus`) VALUES
(6, 8, 2, 1, 100.00, '2025-06-08 11:39:06', '2025-06-08 11:39:06', 'completed'),
(7, 8, 2, 1, 100.00, '2025-06-08 11:39:07', '2025-06-08 11:39:07', 'completed'),
(8, 8, 2, 1, 100.00, '2025-06-08 11:39:09', '2025-06-08 11:39:09', 'completed'),
(27, 8, 2, 1, 100.00, '2025-06-08 18:38:11', '2025-06-08 18:38:11', 'completed'),
(37, 11, 2, 2, 100.00, '2025-06-09 18:35:53', '2025-06-09 18:35:53', 'pending'),
(38, 11, 3, 2, 100.00, '2025-06-09 18:38:18', '2025-06-09 18:38:18', 'pending'),
(40, 22, 3, 2, 100.00, '2025-06-09 20:26:31', '2025-06-09 20:26:31', 'completed'),
(41, 2, 4, 6, 100.00, '2025-06-09 20:32:17', '2025-06-09 20:32:17', 'completed'),
(42, 2, 7, 2, 200.00, '2025-06-09 20:42:14', '2025-06-09 20:42:14', 'completed'),
(43, 22, 4, 4, 100.00, '2025-06-09 20:55:45', '2025-06-09 20:55:45', 'completed'),
(44, 22, 3, 1, 100.00, '2025-06-09 21:16:53', '2025-06-09 21:16:53', 'completed'),
(45, 22, 3, 1, 100.00, '2025-06-09 21:19:46', '2025-06-09 21:19:46', 'completed'),
(48, 22, 4, 1, 100.00, '2025-06-09 21:22:08', '2025-06-09 21:22:08', 'completed'),
(49, 22, 3, 1, 100.00, '2025-06-09 21:23:03', '2025-06-09 21:23:03', 'completed'),
(50, 22, 3, 4, 100.00, '2025-06-09 21:23:36', '2025-06-09 21:23:36', 'completed'),
(51, 8, 6, 1, 200.00, '2025-06-09 21:24:57', '2025-06-09 21:24:57', 'pending'),
(52, 2, 3, 1, 100.00, '2025-06-09 21:27:07', '2025-06-09 21:27:07', 'completed'),
(53, 2, 2, 1, 150.00, '2025-06-09 21:27:11', '2025-06-09 21:27:11', 'completed'),
(54, 2, 3, 1, 100.00, '2025-06-09 21:27:40', '2025-06-09 21:27:40', 'completed'),
(55, 22, 4, 4, 100.00, '2025-06-09 21:27:43', '2025-06-09 21:27:43', 'completed'),
(56, 22, 3, 1, 100.00, '2025-06-09 21:31:44', '2025-06-09 21:31:44', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `price` int NOT NULL,
  `category` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` date NOT NULL,
  `description` text COLLATE utf8mb4_general_ci NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `stock` int NOT NULL DEFAULT '2',
  `discount_price` int NOT NULL DEFAULT '100'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `category`, `created_at`, `description`, `image`, `stock`, `discount_price`) VALUES
(2, 'Wireless Headphones', 300, 'Electronics', '2025-06-09', 'Premium noise-cancelling wireless headphones', 'Pr1.avif', 4, 150),
(3, 'Smart Watch pro', 200, 'Electronics', '2025-06-09', 'Fitness tracking smartwatch with heart rate monitor', 'dummy1.png', 18, 100),
(4, 'Camera', 900, 'Electronics', '2025-06-09', 'Professional DSLR camera with 24MP sensor', 'dummy1.png', 0, 100),
(5, 'Tablet', 500, 'Electronics', '2025-06-09', '10.9-inch tablet with retina display', 'dummy1.png', 11, 100),
(6, 'Premium Cotton Hoodie', 300, 'new', '2025-06-09', 'A high-quality unisex hoodie made from 100% premium cotton. Soft, warm, and perfect for everyday wear. Features a front pocket and adjustable drawstring hood.', 'dummy1.png', 5, 200),
(7, 'Premium Cotton shorts', 400, 'Shorts', '2025-06-09', 'Best selling cotton shorts in good price!!', 'dummy1.png', 3, 200),
(8, 'Premium Cotton T-shirts', 500, 'Shirt', '2025-06-09', 'The best selling t-shirt', 'dummy1.png', 4, 300);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `pwd` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `useraddress` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `pwd`, `phone`, `useraddress`, `role`, `created_at`, `updated_at`) VALUES
(2, 'kebede', 'fjklsj@gmail.com', '$2y$10$ghijDUMKNJDavGnmaYnjHO25uKtZu.60XglT0rnVN7z/xVFsKZ7PG', '1345234', 'adsfds', 'admin', '2025-06-07 07:50:07', '2025-06-07 07:50:07'),
(8, 'Dagi', 'kalab.tadesse-ug@aau.edu.et', '$2y$10$QZ19c17ZdXjmzgzq8.M9wOFNGzbzD3dWSo5vQw/H5/EW2pbVZu7Ea', '2435234234', 'ethjksl', 'customer', '2025-06-07 11:15:33', '2025-06-07 11:15:33'),
(10, 'kalab', 'kalupt9998@gmail.com', '$2y$10$TTkwp/p/9FWWC01AsBhPmuM46HsHp4yAsNl/M6bJ/9MkfumDmKLn6', '2435234234', 'ethjksl', 'customer', '2025-06-08 06:35:29', '2025-06-08 06:35:29'),
(11, 'abel', 'erdunoabel47@gmail.com', '$2y$10$Hkj1wmHsBcuFAKf7lBnk8eM/jltZEH1hyLOLlsBlucYtNdGBPLPdK', '0995527848', '123', 'customer', '2025-06-08 18:37:03', '2025-06-08 18:37:03'),
(12, 'kebede', 'fjklsj@gmail.com', '$2y$10$RJPgoOaGsIdn2O4pBtQhQeRmDFIWJ1zoIgXjzL7I2b29kBMpPPqvC', '1345234', 'adsfds', 'admin', '2025-06-08 19:50:07', '2025-06-08 19:50:07'),
(13, 'Dagi', 'kalab.tadesse-ug@aau.edu.et', '$2y$10$hO.GhLAC1wYPw4woXfDuZ.hFRKKFinkT/fRkWDVcZ7mmHPfx4Aqoa', '2435234234', 'ethjksl', 'customer', '2025-06-08 19:50:07', '2025-06-08 19:50:07'),
(14, 'kalab', 'kalupt9998@gmail.com', '$2y$10$czrLInyhKewhFKMIgHVyServmZhGvLj59OBbC6OWg0CsdMnkcY/6G', '2435234234', 'ethjksl', 'customer', '2025-06-08 19:50:07', '2025-06-08 19:50:07'),
(15, 'abel', 'erdunoabel47@gmail.com', '$2y$10$TAlJdttV9lelOl4aGxRc.O5lMU9QpBxbUU7GTh5WNct8ZDEPofGV6', '0995527848', '123', 'customer', '2025-06-08 19:50:07', '2025-06-08 19:50:07'),
(16, 'abel', 'hakensoabel@gmail.com', '$2y$10$dB1.jZuqfqWT0oEdsinHyu8PtiXXQN1XWyeGi2f94Da8q6p3wUJT.', '1234', '1234', 'customer', '2025-06-08 19:50:50', '2025-06-08 19:50:50'),
(17, 'admin', 'hakensoabel@gmail.com', '$2y$10$TvfczIJY8yL7NBMAmc/nlO4Urc0QhAolmV4TBAVDEapv246tWE.0.', '1234', '1234', 'customer', '2025-06-08 19:52:26', '2025-06-08 19:52:26'),
(18, 'abel', 'hakensoabel@gmail.com', '$2y$10$XBa9ksKFO6Bxu8bTRHWDz.psE3Kn4WC.KzknEjGxLDZhJ6fpZtOIy', '1234', '1234', 'customer', '2025-06-09 08:22:07', '2025-06-09 08:22:07'),
(19, 'this', 'this@gmail.com', '$2y$10$DP9MBsLX6JvxaB10X0Bvi.prBy0bEFOsbTC8sBiJmFLjDo9LMkjH2', '123', '123', 'customer', '2025-06-09 08:50:14', '2025-06-09 08:50:14'),
(20, 'another', 'this@gmail.com', '$2y$10$uVcWWPysZdxGtcJ.kpkZjOUqRhb7T7O70S2BsD/G.H1zFu07KyPG2', '1234567', '123', 'customer', '2025-06-09 20:17:20', '2025-06-09 20:17:20'),
(21, 'amlike', 'hakensoabel@gmail.com', '$2y$10$0HcHzeq3UrxUiATYmgE.vObzFZda19jTxF5jXj3sIklKf2cw9yg2q', '1234567', '1234', 'customer', '2025-06-09 20:19:11', '2025-06-09 20:19:11'),
(22, 'noo', 'elias@gmail.com', '$2y$10$gEOwGHK1n8.sx2hVnjPPZ.fOPlhvndl01hnKjebg8hoPiiLj0qV/i', '0995527848', '1234', 'customer', '2025-06-09 20:24:56', '2025-06-09 20:24:56');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(9, 11, 3, '2025-06-09 18:35:50'),
(11, 22, 4, '2025-06-09 20:28:06'),
(12, 22, 3, '2025-06-09 20:28:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userid` (`userid`),
  ADD KEY `productid` (`productid`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_category_idx` (`name`,`category`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=57;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`productid`) REFERENCES `products` (`id`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
