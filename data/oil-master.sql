-- phpMyAdmin SQL Dump
-- version 4.9.5deb1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 14, 2020 at 10:48 AM
-- Server version: 5.7.31
-- PHP Version: 7.3.20-1+ubuntu19.10.1+deb.sury.org+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `oil-master`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) DEFAULT NULL,
  `category_slug` varchar(255) DEFAULT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `category_status` varchar(255) NOT NULL DEFAULT 'inactive' COMMENT 'active, inactive',
  `created_on` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`, `category_slug`, `description`, `image`, `parent_id`, `category_status`, `created_on`, `created_by`, `modified_on`, `modified_by`) VALUES
(1, '4T 1L', '4t-1l', 'This is the 4T 1litter oil', NULL, 0, 'active', '2020-05-12 13:33:30', 1, NULL, NULL),
(4, '4T 500litter', '4t-500litter', 'This is the another 500L 4T oil', NULL, 1, 'active', '2020-05-12 14:25:40', 1, '2020-05-12 14:27:24', 1),
(5, 'Car Cooland', 'car-cooland', 'Cooland oils ', NULL, 0, 'active', '2020-09-07 22:40:14', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `serial_no` text,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `alternate_phone` varchar(255) DEFAULT NULL,
  `church_name` varchar(255) DEFAULT NULL,
  `street_address` varchar(255) DEFAULT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `pincode` varchar(255) DEFAULT NULL,
  `recceiver_name` varchar(255) DEFAULT NULL,
  `payment_status` varchar(255) DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL,
  `added_on` datetime DEFAULT NULL,
  `customer_status` varchar(255) DEFAULT NULL,
  `message` text,
  `message_status` varchar(255) DEFAULT NULL,
  `send_by` int(11) DEFAULT NULL,
  `send_on` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `serial_no`, `first_name`, `last_name`, `gender`, `email`, `phone`, `alternate_phone`, `church_name`, `street_address`, `address_line2`, `city`, `state`, `pincode`, `recceiver_name`, `payment_status`, `added_by`, `added_on`, `customer_status`, `message`, `message_status`, `send_by`, `send_on`) VALUES
(1, '2020-21/0513/001', 'Kellie', 'Vega', 'male', 'tenedij@mailinator.com', '9512368740', '', NULL, 'Corporis voluptate q', 'Facere ea minim ut a', 'Ut velit Nam dolorem', '27', 'Architecto officia p', 'Camille Vasquez', NULL, 1, '2020-05-13 16:19:20', 'active', NULL, NULL, NULL, NULL),
(2, '2020-21/0513/002', 'Willow', 'Knight', 'female', 'woriduji@mailinator.com', '9632587410', '', NULL, 'Tempore adipisicing', 'Magna amet pariatur', 'Id excepturi eum la', '15', 'Et cupiditate molest', 'Fulton Navarro', NULL, 1, '2020-05-13 16:22:10', 'inactive', NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `customer_zone_map`
--

CREATE TABLE `customer_zone_map` (
  `zone_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customer_zone_map`
--

INSERT INTO `customer_zone_map` (`zone_id`, `customer_id`) VALUES
(2, 1),
(2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `privileges`
--

CREATE TABLE `privileges` (
  `resource_id` varchar(255) NOT NULL,
  `privilege_name` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `privileges`
--

INSERT INTO `privileges` (`resource_id`, `privilege_name`, `display_name`) VALUES
('Admin\\Controller\\Category', 'add', 'Add Category'),
('Admin\\Controller\\Category', 'change-status', 'Change status of the category'),
('Admin\\Controller\\Category', 'edit', 'Modify Category'),
('Admin\\Controller\\Category', 'index', 'Access'),
('Admin\\Controller\\Customer', 'add', 'Add'),
('Admin\\Controller\\Customer', 'edit', 'Edit'),
('Admin\\Controller\\Customer', 'index', 'Access'),
('Admin\\Controller\\Index', 'index', 'Access'),
('Admin\\Controller\\Product', 'add', 'Add'),
('Admin\\Controller\\Product', 'change-status', 'Change Status'),
('Admin\\Controller\\Product', 'edit', 'Edit'),
('Admin\\Controller\\Product', 'index', 'Access'),
('Admin\\Controller\\Role', 'add', 'Add'),
('Admin\\Controller\\Role', 'edit', 'Edit'),
('Admin\\Controller\\Role', 'index', 'Access'),
('Admin\\Controller\\User', 'add', 'Add'),
('Admin\\Controller\\User', 'edit', 'Edit'),
('Admin\\Controller\\User', 'edit-profile', 'Edit User Profile'),
('Admin\\Controller\\User', 'index', 'Access'),
('Admin\\Controller\\Zone', 'add', 'Add Zone'),
('Admin\\Controller\\Zone', 'change-status', 'Change status of the Zone'),
('Admin\\Controller\\Zone', 'edit', 'Modify Zone'),
('Admin\\Controller\\Zone', 'index', 'Access');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` text,
  `purchase_price` varchar(50) DEFAULT NULL,
  `total_qty` int(11) DEFAULT NULL,
  `tax` varchar(255) DEFAULT NULL,
  `hsn` varchar(255) DEFAULT NULL,
  `sku` varchar(255) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `option_colour` varchar(255) DEFAULT NULL,
  `option_grade` varchar(255) DEFAULT NULL,
  `option_type` varchar(255) DEFAULT NULL,
  `remainder_qty` int(11) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `dealer_price` varchar(50) DEFAULT NULL,
  `mrp_price` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `product_qty_map`
