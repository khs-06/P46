-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 09, 2025 at 04:20 AM
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
-- Database: `findbrick-real-estate`
--

-- --------------------------------------------------------

--
-- Table structure for table `about`
--

CREATE TABLE `about` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `about`
--

INSERT INTO `about` (`id`, `title`, `description`, `image`) VALUES
(1, 'Our Company', '<p>FindBricks is committed to providing the best real estate experience for buyers, sellers, and renters. We offer a wide range of properties and dedicated support for all your needs.</p>', '29.jpg'),
(4, 'our broker', '<p>Best brokering, and futured property for our client</p>', '44.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `aid` int(10) NOT NULL,
  `auser` varchar(50) NOT NULL,
  `aemail` varchar(50) NOT NULL,
  `apass` varchar(255) NOT NULL,
  `adob` date NOT NULL,
  `aphone` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`aid`, `auser`, `aemail`, `apass`, `adob`, `aphone`) VALUES
(1, 'admin1', 'admin1@example.com', 'password123', '1980-01-01', '1234567890'),
(2, 'admin2', 'admin2@example.com', 'password456', '1990-02-02', '0987654321');

-- --------------------------------------------------------

--
-- Table structure for table `agent`
--

CREATE TABLE `agent` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `utype` varchar(20) DEFAULT 'agent',
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `specialty` varchar(100) DEFAULT 'Property specialist',
  `city` varchar(100) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `facebook` varchar(255) DEFAULT 'https://facebook.com',
  `twitter` varchar(255) DEFAULT 'https://twitter.com',
  `linkedin` varchar(255) DEFAULT 'https://linkedin.com/in',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `agent`
--

INSERT INTO `agent` (`id`, `user_id`, `name`, `utype`, `phone`, `email`, `image`, `specialty`, `city`, `bio`, `facebook`, `twitter`, `linkedin`, `created_at`) VALUES
(3, 5, 'Kabir', '	\nbuilder', '4444444444', 'findbrick26@gmail.com', 'uploads/users/user_68c15bb4c21bc6.02844311.jpg', 'Property specialist', NULL, NULL, 'https://facebook.com', 'https://twitter.com', 'https://linkedin.com/in', '2025-09-10 11:06:34'),
(4, 9, 'Rumit', 'agent', '1111111111', 'rumitsolanki09@gmail.com', 'uploads/users/user_68ed438f6d1f06.79472090.jpg', 'Property specialist', NULL, NULL, 'https://facebook.com', 'https://twitter.com', 'https://linkedin.com/in', '2025-10-13 18:23:16');

-- --------------------------------------------------------

--
-- Table structure for table `city`
--

CREATE TABLE `city` (
  `cid` int(50) NOT NULL,
  `cname` varchar(100) NOT NULL,
  `sid` int(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `city`
--

INSERT INTO `city` (`cid`, `cname`, `sid`) VALUES
(1, 'Surat', 1),
(2, 'navi mumbai', 2),
(3, 'konni', 3),
(4, 'ludhiana', 4),
(5, 'guwahati', 5);

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`id`, `name`, `email`, `phone`, `subject`, `message`, `created_at`) VALUES
(1, 'John Doe', 'john.doe@example.com', '+1234567890', 'Inquiry about apartment', 'I would like to know more about the apartment listed on your site.', '2025-08-23 18:04:05'),
(2, 'Jane Smith', 'jane.smith@example.com', '+1987654321', 'Site Feedback', 'The site is very user-friendly. Keep up the good work!', '2025-08-23 18:04:05');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `uid` int(10) UNSIGNED NOT NULL,
  `fdescription` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `uid`, `fdescription`, `status`, `created_at`) VALUES
(1, 10, 'test agent', 0, '2025-09-02 04:29:04'),
(2, 5, 'Great experience with the agent, found my dream home!', 1, '2025-08-31 16:26:48'),
(3, 7, 'Website is easy to use, but property details could be clearer.', 0, '2025-08-31 16:26:48'),
(4, 9, 'Agent was very helpful and responsive during my search.', 1, '2025-08-31 16:26:48');

-- --------------------------------------------------------

--
-- Table structure for table `property`
--

