-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 15, 2024 at 06:55 PM
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
-- Database: `beesbiz`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `user_id`, `media_id`, `quantity`) VALUES
(102, 21, 33, 3),
(103, 21, 35, 3),
(104, 19, 37, 1);

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `user_id`, `media_id`, `comment`, `created_at`) VALUES
(63, 20, 33, 'good', '2024-07-14 16:12:21'),
(65, 19, 37, 'i love this pack', '2024-07-15 16:50:39');

-- --------------------------------------------------------

--
-- Table structure for table `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `likes`
--

INSERT INTO `likes` (`id`, `user_id`, `media_id`, `created_at`) VALUES
(49, 20, 33, '2024-07-14 16:12:15'),
(52, 19, 34, '2024-07-15 16:50:13'),
(53, 19, 35, '2024-07-15 16:50:17'),
(54, 19, 37, '2024-07-15 16:50:22'),
(55, 19, 36, '2024-07-15 16:50:26');

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `media_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `item_name` varchar(255) DEFAULT NULL,
  `media_type` enum('instrumental','photo','video') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `contact_info` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_extension` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `media`
--

INSERT INTO `media` (`media_id`, `user_id`, `item_name`, `media_type`, `file_path`, `contact_info`, `created_at`, `file_extension`, `price`) VALUES
(33, 18, 'Kisii honey', 'photo', '6693f71a47235_Untitled.jpg', 'Kisii honey,very original and well harvested @ 40$ per kg', '2024-07-14 16:04:42', '', 40.00),
(34, 19, 'Kisumu honey ', 'photo', '6693f817a519a_3.jpg', 'Very sweet $50 per kg', '2024-07-14 16:08:55', '', 50.00),
(35, 20, 'Honey harvesting equipments', 'photo', '6693f8ca72eef_pks.jpg', 'Get all set of honey harvesting tools\r\nat $500', '2024-07-14 16:11:54', '', 500.00),
(36, 21, 'We do honey harvesting services', 'photo', '6693f97463222_hdc.jpg', 'we are affordable and movable', '2024-07-14 16:14:44', '', 200.00),
(37, 22, 'Honey harvesting gown', 'photo', '669551e886e04_ggg.jpg', 'Get this 3pack at only $56 or call 0673573533\r\nwe have all sizes', '2024-07-15 16:44:24', '', 56.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `phone_number` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `location` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `item_name`, `phone_number`, `email`, `location`, `price`, `quantity`, `total`, `order_date`, `status`) VALUES
(30, 21, 'Kisii honey', '078967564', 'diana@gmail.com', 'kisiii nyaera', 40.00, 3, 120.00, '2024-07-14 16:16:34', 'Received'),
(31, 21, 'Honey harvesting equipments', '078967564', 'diana@gmail.com', 'kisiii nyaera', 500.00, 3, 1500.00, '2024-07-14 16:16:34', 'Received'),
(32, 19, 'Honey harvesting gown', '0754367886', 'peter@gmail.com', 'by grace nairobi', 56.00, 1, 56.00, '2024-07-15 16:51:42', 'Delivered');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `date` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `date`, `reset_token`, `role`) VALUES
(18, 'zaina', 'zainamei@gmail.com', '$2y$10$i7vCZaK5l0orymkvMosbAu/Tb/FXfycZglhENb61cKE.Z5Cdi.Zny', '2024-07-14 15:25:57', '', 'user'),
(19, 'peter', 'peter@gmail.com', '$2y$10$yiZiAhdFhaY374JPJLeJqOVwXY5LwSHc4FmNlUbs/7BQLlGsKARJK', '2024-07-14 16:07:38', '', 'user'),
(20, 'vivaan', 'vivaan@gmail.com', '$2y$10$c.Y9I2D8KBTyzwd2On9C3OnvbZqwcf/uOi0L/R4dHC7BJ0jYtygRG', '2024-07-14 16:10:46', '', 'user'),
(21, 'diana', 'diana@gmail.com', '$2y$10$fIXOV9RxFfWg9UBTSyOdA.YWinyBPl6QVTfHfXzORvaJHOBD/AGKu', '2024-07-14 16:13:51', '', 'user'),
(22, 'japhet', 'japhet@gmail.com', '$2y$10$mWxMZT9HgQaiFs4uj56EMuvoLQi8BssOuqeGoPyTk8N8WOzoHBp4S', '2024-07-15 16:41:13', '', 'user');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_id` (`user_id`),
  ADD KEY `fk_media_id` (`media_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `media_id` (`media_id`);

--
-- Indexes for table `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `media_id` (`media_id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`media_id`),
  ADD KEY `media_ibfk_1` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `media_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `fk_media_id` FOREIGN KEY (`media_id`) REFERENCES `media` (`media_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `media` (`media_id`);

--
-- Constraints for table `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `media` (`media_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
