-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 30, 2025 at 03:01 PM
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
-- Database: `it9_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `log_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `activity` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_log`
--

INSERT INTO `activity_log` (`log_id`, `user_id`, `activity`, `timestamp`) VALUES
(1, 6, 'Logged in', '2025-04-26 10:34:41'),
(2, 6, 'Logged in', '2025-04-26 10:38:55'),
(3, 6, 'Logged in', '2025-04-26 10:41:21'),
(4, 9, 'Logged in', '2025-04-26 10:44:07'),
(5, 9, 'Logged in', '2025-04-26 11:04:21'),
(6, 6, 'Logged in', '2025-04-26 11:07:11'),
(7, 9, 'Logged in', '2025-04-27 08:44:30'),
(8, 6, 'Logged in', '2025-04-27 09:49:04'),
(9, 9, 'Logged in', '2025-04-27 21:21:54'),
(10, 6, 'Logged in', '2025-04-27 21:30:51'),
(11, 6, 'Logged in', '2025-04-27 21:30:58'),
(12, 6, 'Logged in', '2025-04-30 00:41:03'),
(13, 9, 'Logged in', '2025-04-30 00:41:25'),
(14, 6, 'Logged in', '2025-04-30 00:43:26'),
(15, 9, 'Logged in', '2025-04-30 00:43:37'),
(16, 6, 'Logged in', '2025-04-30 04:39:15'),
(17, 6, 'Logged in', '2025-04-30 06:22:14'),
(18, 6, 'Logged in', '2025-04-30 06:23:05'),
(19, 9, 'Logged in', '2025-04-30 06:30:08'),
(20, 9, 'Logged in', '2025-04-30 06:39:05'),
(21, 9, 'Logged in', '2025-04-30 06:40:57'),
(22, 9, 'Logged in', '2025-04-30 06:41:54'),
(23, 9, 'Logged in', '2025-04-30 06:42:07'),
(24, 9, 'Logged in', '2025-04-30 06:43:57'),
(25, 9, 'Logged in', '2025-04-30 06:45:11'),
(26, 9, 'Logged in', '2025-04-30 06:46:40');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `order_number` varchar(50) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `order_status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`id`, `user_id`, `product_id`, `quantity`, `order_date`, `order_number`, `price`, `total`, `order_status`) VALUES
(1, 6, 1, 4, '2025-04-07 22:39:50', 'ORD-6-1744036790', NULL, 56.00, NULL),
(2, 6, 2, 3, '2025-04-07 22:42:50', 'ORD-6-1744036970', NULL, 17.97, NULL),
(3, 6, 1, 6, '2025-04-22 14:01:14', 'ORD-6-1745301674', NULL, 84.00, NULL),
(4, 6, 1, 6, '2025-04-27 00:24:51', 'ORD-6-1745684691', NULL, 84.00, NULL),
(5, 6, 1, 9, '2025-04-27 00:31:55', 'ORD-6-1745685115', NULL, 126.00, 'Delivering'),
(6, 6, 1, 7, '2025-04-30 20:23:25', 'ORD-6-1746015804', NULL, 98.00, NULL),
(7, 6, 1, 1, '2025-04-30 20:24:16', 'ORD-6-1746015856', NULL, 14.00, NULL),
(8, 6, 1, 2, '2025-04-30 20:27:02', 'ORD-6-1746016022', 14.00, 28.00, 'Delivering');

-- --------------------------------------------------------

--
-- Table structure for table `order_tracking`
--