CREATE TABLE `property` (
  `pid` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `ptype` varchar(50) NOT NULL,
  `stype` varchar(50) NOT NULL,
  `price_type` varchar(10) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `bed` int(11) NOT NULL,
  `bath` int(11) NOT NULL,
  `kitc` int(11) NOT NULL,
  `bhk` varchar(50) NOT NULL,
  `balcony` int(11) NOT NULL,
  `hall` int(11) NOT NULL,
  `totalfloor` int(11) NOT NULL,
  `floorcount` varchar(50) NOT NULL,
  `loc` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `asize` varchar(50) NOT NULL,
  `feature` longtext DEFAULT NULL,
  `pimage` varchar(255) NOT NULL,
  `pimage1` varchar(255) NOT NULL,
  `pimage2` varchar(255) NOT NULL,
  `pimage3` varchar(255) NOT NULL,
  `pimage4` varchar(255) NOT NULL,
  `pimage5` varchar(255) DEFAULT NULL,
  `pimage6` varchar(255) DEFAULT NULL,
  `groundimage` varchar(255) DEFAULT NULL,
  `otherimage` varchar(255) DEFAULT NULL,
  `status` varchar(30) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `isFeatured` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `property`
--

INSERT INTO `property` (`pid`, `title`, `ptype`, `stype`, `price_type`, `price`, `bed`, `bath`, `kitc`, `bhk`, `balcony`, `hall`, `totalfloor`, `floorcount`, `loc`, `city`, `state`, `asize`, `feature`, `pimage`, `pimage1`, `pimage2`, `pimage3`, `pimage4`, `pimage5`, `pimage6`, `groundimage`, `otherimage`, `status`, `uid`, `agent_id`, `isFeatured`, `description`, `created_at`, `updated_at`) VALUES
(6, 'test1', 'flat', 'rent', 'lakh', 25.30, 3, 3, 1, '3 BHK', 2, 1, 1, '3 floor', 'main road', 'Surat', 'Gujrat', '200sqf', '<!---feature area start--->\r\n<ul>\r\n<li>Property Age : 5 Years</li>\r\n<li>Swiming Pool : No</li>\r\n<li>Parking : Yes</li>\r\n<li>GYM : Yes</li>\r\n</ul>\r\n<ul>\r\n<li>Type : Apartment</li>\r\n<li>Security : Yes</li>\r\n<li>Dining Capacity : 6 People</li>\r\n<li>Church/Temple : Yes</li>\r\n</ul>\r\n<ul>\r\n<li>3rd Party : No</li>\r\n<li>Elevator : Yes</li>\r\n<li>CCTV : Yes</li>\r\n<li>Water Supply : Ground Water / Tank</li>\r\n</ul>\r\n<!---feature area end--->\r\n<p>&nbsp;</p>', 'uploads/properties/1760375247_68ed31cf2e540.jpg', 'uploads/properties/1760375247_68ed31cf32522.jpg', 'uploads/properties/1760375247_68ed31cf32e86.jpg', 'uploads/properties/1760375247_68ed31cf33dd8.jpg', 'uploads/properties/1760375247_68ed31cf354af.jpg', NULL, 'uploads/properties/1760375247_68ed31cf36a35.jpg', 'uploads/properties/1760375247_68ed31cf38146.jpg', NULL, 'available', 5, 3, 0, '<p>test&nbsp;</p>', '2025-10-13 17:07:27', '2025-11-05 04:56:10'),
(7, 'Maple Grove', 'house', 'sale', 'lakh', 34.20, 3, 3, 1, '3 BHK', 2, 2, 1, '1 floor', 'main road', 'Surat', 'Gujrat', '1500sqft', '<!---feature area start--->\r\n<ul>\r\n<li>Property Age : 10 Years</li>\r\n<li>Swiming Pool : Yes</li>\r\n<li>Parking : Yes</li>\r\n<li>GYM : Yes</li>\r\n</ul>\r\n<ul>\r\n<li>Type : Apartment</li>\r\n<li>Security : Yes</li>\r\n<li>Dining Capacity : 10 People</li>\r\n<li>Church/Temple : No</li>\r\n</ul>\r\n<ul>\r\n<li>3rd Party : No</li>\r\n<li>Elevator : Yes</li>\r\n<li>CCTV : Yes</li>\r\n<li>Water Supply : Ground Water / Tank</li>\r\n</ul>\r\n<!---feature area end--->\r\n<p>&nbsp;</p>', 'uploads/properties/1762276614_690a35068853e.jpg', 'uploads/properties/1762276614_690a35068c1f0.jpg', 'uploads/properties/1762276614_690a35068cb54.jpg', 'uploads/properties/1762276614_690a35068d47d.jpg', 'uploads/properties/1762276614_690a35068e5a1.jpg', NULL, NULL, 'uploads/properties/1762276614_690a35068f33f.jpg', NULL, 'available', 9, 4, 0, '<p>test</p>', '2025-11-04 17:16:54', '2025-11-05 04:56:24'),
(8, 'Amberstone', 'villa', 'sale', 'cr', 3.42, 6, 6, 2, '4 BHK', 3, 2, 2, '1 floor', 'main road', 'navi mumbai', 'Maharashtra', '2000sqft', '<!---feature area start--->\r\n<ul>\r\n<li>Property Age : 10 Years</li>\r\n<li>Swiming Pool : Yes</li>\r\n<li>Parking : Yes</li>\r\n<li>GYM : Yes</li>\r\n</ul>\r\n<ul>\r\n<li>Type : Apartment</li>\r\n<li>Security : Yes</li>\r\n<li>Dining Capacity : 10 People</li>\r\n<li>Church/Temple : No</li>\r\n</ul>\r\n<ul>\r\n<li>3rd Party : No</li>\r\n<li>Elevator : Yes</li>\r\n<li>CCTV : Yes</li>\r\n<li>Water Supply : Ground Water / Tank</li>\r\n</ul>\r\n<!---feature area end--->\r\n<p>&nbsp;</p>', 'uploads/properties/1762276751_690a358fe0736.jpg', 'uploads/properties/1762276751_690a358fe19df.jpg', 'uploads/properties/1762276751_690a358fe2348.jpg', 'uploads/properties/1762276751_690a358fe2adb.jpg', 'uploads/properties/1762276751_690a358fe3324.jpg', NULL, 'uploads/properties/1762276751_690a358fe4121.jpg', 'uploads/properties/1762276751_690a358fe4c69.jpg', NULL, 'available', 9, 4, 0, '<p>test</p>', '2025-11-04 17:19:11', '2025-11-05 04:56:26'),
(9, 'Windmere', 'apartment', 'sale', 'lakh', 48.70, 2, 2, 2, '1 BHK', 2, 2, 3, '1 floor', 'main road', 'konni', 'Kerala', '1000sqft', '<!---feature area start--->\r\n<ul>\r\n<li>Property Age : 10 Years</li>\r\n<li>Swiming Pool : Yes</li>\r\n<li>Parking : Yes</li>\r\n<li>GYM : Yes</li>\r\n</ul>\r\n<ul>\r\n<li>Type : Apartment</li>\r\n<li>Security : Yes</li>\r\n<li>Dining Capacity : 10 People</li>\r\n<li>Church/Temple : No</li>\r\n</ul>\r\n<ul>\r\n<li>3rd Party : No</li>\r\n<li>Elevator : Yes</li>\r\n<li>CCTV : Yes</li>\r\n<li>Water Supply : Ground Water / Tank</li>\r\n</ul>\r\n<!---feature area end--->\r\n<p>&nbsp;</p>', 'uploads/properties/1762276906_690a362ac0ee2.jpg', 'uploads/properties/1762276906_690a362ac4407.jpg', 'uploads/properties/1762276906_690a362ac5078.jpg', 'uploads/properties/1762276906_690a362ac5bbd.jpg', 'uploads/properties/1762276906_690a362ac65f3.jpg', NULL, 'uploads/properties/1762276906_690a362ac7cfa.jpg', 'uploads/properties/1762276906_690a362ac8e95.jpg', 'uploads/properties/1762276906_690a362aca9dd.jpg', 'Sold', 10, 4, 1, '<p>test</p>', '2025-11-04 17:21:46', '2025-11-05 06:08:16'),
(10, 'Rosehill', 'flat', 'rent', 'k', 38.00, 3, 3, 1, '2 BHK', 1, 1, 2, '2 floor', 'near by main road', 'ludhiana', 'Punjab', '1000sqft', '<!---feature area start--->\r\n<ul>\r\n<li>Property Age : 10 Years</li>\r\n<li>Swiming Pool : Yes</li>\r\n<li>Parking : Yes</li>\r\n<li>GYM : Yes</li>\r\n</ul>\r\n<ul>\r\n<li>Type : Apartment</li>\r\n<li>Security : Yes</li>\r\n<li>Dining Capacity : 10 People</li>\r\n<li>Church/Temple : No</li>\r\n</ul>\r\n<ul>\r\n<li>3rd Party : No</li>\r\n<li>Elevator : Yes</li>\r\n<li>CCTV : Yes</li>\r\n<li>Water Supply : Ground Water / Tank</li>\r\n</ul>\r\n<!---feature area end--->\r\n<p>&nbsp;</p>', 'uploads/properties/1762277068_690a36cc13f02.jpg', 'uploads/properties/1762277068_690a36cc1af9a.jpg', 'uploads/properties/1762277068_690a36cc1bbb9.jpg', 'uploads/properties/1762277068_690a36cc1c272.jpg', 'uploads/properties/1762277068_690a36cc1dca6.jpg', NULL, 'uploads/properties/1762277068_690a36cc1fc18.jpg', NULL, NULL, 'available', 9, 4, 0, '<p>test</p>', '2025-11-04 17:24:28', '2025-11-05 04:56:32'),
(11, 'Hilltop Haven', 'building', 'sale', 'cr', 8.20, 10, 10, 5, '3 BHK', 10, 3, 10, '5 floor', 'main road', 'guwahati', 'Assam', '1200sqft', '<!---feature area start--->\r\n<ul>\r\n<li>Property Age : 10 Years</li>\r\n<li>Swiming Pool : Yes</li>\r\n<li>Parking : Yes</li>\r\n<li>GYM : Yes</li>\r\n</ul>\r\n<ul>\r\n<li>Type : Apartment</li>\r\n<li>Security : Yes</li>\r\n<li>Dining Capacity : 10 People</li>\r\n<li>Church/Temple : No</li>\r\n</ul>\r\n<ul>\r\n<li>3rd Party : No</li>\r\n<li>Elevator : Yes</li>\r\n<li>CCTV : Yes</li>\r\n<li>Water Supply : Ground Water / Tank</li>\r\n</ul>\r\n<!---feature area end--->\r\n<p>&nbsp;</p>', 'uploads/properties/1762277237_690a37751e5db.jpg', 'uploads/properties/1762277237_690a37754453f.jpg', 'uploads/properties/1762277237_690a377544e3f.jpg', 'uploads/properties/1762277237_690a37754537c.jpg', 'uploads/properties/1762277237_690a377545912.jpg', NULL, 'uploads/properties/1762277237_690a377546442.jpg', 'uploads/properties/1762277237_690a3775470c3.jpg', NULL, 'available', 9, 4, 1, '<p>test</p>', '2025-11-04 17:27:17', '2025-11-05 04:56:35'),
(12, 'Eagle’s Rest', 'flat', 'sale', 'lakh', 25.40, 4, 3, 1, '2 BHK', 4, 3, 3, '2 floor', 'Giddangi St, Chinna Bazar', 'Surat', 'Gujrat', '2000sqft', '<!---feature area start--->\r\n<ul>\r\n<li>Property Age : 10 Years</li>\r\n<li>Swiming Pool : Yes</li>\r\n<li>Parking : Yes</li>\r\n<li>GYM : Yes</li>\r\n</ul>\r\n<ul>\r\n<li>Type : Apartment</li>\r\n<li>Security : Yes</li>\r\n<li>Dining Capacity : 10 People</li>\r\n<li>Church/Temple : No</li>\r\n</ul>\r\n<ul>\r\n<li>3rd Party : No</li>\r\n<li>Elevator : Yes</li>\r\n<li>CCTV : Yes</li>\r\n<li>Water Supply : Ground Water / Tank</li>\r\n</ul>\r\n<!---feature area end--->\r\n<p>&nbsp;</p>', 'uploads/properties/1762277630_690a38fe8d89c.jpg', 'uploads/properties/1762277630_690a38feb3d6e.jpg', 'uploads/properties/1762277630_690a38feb44e9.jpg', 'uploads/properties/1762277630_690a38feb4ed7.jpg', 'uploads/properties/1762277630_690a38feb5aee.jpg', NULL, 'uploads/properties/1762277630_690a38feb6542.jpg', 'uploads/properties/1762277630_690a38feb725f.jpg', 'uploads/properties/1762277630_690a38feb830a.jpg', 'available', 5, 3, 1, '<p>test</p>', '2025-11-04 17:33:50', '2025-11-05 04:56:38'),
(13, 'Mannat', 'villa', 'sale', 'cr', 5.23, 3, 4, 1, '3 BHK', 2, 1, 3, '2 floor', 'Maa Laxmi Market, Dr RP Road,', 'guwahati', 'Assam', '3000sqft', '<!---feature area start--->\r\n<ul>\r\n<li>Property Age : 10 Years</li>\r\n<li>Swiming Pool : Yes</li>\r\n<li>Parking : Yes</li>\r\n<li>GYM : Yes</li>\r\n</ul>\r\n<ul>\r\n<li>Type : Apartment</li>\r\n<li>Security : Yes</li>\r\n<li>Dining Capacity : 10 People</li>\r\n<li>Church/Temple : No</li>\r\n</ul>\r\n<ul>\r\n<li>3rd Party : No</li>\r\n<li>Elevator : Yes</li>\r\n<li>CCTV : Yes</li>\r\n<li>Water Supply : Ground Water / Tank</li>\r\n</ul>\r\n<!---feature area end--->\r\n<p>&nbsp;</p>', 'uploads/properties/1762277830_690a39c6dc63e.jpg', 'uploads/properties/1762277831_690a39c72e6ac.jpg', 'uploads/properties/1762277831_690a39c72efb2.jpg', 'uploads/properties/1762277831_690a39c72f4a2.jpg', 'uploads/properties/1762277831_690a39c72feab.jpg', NULL, 'uploads/properties/1762277831_690a39c730818.jpg', 'uploads/properties/1762277831_690a39c7313fd.jpg', NULL, 'available', 5, 3, 1, '<p>test</p>', '2025-11-04 17:37:11', '2025-11-05 04:56:41'),
(14, 'Townville', 'apartment', 'sale', 'lakh', 72.00, 2, 2, 2, '2 BHK', 1, 1, 2, '3 floor', '44/1045, Kaloor, Kochi,', 'konni', 'Kerala', '2300sqft', '<!---feature area start--->\r\n<ul>\r\n<li>Property Age : 10 Years</li>\r\n<li>Swiming Pool : Yes</li>\r\n<li>Parking : Yes</li>\r\n<li>GYM : Yes</li>\r\n</ul>\r\n<ul>\r\n<li>Type : Apartment</li>\r\n<li>Security : Yes</li>\r\n<li>Dining Capacity : 10 People</li>\r\n<li>Church/Temple : No</li>\r\n</ul>\r\n<ul>\r\n<li>3rd Party : No</li>\r\n<li>Elevator : Yes</li>\r\n<li>CCTV : Yes</li>\r\n<li>Water Supply : Ground Water / Tank</li>\r\n</ul>\r\n<!---feature area end--->\r\n<p>&nbsp;</p>', 'uploads/properties/1762278007_690a3a77a0df0.jpg', 'uploads/properties/1762278007_690a3a77cc75b.jpg', 'uploads/properties/1762278007_690a3a77cd1ad.jpg', 'uploads/properties/1762278007_690a3a77cdd91.jpg', 'uploads/properties/1762278007_690a3a77ced7d.jpg', NULL, NULL, 'uploads/properties/1762278007_690a3a77cff18.jpg', 'uploads/properties/1762278007_690a3a77d28e8.jpg', 'available', 5, 3, 0, '<p>test</p>', '2025-11-04 17:40:07', '2025-11-05 04:56:44'),
(15, 'Drake', 'house', 'rent', 'lakh', 54.90, 4, 4, 2, '2 BHK', 2, 1, 2, '2 floor', 'Domoria Pul, Domoria Pul, Near Domoria Pul,', 'ludhiana', 'Punjab', '2000sqft', '<!---feature area start--->\r\n<ul>\r\n<li>Property Age : 10 Years</li>\r\n<li>Swiming Pool : Yes</li>\r\n<li>Parking : Yes</li>\r\n<li>GYM : Yes</li>\r\n</ul>\r\n<ul>\r\n<li>Type : Apartment</li>\r\n<li>Security : Yes</li>\r\n<li>Dining Capacity : 10 People</li>\r\n<li>Church/Temple : No</li>\r\n</ul>\r\n<ul>\r\n<li>3rd Party : No</li>\r\n<li>Elevator : Yes</li>\r\n<li>CCTV : Yes</li>\r\n<li>Water Supply : Ground Water / Tank</li>\r\n</ul>\r\n<!---feature area end--->\r\n<p>&nbsp;</p>', 'uploads/properties/1762278186_690a3b2a3cb8a.jpg', 'uploads/properties/1762278186_690a3b2a82dc4.jpg', 'uploads/properties/1762278186_690a3b2a83564.jpg', 'uploads/properties/1762278186_690a3b2a83ac5.jpg', 'uploads/properties/1762278186_690a3b2a841ca.jpg', NULL, 'uploads/properties/1762278186_690a3b2a84967.jpg', 'uploads/properties/1762278186_690a3b2a851ea.jpg', 'uploads/properties/1762278186_690a3b2a85bbb.jpg', 'available', 5, 3, 0, '<p>test</p>', '2025-11-04 17:43:06', '2025-11-05 04:56:46'),
(16, 'No Wake Zone', 'office', 'rent', 'lakh', 5.00, 2, 2, 1, '2 BHK', 2, 1, 2, '4 floor', 'Paritosh Building, 101,, Usmanpura', 'Surat', 'Gujrat', '1700sqft', '<!---feature area start--->\r\n<ul>\r\n<li>Property Age : 10 Years</li>\r\n<li>Swiming Pool : Yes</li>\r\n<li>Parking : Yes</li>\r\n<li>GYM : Yes</li>\r\n</ul>\r\n<ul>\r\n<li>Type : Apartment</li>\r\n<li>Security : Yes</li>\r\n<li>Dining Capacity : 10 People</li>\r\n<li>Church/Temple : No</li>\r\n</ul>\r\n<ul>\r\n<li>3rd Party : No</li>\r\n<li>Elevator : Yes</li>\r\n<li>CCTV : Yes</li>\r\n<li>Water Supply : Ground Water / Tank</li>\r\n</ul>\r\n<!---feature area end--->\r\n<p>&nbsp;</p>', 'uploads/properties/1762278431_690a3c1fa1658.jpg', 'uploads/properties/1762278431_690a3c1fe76fa.jpg', 'uploads/properties/1762278431_690a3c1fe7ec2.jpg', 'uploads/properties/1762278431_690a3c1fe83fb.jpg', 'uploads/properties/1762278431_690a3c1fe8bcc.jpg', NULL, 'uploads/properties/1762278431_690a3c1fe94e0.jpg', 'uploads/properties/1762278431_690a3c1fea0ed.jpg', 'uploads/properties/1762278431_690a3c1fead7c.jpg', 'available', 5, 3, 1, '<p>test</p>', '2025-11-04 17:47:11', '2025-11-05 04:56:51');

-- --------------------------------------------------------

--
-- Table structure for table `record`
--

CREATE TABLE `record` (
  `id` int(11) NOT NULL,
  `pid` int(11) NOT NULL,
  `agent_id` int(11) UNSIGNED NOT NULL,
  `time` datetime NOT NULL,
  `sale_type` varchar(50) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `buyer_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `record`
--

INSERT INTO `record` (`id`, `pid`, `agent_id`, `time`, `sale_type`, `price`, `buyer_id`) VALUES
(15, 9, 4, '2025-11-05 11:38:16', 'sale', 48.70, 10);

-- --------------------------------------------------------

--
-- Table structure for table `state`
--

CREATE TABLE `state` (
  `sid` int(50) NOT NULL,
  `sname` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `state`
--

INSERT INTO `state` (`sid`, `sname`) VALUES
(1, 'Gujrat'),
(2, 'Maharashtra'),
(3, 'Kerala'),
(4, 'Punjab'),
(5, 'Assam');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `uid` int(10) UNSIGNED NOT NULL,
  `uname` varchar(100) NOT NULL,
  `uemail` varchar(100) NOT NULL,
  `utype` enum('user','agent','builder') NOT NULL,
  `uphone` varchar(20) NOT NULL,
  `upassword` varchar(255) NOT NULL,
  `uimage` varchar(255) DEFAULT NULL,
  `email_verified` tinyint(1) NOT NULL DEFAULT 0,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `verify_otp` varchar(10) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_password_otp` varchar(6) DEFAULT NULL,
  `reset_password_otp_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`uid`, `uname`, `uemail`, `utype`, `uphone`, `upassword`, `uimage`, `email_verified`, `status`, `verify_otp`, `otp_expiry`, `created_at`, `updated_at`, `reset_password_otp`, `reset_password_otp_expiry`) VALUES
(5, 'Kabir', 'findbrick26@gmail.com', 'builder', '4444444444', '$2y$10$JGlLzcup5PQjCuvQelivIOyuK2fwCksSW89oIouv4l2PHG7AA0l22', 'uploads/users/user_68c15bb4c21bc6.02844311.jpg', 1, 'active', NULL, NULL, '2025-09-10 11:06:28', '2025-10-09 19:26:05', NULL, NULL),
(7, 'Mihir', 'tivedimihir@gmail.com', 'user', '4444444444', '$2y$10$/9.CCtKxgrKnFkgyNwChpOehs3x/LhaHKMWw/GBwwETvAvkPyn70O', 'uploads/users/user_68e8822f50b5c1.11908688.jpg', 1, 'active', NULL, NULL, '2025-10-10 03:49:03', '2025-10-10 03:49:55', NULL, NULL),
(9, 'Rumit', 'rumitsolanki09@gmail.com', 'agent', '1111111111', '$2y$10$5ZibKiSzNsgmjEFQ0JcUP.fmwwTNwKI9ZVXcCAwhZs66c2CwQbE22', 'uploads/users/user_68ed438f6d1f06.79472090.jpg', 1, 'active', NULL, NULL, '2025-10-13 18:23:11', '2025-10-31 17:12:25', '720752', '2025-10-31 18:22:25'),
(10, 'Khushali', 'kakashisin26@gmail.com', 'user', '1111111111', '$2y$10$2rCFHB7XI0TZRDtAGA6FQOKjMDSaxQQ0n5Mv9Q43H2u7CU9DjRQnu', 'uploads/users/user_6904e849dfcd79.90016127.jpg', 1, 'active', NULL, NULL, '2025-10-31 16:48:09', '2025-10-31 17:05:05', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about`
--
ALTER TABLE `about`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`aid`),
  ADD UNIQUE KEY `aemail` (`aemail`);

--
-- Indexes for table `agent`
--
ALTER TABLE `agent`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `city`
--
ALTER TABLE `city`
  ADD PRIMARY KEY (`cid`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `uid` (`uid`);

--
-- Indexes for table `property`
--
ALTER TABLE `property`
  ADD PRIMARY KEY (`pid`),
  ADD KEY `uid` (`uid`),
  ADD KEY `ptype` (`ptype`),
  ADD KEY `stype` (`stype`);

--
-- Indexes for table `record`
--
ALTER TABLE `record`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_record` (`pid`),
  ADD KEY `fk_aid` (`agent_id`),
  ADD KEY `fk_bid` (`buyer_id`);

--
-- Indexes for table `state`
--
ALTER TABLE `state`
  ADD PRIMARY KEY (`sid`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`uid`),
  ADD UNIQUE KEY `uemail` (`uemail`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about`
--
ALTER TABLE `about`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `aid` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `agent`
--
ALTER TABLE `agent`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `city`
--
ALTER TABLE `city`
  MODIFY `cid` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `property`
--
ALTER TABLE `property`
  MODIFY `pid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `record`
--
ALTER TABLE `record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `state`
--
ALTER TABLE `state`
  MODIFY `sid` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `uid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `record`
--
ALTER TABLE `record`
  ADD CONSTRAINT `fk_aid` FOREIGN KEY (`agent_id`) REFERENCES `agent` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_bid` FOREIGN KEY (`buyer_id`) REFERENCES `user` (`uid`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_record` FOREIGN KEY (`pid`) REFERENCES `property` (`pid`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