--

CREATE TABLE `product_qty_map` (
  `product_id` int(11) NOT NULL,
  `qty_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `qty_details`
--

CREATE TABLE `qty_details` (
  `qty_id` int(11) NOT NULL,
  `qty_name` varchar(255) DEFAULT NULL,
  `qty_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `resource_id` varchar(255) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `resources`
--

INSERT INTO `resources` (`resource_id`, `display_name`) VALUES
('Admin\\Controller\\Category', 'Manage Category'),
('Admin\\Controller\\Customer', 'Manage Customer'),
('Admin\\Controller\\Index', 'Manage Dashboard'),
('Admin\\Controller\\Product', 'Manage Products'),
('Admin\\Controller\\Role', 'Manage Roles'),
('Admin\\Controller\\User', 'Manage Users'),
('Admin\\Controller\\Zone', 'Manage Zone');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(255) DEFAULT NULL,
  `role_code` varchar(255) DEFAULT NULL,
  `description` text,
  `status` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`role_id`, `role_name`, `role_code`, `description`, `status`) VALUES
(1, 'Admin', 'ADM', 'Has full access', 'active'),
(2, 'Workers', 'WRK', 'Test description', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE `state` (
  `state_id` varchar(255) NOT NULL,
  `state_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`state_id`, `state_name`) VALUES
('01', 'Jammu & Kashmir'),
('02', 'Himachal Pradesh'),
('03', 'Punjab'),
('04', 'Chandigarh'),
('05', 'Uttrakhand'),
('06', 'Haryana'),
('07', 'Delhi'),
('08', 'Rajasthan'),
('09', 'Uttar Pradesh'),
('10', 'Bihar'),
('11', 'Sikkim'),
('12', 'Arunachal Pradesh'),
('13', 'Nagaland'),
('14', 'Manipur'),
('15', 'Mizoram'),
('16', 'Tripura'),
('17', 'Meghalaya'),
('18', 'Assam'),
('19', 'West Bengal'),
('20', 'Jharkhand'),
('21', 'Orissa'),
('22', 'Chattisgarh'),
('23', 'Madhya Pradesh'),
('24', 'Gujarat'),
('25', 'Daman & Diu'),
('26', 'Dadra & Nagar Haveli'),
('27', 'Maharashtra'),
('28', 'Andhra Pradesh'),
('29', 'Karnataka'),
('30', 'Goa'),
('31', 'Lakswadeep'),
('32', 'Kerala'),
('33', 'Tamil Nadu'),
('34', 'Pondicherry'),
('35', 'Andaman & Nicobar Islands'),
('36', 'Telangana');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `alternate_email` varchar(255) DEFAULT NULL,
  `address` text,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `contact_no` varchar(255) DEFAULT NULL,
  `alternate_contact` varchar(255) DEFAULT NULL,
  `role` int(11) NOT NULL,
  `mail_to` varchar(255) DEFAULT NULL,
  `user_status` varchar(255) DEFAULT NULL,
  `added_on` datetime DEFAULT NULL,
  `added_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `user_name`, `gender`, `alternate_email`, `address`, `email`, `password`, `contact_no`, `alternate_contact`, `role`, `mail_to`, `user_status`, `added_on`, `added_by`) VALUES
(1, 'admin', 'male', 'thanaseelan@deforay.com', '4-3/35, North charch street, Sambavarvadakarai, Kadayanallur Taluk, Tirunelveli District - 627856', 'thanaseelan@deforay.com', '3cb752cc82b28ed86ff9fd2056f4316f6de4a461', '9994027557', '9994563210', 1, NULL, 'active', '2015-08-28 05:27:36', 0),
(4, 'Thanaseelan', 'male', 'seelan203@gmail.com', 'South street, Chennai', 'seelan203@gmail.com', '3cb752cc82b28ed86ff9fd2056f4316f6de4a461', '9512368740', '9512648730', 2, NULL, 'active', '2019-08-25 21:22:03', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_product_map`
--

CREATE TABLE `user_product_map` (
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `user_zone_map`
--

CREATE TABLE `user_zone_map` (
  `zone_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user_zone_map`
--

INSERT INTO `user_zone_map` (`zone_id`, `user_id`) VALUES
(1, 1),
(2, 1),
(2, 4);

-- --------------------------------------------------------

--
-- Table structure for table `zone`
--

CREATE TABLE `zone` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `image` varchar(50) DEFAULT NULL,
  `parent_zone` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `created_on` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_on` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `zone`
--

INSERT INTO `zone` (`id`, `name`, `slug`, `description`, `image`, `parent_zone`, `status`, `created_on`, `created_by`, `modified_on`, `modified_by`) VALUES
(1, 'Tenkasi', 'tenkasi', 'Tenkasi area desc', NULL, 0, 'active', '2020-05-12 19:23:26', 1, '2020-05-13 16:50:24', 1),
(2, 'Surandai', 'surandai', 'Surandai area desc', NULL, 1, 'active', '2020-05-12 19:47:43', 1, '2020-05-14 17:45:05', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `customer_zone_map`
--
ALTER TABLE `customer_zone_map`
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `zone_id` (`zone_id`);

--
-- Indexes for table `privileges`
--
ALTER TABLE `privileges`
  ADD PRIMARY KEY (`resource_id`,`privilege_name`);

--
-- Indexes for table `product_qty_map`
--
ALTER TABLE `product_qty_map`
  ADD KEY `product` (`product_id`),
  ADD KEY `qty` (`qty_id`);

--
-- Indexes for table `qty_details`
--
ALTER TABLE `qty_details`
  ADD PRIMARY KEY (`qty_id`);

--
-- Indexes for table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`resource_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`role_id`);

--
-- Indexes for table `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`state_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `role` (`role`);

--
-- Indexes for table `user_product_map`
--
ALTER TABLE `user_product_map`
  ADD KEY `user` (`user_id`),
  ADD KEY `product_user` (`product_id`);

--
-- Indexes for table `user_zone_map`
--
ALTER TABLE `user_zone_map`
  ADD KEY `user_id` (`user_id`),
  ADD KEY `zone_id` (`zone_id`);

--
-- Indexes for table `zone`
--
ALTER TABLE `zone`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `qty_details`
--
ALTER TABLE `qty_details`
  MODIFY `qty_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `zone`
--
ALTER TABLE `zone`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer`
--
ALTER TABLE `customer`
  ADD CONSTRAINT `customer_ibfk_1` FOREIGN KEY (`added_by`) REFERENCES `user` (`user_id`);

--
-- Constraints for table `customer_zone_map`
--
ALTER TABLE `customer_zone_map`
  ADD CONSTRAINT `customer_zone_map_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`),
  ADD CONSTRAINT `customer_zone_map_ibfk_2` FOREIGN KEY (`zone_id`) REFERENCES `zone` (`id`);

--
-- Constraints for table `privileges`
--
ALTER TABLE `privileges`
  ADD CONSTRAINT `privileges_ibfk_1` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`resource_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