CREATE TABLE `order_tracking` (
  `track_id` int(11) NOT NULL,
  `order_number` varchar(100) NOT NULL,
  `status` enum('Pending','Delivering','Delivered') NOT NULL DEFAULT 'Pending',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_by` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_tracking`
--

INSERT INTO `order_tracking` (`track_id`, `order_number`, `status`, `updated_at`, `updated_by`, `remarks`) VALUES
(1, 'ORD-6-1746016022', 'Pending', '2025-04-30 12:27:02', NULL, NULL),
(2, 'ORD-6-1746016022', 'Delivering', '2025-04-30 12:50:29', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `category` varchar(50) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `archived` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `price`, `archived`) VALUES
(1, 'Fried Chicken', 'Entrees', 14.00, 0),
(2, 'Caesar Salad', 'Sides', 5.99, 0),
(3, 'Chocolate Cake', 'Desserts', 6.99, 0),
(4, 'Coke', 'Beverages', 1.99, 0),
(5, 'Kids Burger', 'Kids Menu', 4.99, 0),
(6, 'Special Steak', 'Specials', 19.99, 0),
(7, 'Grilled Chicken Breast', 'Entrees', 7.99, 0),
(8, 'Beef Steak', 'Entrees', 12.99, 0),
(9, 'Spaghetti Carbonara', 'Entrees', 9.99, 0),
(10, 'Crispy Fried Chicken', 'Entrees', 8.49, 0),
(11, 'Salmon Fillet', 'Entrees', 14.99, 0),
(12, 'Pork Chop', 'Entrees', 10.99, 0),
(13, 'Teriyaki Chicken', 'Entrees', 9.49, 0),
(14, 'Roast Beef', 'Entrees', 13.99, 0),
(15, 'Shrimp Scampi', 'Entrees', 11.99, 0),
(16, 'Vegetarian Stir-fry', 'Entrees', 8.99, 0),
(17, 'BBQ Chicken', 'Entrees', 10.49, 0),
(18, 'Fish and Chips', 'Entrees', 9.49, 0),
(19, 'Chicken Parmesan', 'Entrees', 11.99, 0),
(20, 'Lasagna', 'Entrees', 12.49, 0),
(21, 'Beef Stroganoff', 'Entrees', 13.49, 0),
(22, 'Mushroom Risotto', 'Entrees', 10.99, 0),
(23, 'Penne Alfredo', 'Entrees', 9.99, 0),
(24, 'Honey Garlic Pork', 'Entrees', 11.49, 0),
(25, 'Chicken Adobo', 'Entrees', 8.99, 0),
(26, 'Tuna Steak', 'Entrees', 14.99, 0),
(27, 'Caesar Salad', 'Sides', 5.99, 0),
(28, 'French Fries', 'Sides', 3.99, 0),
(29, 'Garlic Bread', 'Sides', 4.49, 0),
(30, 'Mashed Potatoes', 'Sides', 4.99, 0),
(31, 'Coleslaw', 'Sides', 3.49, 0),
(32, 'Steamed Vegetables', 'Sides', 4.99, 0),
(33, 'Baked Beans', 'Sides', 3.99, 0),
(34, 'Cheesy Corn', 'Sides', 3.99, 0),
(35, 'Onion Rings', 'Sides', 4.49, 0),
(36, 'Garden Salad', 'Sides', 5.49, 0),
(37, 'Potato Wedges', 'Sides', 3.99, 0),
(38, 'Bruschetta', 'Sides', 5.49, 0),
(39, 'Sweet Potato Fries', 'Sides', 4.49, 0),
(40, 'Mozzarella Sticks', 'Sides', 5.99, 0),
(41, 'Macaroni Salad', 'Sides', 4.99, 0),
(42, 'Deviled Eggs', 'Sides', 3.99, 0),
(43, 'Stuffed Mushrooms', 'Sides', 6.49, 0),
(44, 'Pickles', 'Sides', 3.49, 0),
(45, 'Cornbread', 'Sides', 3.99, 0),
(46, 'Crispy Tofu Bites', 'Sides', 4.49, 0),
(47, 'Chocolate Lava Cake', 'Desserts', 6.99, 0),
(48, 'Blueberry Cheesecake', 'Desserts', 5.99, 0),
(49, 'Ice Cream Sundae', 'Desserts', 4.99, 0),
(50, 'Apple Pie', 'Desserts', 5.49, 0),
(51, 'Tiramisu', 'Desserts', 6.49, 0),
(52, 'Banana Split', 'Desserts', 5.99, 0),
(53, 'Carrot Cake', 'Desserts', 5.49, 0),
(54, 'Brownie Delight', 'Desserts', 4.99, 0),
(55, 'Mango Float', 'Desserts', 5.99, 0),
(56, 'Chocolate Mousse', 'Desserts', 5.49, 0),
(57, 'Strawberry Shortcake', 'Desserts', 6.49, 0),
(58, 'Creme Brulee', 'Desserts', 6.99, 0),
(59, 'Panna Cotta', 'Desserts', 5.99, 0),
(60, 'Lemon Tart', 'Desserts', 5.49, 0),
(61, 'Fruit Salad', 'Desserts', 4.99, 0),
(62, 'Coconut Macaroons', 'Desserts', 4.49, 0),
(63, 'Donuts', 'Desserts', 3.99, 0),
(64, 'Cupcakes', 'Desserts', 3.99, 0),
(65, 'Churros', 'Desserts', 4.49, 0),
(66, 'Mochi Ice Cream', 'Desserts', 5.49, 0),
(67, 'Lemon Iced Tea', 'Beverages', 2.99, 0),
(68, 'Mango Smoothie', 'Beverages', 3.99, 0),
(69, 'Cold Brew Coffee', 'Beverages', 4.49, 0),
(70, 'Strawberry Milkshake', 'Beverages', 3.49, 0),
(71, 'Hot Chocolate', 'Beverages', 3.99, 0),
(72, 'Espresso', 'Beverages', 2.49, 0),
(73, 'Green Tea', 'Beverages', 2.99, 0),
(74, 'Lemonade', 'Beverages', 3.49, 0),
(75, 'Cappuccino', 'Beverages', 4.99, 0),
(76, 'Iced Americano', 'Beverages', 3.99, 0),
(77, 'Mocha Frappe', 'Beverages', 4.99, 0),
(78, 'Vanilla Shake', 'Beverages', 3.99, 0),
(79, 'Watermelon Juice', 'Beverages', 3.49, 0),
(80, 'Orange Juice', 'Beverages', 3.99, 0),
(81, 'Pineapple Shake', 'Beverages', 3.49, 0),
(82, 'Peach Iced Tea', 'Beverages', 2.99, 0),
(83, 'Milk Tea', 'Beverages', 4.49, 0),
(84, 'Coconut Water', 'Beverages', 3.49, 0),
(85, 'Soda', 'Beverages', 1.99, 0),
(86, 'Energy Drink', 'Beverages', 3.99, 0),
(87, 'BBQ Ribs Special', 'Specials', 15.99, 0),
(88, 'Seafood Paella', 'Specials', 18.99, 0),
(89, 'Lobster Thermidor', 'Specials', 24.99, 0),
(90, 'Truffle Pasta', 'Specials', 16.99, 0),
(91, 'Wagyu Steak', 'Specials', 29.99, 0),
(92, 'Oyster Rockefeller', 'Specials', 14.99, 0),
(93, 'Gourmet Burger', 'Specials', 12.99, 0),
(94, 'Duck Confit', 'Specials', 22.99, 0),
(95, 'Beef Wellington', 'Specials', 26.99, 0),
(96, 'Grilled Lamb Chops', 'Specials', 19.99, 0),
(97, 'Peking Duck', 'Specials', 23.99, 0),
(98, 'Seafood Platter', 'Specials', 27.99, 0),
(99, 'Stuffed Lobster', 'Specials', 25.99, 0),
(100, 'Sushi Omakase', 'Specials', 21.99, 0),
(101, 'Gourmet Pizza', 'Specials', 17.99, 0),
(102, 'Pasta Primavera', 'Specials', 15.49, 0),
(103, 'Blackened Salmon', 'Specials', 20.99, 0),
(104, 'Veal Marsala', 'Specials', 19.49, 0),
(105, 'Coq au Vin', 'Specials', 18.99, 0),
(106, 'Butter Chicken', 'Specials', 16.49, 0),
(107, 'Chicken Nuggets', 'Kids Menu', 5.99, 0),
(108, 'Mac and Cheese', 'Kids Menu', 6.49, 0),
(109, 'Mini Pizza', 'Kids Menu', 5.99, 0),
(110, 'Hotdog and Fries', 'Kids Menu', 4.99, 0),
(111, 'Peanut Butter & Jelly Sandwich', 'Kids Menu', 3.99, 0),
(112, 'Mini Pancakes', 'Kids Menu', 4.99, 0),
(113, 'Fish Sticks', 'Kids Menu', 5.49, 0),
(114, 'Cheese Quesadilla', 'Kids Menu', 4.99, 0),
(115, 'Fruit Yogurt', 'Kids Menu', 3.99, 0),
(116, 'Chocolate Chip Cookies', 'Kids Menu', 3.49, 0),
(117, 'Chicken Tenders', 'Kids Menu', 6.49, 0),
(118, 'Grilled Cheese Sandwich', 'Kids Menu', 5.49, 0),
(119, 'Mini Burger', 'Kids Menu', 6.49, 0),
(120, 'Banana Muffins', 'Kids Menu', 3.99, 0),
(121, 'French Toast Sticks', 'Kids Menu', 4.99, 0),
(122, 'Carrot Sticks with Hummus', 'Kids Menu', 3.99, 0),
(123, 'Apple Slices with Peanut Butter', 'Kids Menu', 3.49, 0),
(124, 'Mini Waffles', 'Kids Menu', 4.99, 0),
(125, 'Tater Tots', 'Kids Menu', 3.99, 0),
(126, 'Pudding Cup', 'Kids Menu', 3.49, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `gender` enum('male','female','other') NOT NULL,
  `contact` varchar(15) NOT NULL,
  `country` varchar(50) NOT NULL,
  `user_name` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `verification_code` varchar(6) DEFAULT NULL,
  `is_admin` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `birthdate`, `gender`, `contact`, `country`, `user_name`, `password`, `verification_code`, `is_admin`) VALUES
(5, 'Nhor', 'Ranain', 'nhor.ranain@gmail.com', '2005-11-28', 'male', '09610477234', 'Philippines', 'nhordacubes01', 'nhor0', '434519', 0),
(6, 'James', 'Omosay', 'james.omosay@gmail.com', '2005-01-01', 'male', '0912345678', 'Philippines', 'james1234', 'james1234', NULL, 0),
(9, 'Nhor_admin', 'Ranain', 'nhory.ranain@gmail.com', '2005-11-28', 'male', '09610477234', 'Philippines', 'nhor_admin1234', 'nhor1234', NULL, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`log_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_number` (`order_number`);

--
-- Indexes for table `order_tracking`
--
ALTER TABLE `order_tracking`
  ADD PRIMARY KEY (`track_id`),
  ADD KEY `order_number` (`order_number`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `user_name` (`user_name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `log_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `order_tracking`
--
ALTER TABLE `order_tracking`
  MODIFY `track_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_tracking`
--
ALTER TABLE `order_tracking`
  ADD CONSTRAINT `order_tracking_ibfk_1` FOREIGN KEY (`order_number`) REFERENCES `history` (`order_number`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
