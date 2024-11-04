-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 28, 2024 at 11:21 AM
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
-- Database: `testdb`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password`) VALUES
(2, 'admin@gmail.com', '$2y$10$9q4r3le7FwSxoQ0QLNNCfeRJlgv0w5o..YZDtSTvkVeY8zT6Q28qW');

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `detail` text NOT NULL,
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `queue_number` int(11) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `appointment_date`, `appointment_time`, `queue_number`, `status`, `created_at`) VALUES
(2, 21, '2024-10-31', '08:00:00', 1, 'pending', '2024-10-26 00:26:09');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(6) UNSIGNED NOT NULL,
  `user_id` int(6) UNSIGNED NOT NULL,
  `package_id` int(6) UNSIGNED NOT NULL,
  `event_location` varchar(255) NOT NULL,
  `custom_location` varchar(255) DEFAULT NULL,
  `event_date` date NOT NULL,
  `receipt_photo` varchar(255) NOT NULL,
  `status` enum('pending','approved','declined') DEFAULT 'pending',
  `decline_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `package_id`, `event_location`, `custom_location`, `event_date`, `receipt_photo`, `status`, `decline_reason`) VALUES
(22, 21, 11, 'basilan', NULL, '2025-01-01', 'uploads/e79dd102b35213f815291e0fb4bd12df.jpg', 'approved', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `gallery`
--

CREATE TABLE `gallery` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `type` enum('photo','video','features') NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallery`
--

INSERT INTO `gallery` (`id`, `title`, `description`, `type`, `file_path`, `created_at`) VALUES
(8, 'sample title 1', 'sample description', 'photo', 'uploads/photos/D-7.jpg', '2024-10-27 18:03:11'),
(9, 'sample video 1', 'sample description', 'video', 'uploads/videos/21dbb6d45f92051d667114c795382eae50cb92e7d760b4a6ccc14b12f2ee442d.mp4', '2024-10-27 18:03:55'),
(10, 'sample title 2', 'sample description 2', 'photo', 'uploads/photos/D-5.jpg', '2024-10-27 18:05:10'),
(11, 'sample video 2', 'sample description 2', 'video', 'uploads/videos/402053846_7392263927454527_6587841122060056140_n.mp4', '2024-10-27 18:07:02'),
(12, 'sample picture', 'description', 'photo', 'uploads/photos/D-9.jpg', '2024-10-27 18:16:04'),
(13, 'sample', 'sample', 'photo', 'uploads/photos/FJ-18.jpg', '2024-10-27 18:19:40'),
(14, 'sanokeasdh', 'asdkjhaksd', 'photo', 'uploads/photos/FJ-15.jpg', '2024-10-27 18:19:59'),
(15, 'samopdkasfi', 'kjasdkgajd', 'photo', 'uploads/photos/C-23.jpg', '2024-10-27 18:20:21'),
(16, 'sdihfsczh', 'iosajosidf', 'photo', 'uploads/photos/FJ-14.jpg', '2024-10-27 18:20:47'),
(17, 'sample title 5', 'samdasldkj', 'photo', 'uploads/photos/D-2.jpg', '2024-10-28 02:52:56'),
(18, 'sdffklsdk', 'hafosfsdhoyo', 'photo', 'uploads/photos/D-10.jpg', '2024-10-28 02:53:14');

-- --------------------------------------------------------

--
-- Table structure for table `packages`
--

CREATE TABLE `packages` (
  `id` int(6) UNSIGNED NOT NULL,
  `package_name` varchar(100) NOT NULL,
  `package_price` decimal(10,2) NOT NULL,
  `package_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `packages`
--

INSERT INTO `packages` (`id`, `package_name`, `package_price`, `package_type`) VALUES
(11, 'premium', 1000.00, 'photo');

-- --------------------------------------------------------

--
-- Table structure for table `package_contents`
--

CREATE TABLE `package_contents` (
  `id` int(6) UNSIGNED NOT NULL,
  `package_id` int(6) UNSIGNED NOT NULL,
  `content` text NOT NULL,
  `description` text DEFAULT NULL,
  `content_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `package_contents`
--

INSERT INTO `package_contents` (`id`, `package_id`, `content`, `description`, `content_price`) VALUES
(52, 11, 'samplecontent', NULL, NULL),
(53, 11, 'samplecontent3', NULL, NULL),
(54, 11, 'samplecontent4', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(6) UNSIGNED NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `otp` varchar(6) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `contact_no` varchar(15) NOT NULL,
  `valid_id` varchar(255) NOT NULL,
  `profile_picture` varchar(255) NOT NULL,
  `reg_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `email`, `otp`, `password`, `address`, `contact_no`, `valid_id`, `profile_picture`, `reg_date`, `status`) VALUES
(21, 'jabar javier', 'javar.javier@yahoo.com', NULL, '$2y$10$yrR3L/3jl9Gx6g...5ajDOmh4l6OKVkK4HoL4CSZzNT.le5EZ/0Oi', 'myaddress', '09069630154', 'uploads/valid_ids/1024px-UMID_EMV_sample-1024x655.jpg', 'uploads/profile_pictures/1024px-UMID_EMV_sample-1024x655 - Copy.jpg', '2024-10-25 12:20:57', 'approved'),
(22, 'sample name', 'sample@gmail.com', NULL, '$2y$10$CjStdcepRlZPA.Kf1Zq4autGVQtnHZJqUsFZ14mvMBe98JngTsGZa', 'asdasd', '09069630154', 'uploads/valid_ids/1024px-UMID_EMV_sample-1024x655.jpg', 'uploads/profile_pictures/1024px-UMID_EMV_sample-1024x655 - Copy.jpg', '2024-10-26 00:44:38', 'pending');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `announcements`
--
ALTER TABLE `announcements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `gallery`
--
ALTER TABLE `gallery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `packages`
--
ALTER TABLE `packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `package_contents`
--
ALTER TABLE `package_contents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `package_id` (`package_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `announcements`
--
ALTER TABLE `announcements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `gallery`
--
ALTER TABLE `gallery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `packages`
--
ALTER TABLE `packages`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `package_contents`
--
ALTER TABLE `package_contents`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `package_contents`
--
ALTER TABLE `package_contents`
  ADD CONSTRAINT `package_contents_ibfk_1` FOREIGN KEY (`package_id`) REFERENCES `packages` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
