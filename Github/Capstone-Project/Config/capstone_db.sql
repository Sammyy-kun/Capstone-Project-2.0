-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2026 at 02:50 AM
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
-- Database: `capstone_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `repair_id` int(11) NOT NULL,
  `technician_id` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `time_slot` varchar(50) NOT NULL,
  `status` enum('scheduled','completed','cancelled') DEFAULT 'scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `business_applications`
--

CREATE TABLE `business_applications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(120) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `id_type` varchar(50) DEFAULT NULL,
  `gov_id` varchar(100) DEFAULT NULL,
  `gov_id_file` varchar(255) DEFAULT NULL,
  `business_name` varchar(255) NOT NULL,
  `business_type` varchar(100) DEFAULT NULL,
  `business_form` varchar(50) DEFAULT NULL,
  `business_email` varchar(120) DEFAULT NULL,
  `business_phone` varchar(20) DEFAULT NULL,
  `business_address` text DEFAULT NULL,
  `tin_number` varchar(20) DEFAULT NULL,
  `offer_details` text DEFAULT NULL,
  `service_area` varchar(255) DEFAULT NULL,
  `avg_pricing` decimal(10,2) DEFAULT NULL,
  `business_permit` varchar(255) DEFAULT NULL,
  `dti_registration` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `business_applications`
--

INSERT INTO `business_applications` (`id`, `user_id`, `first_name`, `last_name`, `email`, `phone`, `id_type`, `gov_id`, `gov_id_file`, `business_name`, `business_type`, `business_form`, `business_email`, `business_phone`, `business_address`, `tin_number`, `offer_details`, `service_area`, `avg_pricing`, `business_permit`, `dti_registration`, `status`, `rejection_reason`, `created_at`, `updated_at`, `latitude`, `longitude`) VALUES
(1, 17, 'Ydan', 'Orbien', 'orbienydan1@gmail.com', '09662615748', 'National ID', '312321321321', 'Public/uploads/documents/biz_17_gov_id_file_1771744946.jpg', 'Digistore', 'Appliance Store', NULL, 'orbienydan1@gmail.com', '09662615748', 'sakljdiaskdjasd', '323-123-213-321', 'dsklahjdiahsdakjsdha ulul', 'dasma', 5000.00, 'Public/uploads/documents/biz_17_business_permit_1771744946.jpg', 'Public/uploads/documents/biz_17_dti_registration_1771744946.jpg', 'Pending', NULL, '2026-02-22 07:22:26', '2026-02-22 07:22:26', NULL, NULL),
(2, 18, 'Ydan', 'Orbien', 'orbienydan1@gmail.com', '32193821903', 'Drivers License', '3213213213', 'Public/uploads/documents/biz_18_gov_id_file_1771745228.jpg', 'Digistore', 'Appliance Store', NULL, 'orbienydan1@gmail.com', '09662615748', 'dasdasdasdsadsa', '213-213-123-123', 'dasdsadasdas', 'dasma', 5000.00, 'Public/uploads/documents/biz_18_business_permit_1771745228.jpg', 'Public/uploads/documents/biz_18_dti_registration_1771745228.jpg', 'Approved', NULL, '2026-02-22 07:27:08', '2026-02-22 08:24:54', NULL, NULL),
(3, 19, 'Jeriel Chryztal', 'Tan', 'orbienydan1@gmail.com', '09662615748', 'National ID', '32138213132131', 'Public/uploads/documents/biz_19_gov_id_file_1772103187.png', 'Chryzstore', 'Appliance Store', NULL, 'orbienydan1@gmail.com', '09662615748', 'SAMPLE ADDRESS', '123-123-212-133', 'WE SELL GOOD PRODUCTS', 'imus', 5000.00, 'Public/uploads/documents/biz_19_business_permit_1772103187.png', 'Public/uploads/documents/biz_19_dti_registration_1772103187.png', 'Pending', NULL, '2026-02-26 10:53:07', '2026-02-26 10:53:07', NULL, NULL),
(4, 21, 'Ydan', 'Orbien', 'dsadkas@gmail.com', '09372187367218', 'National ID', '39210938219032', 'Public/uploads/documents/biz_21_gov_id_file_1772533271.jpg', 'takdjsal', 'Appliance Store', NULL, 'orbienydan1@gmail.com', '321321321321312', 'dasdasdasd', '232-132-132-131', 'skdjlaksjdaslkdasj', 'dsadasd', 500.00, 'Public/uploads/documents/biz_21_business_permit_1772533271.jpg', 'Public/uploads/documents/biz_21_dti_registration_1772533271.jpg', 'Rejected', NULL, '2026-03-03 10:21:11', '2026-03-03 10:22:15', NULL, NULL),
(5, 24, 'Lorenz', 'Orbien', 'orbienydan1@gmail.com', '09662615748', 'National ID', '3132131231312321312312312312321312312', 'Public/uploads/documents/biz_24_gov_id_file_1772690011.png', 'Enzostore', 'Appliance Repair & Sales', NULL, 'orbienydan1@gmail.com', '09662615748', 'b9 L2 banaba street Narra homes Pag asa 1 Imus, Cavite 4103', '213-132-321-332', 'we sell good products and we do repairs professionally', 'IMUS', 1000.00, 'Public/uploads/documents/biz_24_business_permit_1772690011.jpg', 'Public/uploads/documents/biz_24_dti_registration_1772690011.jpg', 'Rejected', 'Invalid DTI/SEC registration', '2026-03-05 05:53:31', '2026-03-06 14:12:49', NULL, NULL),
(6, 25, 'Bill', 'Gates', 'billgates@gmail.com', '09323232323', 'National ID', '323232323232', 'Public/uploads/documents/biz_25_gov_id_file_1772715351.PNG', 'Microsoft', 'Appliance Store', NULL, 'billgates31@gmail.com', '32323232323', '2dsdsdsdsd', '323-232-232-323', 'dsddwsds', 'dssdsdsd', 3233.00, 'Public/uploads/documents/biz_25_business_permit_1772715351.jpg', 'Public/uploads/documents/biz_25_dti_registration_1772715351.PNG', 'Approved', NULL, '2026-03-05 12:55:51', '2026-03-05 12:57:51', NULL, NULL),
(7, 27, 'Dianne', 'Obillo', 'dianneobillo@gmail.com', '09627022406', 'National ID', '443434343434', 'Public/uploads/documents/biz_27_gov_id_file_1772809530.pdf', 'Dianne Store', 'Appliance Store', NULL, 'diannestore@gmail.com', '09627022406', 'sdsdsdsds', '343-434-343-434', 'dsdsds', 'dsdsds', 3434.00, 'Public/uploads/documents/biz_27_business_permit_1772809530.pdf', 'Public/uploads/documents/biz_27_dti_registration_1772809530.pdf', 'Approved', NULL, '2026-03-06 14:14:38', '2026-03-06 15:06:17', NULL, NULL),
(8, 28, 'Elon ', 'Musk', 'elonmusk31@gmail.com', '09627022406', 'National ID', '4343343434343434', 'Public/uploads/documents/biz_28_gov_id_file_1773146760.jpg', 'Tesla', 'Appliance Store', 'sole_proprietorship', 'elonmusk31@gmail.com', '09627022405', 'dfdfdffdf', '343-434-343-434', 'sddsdsdsdsd', 'sdsds', 4343434.00, 'Public/uploads/documents/biz_28_business_permit_1773146760.PNG', 'Public/uploads/documents/biz_28_dti_registration_1773146760.PNG', 'Approved', NULL, '2026-03-10 12:46:00', '2026-03-10 12:46:59', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `user_id`, `product_id`, `quantity`, `created_at`, `updated_at`) VALUES
(9, 22, 4, 2, '2026-03-05 10:13:34', '2026-03-05 10:13:38');

-- --------------------------------------------------------

--
-- Table structure for table `corporate_accounts`
--

CREATE TABLE `corporate_accounts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `tax_id` varchar(50) DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `credit_limit` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `repair_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('unpaid','paid','cancelled') DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error') DEFAULT 'info',
  `target_url` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `target_url`, `is_read`, `created_at`) VALUES
(2, 5, 'Test Notification 236', 'This is a test at 09:50:41', 'info', NULL, 0, '2026-02-04 08:50:41'),
(3, 22, 'Added to Cart', 'You added Logitech g102 to your cart successfully.', 'success', '../Cart/index.php', 0, '2026-03-05 10:13:34'),
(4, 22, 'Added to Cart', 'You added Logitech g102 to your cart successfully.', 'success', '../Cart/index.php', 0, '2026-03-05 10:13:38'),
(5, 22, 'Added to Cart', 'You added Attack Shark R5 to your cart successfully.', 'success', '../Cart/index.php', 0, '2026-03-05 10:16:22'),
(6, 22, 'Added to Cart', 'You added Attack Shark R5 to your cart successfully.', 'success', '../Cart/index.php', 0, '2026-03-05 10:16:27'),
(7, 26, 'Added to Cart', 'You added wooting 60HE to your cart successfully.', 'success', '../Cart/index.php', 1, '2026-03-05 13:02:03'),
(8, 26, 'Added to Cart', 'You added Attack Shark R5 to your cart successfully.', 'success', '../Cart/index.php', 1, '2026-03-10 11:47:27'),
(9, 26, 'Added to Cart', 'You added Attack Shark R5 to your cart successfully.', 'success', '../Cart/index.php', 1, '2026-03-10 12:28:49'),
(10, 28, 'Application Approved', 'Congratulations! Your business application has been approved. All merchant features are now unlocked.', 'success', NULL, 0, '2026-03-10 12:46:59'),
(11, 26, 'Added to Cart', 'You added Samsung Tv to your cart successfully.', 'success', '../Cart/index.php', 1, '2026-03-10 13:12:21'),
(12, 26, 'Added to Cart', 'You added Samsung Tv to your cart successfully.', 'success', '../Cart/index.php', 1, '2026-03-10 13:12:56'),
(13, 26, 'Added to Cart', 'You added Samsung Tv to your cart successfully.', 'success', '../Cart/index.php', 1, '2026-03-10 15:16:08'),
(14, 26, 'Added to Cart', 'You added Samsung Tv to your cart successfully.', 'success', '../Cart/index.php', 1, '2026-03-10 15:16:39'),
(15, 28, 'New Order Received', 'Order #1 from Samuel Obillo — ₱279,079.00', 'success', NULL, 0, '2026-03-10 15:23:36');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_id` int(11) DEFAULT NULL,
  `delivery_method` varchar(20) NOT NULL DEFAULT 'lalamove',
  `payment_method` varchar(20) NOT NULL DEFAULT 'cod',
  `delivery_fee` decimal(10,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `distance_km` decimal(8,2) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Processing','Shipped','Delivered','Cancelled') NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `address_id`, `delivery_method`, `payment_method`, `delivery_fee`, `notes`, `distance_km`, `total_amount`, `status`, `created_at`, `updated_at`) VALUES
(1, 26, 1, 'lalamove', 'cod', 80.00, NULL, NULL, 279079.00, 'Pending', '2026-03-10 15:23:36', '2026-03-10 15:23:36');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `owner_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL DEFAULT '',
  `company_name` varchar(255) NOT NULL DEFAULT '',
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `owner_id`, `product_name`, `company_name`, `quantity`, `price`) VALUES
(1, 1, 5, 28, 'Samsung Tv', 'Tesla', 1, 278999.00);

-- --------------------------------------------------------

--
-- Table structure for table `ownership_records`
--

CREATE TABLE `ownership_records` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `appliance_name` varchar(255) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model_number` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `warranty_expiry` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`, `created_at`) VALUES
(10, 'paoloemmanuelaustria@gmail.com', 'b83c3525ff50117eeec0556ee88d2b922a95a954a55da77560ef8515043716f3', '2026-02-16 15:32:08', '2026-02-16 07:15:02'),
(11, 'orbienydan1@gmail.com', '418817', '2026-02-27 01:14:19', '2026-02-26 16:59:19');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `repair_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `method` enum('gcash','paypal','cod') NOT NULL,
  `status` enum('pending','paid','failed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `specs` text DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `image_url_2` varchar(255) DEFAULT NULL,
  `image_url_3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `owner_id`, `product_name`, `brand`, `model`, `specs`, `qty`, `price`, `image_url`, `description`, `category`, `created_at`, `image_url_2`, `image_url_3`) VALUES
(1, 16, 'Madlions 68HE', NULL, NULL, NULL, 10, 3000.00, '../../../Public/uploads/products/prod_1772703414_5045.jpg', 'keyboard', NULL, '2026-02-20 02:18:47', '../../../Public/uploads/products/prod_1772703414_1285.jpg', NULL),
(2, 19, 'Attack Shark R5', NULL, NULL, NULL, 10, 2000.00, '../../../Public/uploads/products/prod_1772703525_8442.jpg', 'Mouse', NULL, '2026-02-26 11:21:34', NULL, NULL),
(3, 16, 'wooting 60HE', NULL, NULL, NULL, 50, 11000.00, '../../../Public/uploads/products/prod_1772703465_3662.jpg', 'rappid trigger keyboard', NULL, '2026-03-05 06:31:00', NULL, NULL),
(4, 16, 'Logitech g102', NULL, NULL, NULL, 20, 3000.00, '../../../Public/uploads/products/prod_1772693949_4368.webp', 'The Logitech G102 Lightsync is a budget-friendly wired gaming mouse featuring an 8,000 DPI optical sensor, 6 programmable buttons, and customizable RGB lighting. Known for its classic, ergonomic shape and reliable performance, it offers a 1000Hz polling rate and is fully compatible with Logitech G HUB software.', NULL, '2026-03-05 06:59:09', '../../../Public/uploads/products/prod_1772693949_1578.webp', NULL),
(5, 28, 'Samsung Tv', NULL, NULL, NULL, 5, 278999.00, '../../../Public/uploads/products/prod_1773148038_6355.jpg', 'Our most advanced Art TV blurs the lines between showpiece and entertainment for art and movie lovers alike. The modern, slim-bezel frame, matte screen and wireless connection¹ allow you to create a clutter-free gallery experience at home, featuring a selection of world-renowned artwork.² Whether you\'re beholding a masterpiece or curling up to watch your favorite movie, enjoy spectacular, 4K Neo QLED picture and sound quality, along with personalized TV experiences powered by Samsung Vision AI.³', 'TV/Monitor', '2026-03-10 13:07:18', '../../../Public/uploads/products/prod_1773148038_1310.jpg', '../../../Public/uploads/products/prod_1773148038_1165.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `product_serial_numbers`
--

CREATE TABLE `product_serial_numbers` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `serial_number` varchar(100) NOT NULL,
  `status` enum('in_stock','sold','distributed') DEFAULT 'in_stock',
  `distributed_to` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_serial_numbers`
--

INSERT INTO `product_serial_numbers` (`id`, `product_id`, `serial_number`, `status`, `distributed_to`, `created_at`) VALUES
(1, 2, '321321312', 'in_stock', NULL, '2026-02-26 11:21:40'),
(2, 1, 'dsadasdsad', 'in_stock', NULL, '2026-02-26 12:39:23');

-- --------------------------------------------------------

--
-- Table structure for table `repairs`
--

CREATE TABLE `repairs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `owner_id` int(11) NOT NULL,
  `service_type` enum('walk_in','home_service','pickup') DEFAULT 'walk_in',
  `issue_category` enum('mechanical','electrical','software','other') DEFAULT 'other',
  `description` text NOT NULL,
  `status` enum('pending','approved','in_progress','completed','rejected') DEFAULT 'pending',
  `consultation_status` enum('pending','consulted') DEFAULT 'pending',
  `consultation_notes` text DEFAULT NULL,
  `schedule_date` date DEFAULT NULL,
  `warranty_status` enum('valid','expired','void') DEFAULT 'valid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `priority` enum('low','medium','high','emergency') DEFAULT 'medium',
  `delivery_fee` decimal(10,2) DEFAULT 0.00,
  `delivery_payment_method` enum('online','cash') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repairs`
--

INSERT INTO `repairs` (`id`, `user_id`, `owner_id`, `service_type`, `issue_category`, `description`, `status`, `consultation_status`, `consultation_notes`, `schedule_date`, `warranty_status`, `created_at`, `priority`, `delivery_fee`, `delivery_payment_method`) VALUES
(2, 15, 9, 'home_service', 'software', 'may virus po pc ko', 'pending', 'pending', NULL, '2026-03-04', 'valid', '2026-02-16 07:31:59', 'medium', 0.00, NULL),
(3, 15, 9, 'home_service', 'mechanical', 'dasdas', 'pending', 'pending', NULL, '2026-02-20', 'valid', '2026-02-16 07:33:07', 'medium', 0.00, NULL),
(4, 20, 9, 'walk_in', 'mechanical', 'my electric fan isnt working', 'pending', 'pending', NULL, '2026-02-28', 'valid', '2026-02-26 11:43:22', 'medium', 0.00, NULL),
(5, 22, 16, 'home_service', 'software', 'dhasjdsajd', 'in_progress', 'pending', NULL, '2026-03-07', 'valid', '2026-03-03 10:24:49', 'medium', 0.00, NULL),
(6, 22, 16, 'home_service', 'software', 'cant install app', '', 'pending', NULL, '2026-03-08', 'valid', '2026-03-03 11:25:28', 'medium', 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `repair_assignments`
--

CREATE TABLE `repair_assignments` (
  `id` int(11) NOT NULL,
  `repair_id` int(11) NOT NULL,
  `technician_id` int(11) NOT NULL,
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('assigned','in_progress','completed') DEFAULT 'assigned'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `repair_assignments`
--

INSERT INTO `repair_assignments` (`id`, `repair_id`, `technician_id`, `assigned_at`, `status`) VALUES
(1, 5, 1, '2026-03-03 10:49:15', 'assigned');

-- --------------------------------------------------------

--
-- Table structure for table `repair_logs`
--

CREATE TABLE `repair_logs` (
  `id` int(11) NOT NULL,
  `repair_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `notes` text DEFAULT NULL,
  `technician_name` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `reviewer_id` int(11) NOT NULL,
  `target_id` int(11) NOT NULL,
  `target_type` enum('shop','technician') NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `spare_parts`
--

CREATE TABLE `spare_parts` (
  `id` int(11) NOT NULL,
  `part_name` varchar(255) NOT NULL,
  `part_number` varchar(100) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL,
  `reorder_level` int(11) DEFAULT 5,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `contact_info` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact_info`, `address`, `created_at`) VALUES
(1, 'Ydan', 'Ydan Emerson C. Orbien', 'b9 l2 banaba street narra homes', '2026-01-31 05:32:46'),
(2, 'Sample Supplier', '09662615748', 'B9 L2 Banaba Street Narra Homes Pag Asa 1 Imus,Cavite 4103', '2026-02-20 02:49:41');

-- --------------------------------------------------------

--
-- Table structure for table `technician_profiles`
--

CREATE TABLE `technician_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('active','busy','offline') DEFAULT 'offline',
  `specialization` text DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `technician_profiles`
--

INSERT INTO `technician_profiles` (`id`, `user_id`, `status`, `specialization`, `bio`, `created_at`) VALUES
(1, 13, 'active', '', '', '2026-01-31 10:04:37'),
(2, 23, 'offline', 'Software', 'Newly created technician.', '2026-03-03 11:24:02');

-- --------------------------------------------------------

--
-- Table structure for table `technician_skills`
--

CREATE TABLE `technician_skills` (
  `id` int(11) NOT NULL,
  `technician_id` int(11) NOT NULL,
  `skill_name` varchar(100) NOT NULL,
  `proficiency_level` enum('beginner','intermediate','expert') DEFAULT 'intermediate'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `role` enum('admin','owner','client','user','technician') NOT NULL DEFAULT 'user',
  `username` varchar(50) NOT NULL,
  `full_name` varchar(120) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `shop_code` varchar(20) DEFAULT NULL,
  `account_type` enum('individual','corporate') DEFAULT 'individual',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_picture` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role`, `username`, `full_name`, `email`, `password`, `phone`, `business_name`, `address`, `shop_code`, `account_type`, `created_at`, `profile_picture`, `status`) VALUES
(5, 'user', 'DebugUser_1769837131', 'Debug User', 'test_1769837131@example.com', '$2y$10$8AZ.ivORDBw1RnEzCIG/6u3K2S8.eUYB9btXdoEVa3CuxFh96MwB2', NULL, '', NULL, NULL, 'individual', '2026-01-31 05:25:31', NULL, 'Pending'),
(6, 'user', 'iloveriemaven', 'rie maven', 'orbienydan1@gmail.com', '$2y$10$SGxMyue/R0KXoHhbNMtnQeLMkkZMNHUp475akBSZ82G1XQRN8w5ii', NULL, '', NULL, NULL, 'individual', '2026-01-31 05:29:36', NULL, 'Pending'),
(7, 'owner', 'skatermark', 'Ydan', 'orbienydan1@gmail.com', '$2y$10$8BgT9.EsQpyBUxWWjJ03rupuE5.ZDDpwE3ODLzmWWe8Lq4lxxcpV6', NULL, 'Bombashop', NULL, NULL, 'individual', '2026-01-31 05:31:14', NULL, 'Rejected'),
(8, 'user', 'sammy', 'Samuel Obillo', 'orbienydan1@gmail.com', '$2y$10$Su1nAahemdchxiDNY2VQPeJRj/vxQkGnBtAJlj79s7VU6iRuQ9eLC', NULL, '', NULL, NULL, 'individual', '2026-01-31 06:20:37', NULL, 'Pending'),
(9, 'owner', 'prodigy', 'Ydan Emerson C. Orbien', 'orbien.ydanemerson@ncst.edu.ph', '$2y$10$4grUUfZB8I/.2/8NhEam4uAadqD4h24xln.Vd6zMAJP5aB1sB0K2i', NULL, 'Appliansays', NULL, 'SHOP-61B192', 'individual', '2026-01-31 06:35:14', NULL, 'Approved'),
(10, 'admin', 'fixmart', 'FixMart', 'admin@fixmart.com', '$2y$10$Fmd/oPzy8PyLWxcu1/QLHObcifMk5gN3qB1JUjvf1Y7vbmrB854Dy', '09662615748', 'null', 'null', NULL, 'individual', '2026-01-31 07:04:32', '../../../Public/uploads/profiles/91117389f41ecc9d10a46d480588792e.png', 'Pending'),
(11, 'user', 'demo_user_294', 'Demo User', 'demo_user@gmail.com', '$2y$10$Bd1NRao3IozO/Pc70IV.Pe0XaDSWVHbRO5PThL0FEfpEaUGyA7Soi', NULL, NULL, NULL, NULL, 'individual', '2026-01-31 08:32:57', NULL, 'Pending'),
(12, 'user', 'sammyy', 'Samuel Obillo', 'obillosamuel5@gmail.com', '$2y$10$QxzOzBj8zg97T3mo/l01qeZlGXs2Wd4ZaGaLjqZ9v4mhW6d2sgZau', NULL, '', NULL, NULL, 'individual', '2026-01-31 09:38:23', NULL, 'Pending'),
(13, 'technician', 'aljonmalupet123', 'Aljon Virana', 'obillosamuel5@gmail.com', '$2y$10$WsoU/5gdkMYPd69d9WFZP.caH1PLfP5IKp/wcxNrl3lSmFxH6g8Em', 'null', '', '', NULL, 'individual', '2026-01-31 10:04:37', '../../../Public/uploads/profiles/197ddc5c694b7eeed4f900735817b93b.png', 'Pending'),
(14, 'user', 'pao1', 'Paolo Emman Austria', 'orbienydan1@gmail.com', '$2y$10$dRov9y0QPqablVbz9Coh2ecWIoxHeCWPbpWbXGuLlRebK.aXN2kXG', NULL, '', NULL, NULL, 'individual', '2026-02-16 06:59:32', NULL, 'Pending'),
(15, 'user', 'pao2', 'Paolo Emman Austria', 'paoloemmanuelaustria@gmail.com', '$2y$10$SGNsB.1.XYD/TJkEYin0BekYZJrR29AGLRjeIGIFuRRXxJA8LgqxO', NULL, '', NULL, NULL, 'individual', '2026-02-16 07:02:17', NULL, 'Pending'),
(16, 'owner', 'lebronjames123', 'Lebron James', 'obillosamuel5@gmail.com', '$2y$10$2lk6HoBR6QL.Zzj87Xbm7OrnP4D1Ueu2Mb5e8cbwSDWw.IcWdvD5.', '09662615748', 'Goaty', 'b9 l2 banaba street', 'SHOP-ABF72A', 'individual', '2026-02-20 01:51:59', NULL, 'Pending'),
(17, 'owner', 'Digistore', 'Ydan Orbien', 'orbienydan1@gmail.com', '$2y$10$q7xTiW7Hg/J/jfV04cLZ..K48YlkCrj77e5ZBRnHHHcK8vVYSDYQu', NULL, NULL, NULL, NULL, 'individual', '2026-02-22 06:54:30', NULL, 'Pending'),
(18, 'owner', 'digistore123', 'Ydan Orbien', 'orbienydan1@gmail.com', '$2y$10$uyru2J3jjnWCPrdYfkB6su3Smn7JE7A79UrS3o1ecLAz3c4EPMJru', NULL, 'Digistore', NULL, NULL, 'individual', '2026-02-22 07:27:08', NULL, 'Approved'),
(19, 'owner', 'iloveydan', 'Jeriel', 'orbienydan1@gmail.com', '$2y$10$ydwZkf5dNr0O9UHd0fTnvew5zNeRhCyg8f0fKCcJ.q7AeWZQq2IZS', 'null', 'null', 'null', NULL, 'individual', '2026-02-26 10:53:07', NULL, 'Pending'),
(20, 'user', 'riri123', 'rie maven', 'orbienydan1@gmail.com', '$2y$10$WVkDUjKT88nKkn3BXX.C9eXnAgRX3oiJVH6weI8rcMDfxZT5nwoV6', NULL, '', NULL, NULL, 'individual', '2026-02-26 11:22:40', NULL, 'Pending'),
(21, 'owner', 'baxter123456', 'Ydan Orbien', 'dsadkas@gmail.com', '$2y$10$MR06TrHUuL9d8dH.1hXP/e/IbAcUpfCabRzzM7Poet5lUXarYKlhe', NULL, NULL, NULL, NULL, 'individual', '2026-03-03 10:21:11', NULL, 'Pending'),
(22, 'user', 'lerbs123', 'lerbs', 'orbienydan1@gmail.com', '$2y$10$GsHK9qn39d4pZjOh4WAENOCmDb8Ww08.pWWpyDn7W9oGeZmR.RgUe', NULL, '', NULL, NULL, 'individual', '2026-03-03 10:23:44', NULL, 'Pending'),
(23, 'technician', 'ydanemersonorbien', 'Ydan Emerson Orbien', 'orbienydan1@gmail.com', '$2y$10$gjS2xK4Au21LcLHBmwwlKOJ/8EglbSvfBWeD7yHiy89W3Gd7dWt0y', NULL, NULL, NULL, NULL, 'individual', '2026-03-03 11:24:02', NULL, 'Pending'),
(24, 'owner', 'lorenz123', 'Lorenz Orbien', 'orbienydan1@gmail.com', '$2y$10$GGvA1fmQ1G0umRcnP1plt.NqRzOX04aEN5BF6xJzqsDShRWGWtXUu', NULL, NULL, NULL, NULL, 'individual', '2026-03-05 05:53:31', NULL, 'Pending'),
(25, 'owner', 'billgates31', 'Bill Gates', 'billgates@gmail.com', '$2y$10$j08/sz3lTw1Ec7kPcS.x0.VgyPR5TSNfj4L2Pg5dQDLEV7yPDOULG', NULL, 'Microsoft', NULL, NULL, 'individual', '2026-03-05 12:55:51', NULL, 'Approved'),
(26, 'user', 'sammy31', 'Samuel Obillo', 'obillosamuel5@gmail.com', '$2y$10$nwZm.0Dy.Z6QkpOPhtgSYOEY2MpdJ8XSHnAklmbRNaVQawnxqDSUy', NULL, '', NULL, NULL, 'individual', '2026-03-05 12:59:51', NULL, 'Pending'),
(27, 'owner', 'dianneobillo', 'Dianne Obillo', 'dianneobillo@gmail.com', '$2y$10$SnkGtn0zOxDIvba8Tw2YHOQiwoyUcsWvXcHzPwHha.T1Bb6cb/wp6', NULL, 'Dianne Store', NULL, NULL, 'individual', '2026-03-06 14:14:38', NULL, 'Approved'),
(28, 'owner', 'elonmusk31', 'Elon Musk', 'elonmusk31@gmail.com', '$2y$10$KWJG5pD7MWBHyIoZvjdgROzG8jyhyA9hTMCg66/owO3Q9m4FRWnlW', NULL, 'Tesla', NULL, NULL, 'individual', '2026-03-10 12:46:00', NULL, 'Approved');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `label` varchar(50) NOT NULL DEFAULT 'Home',
  `recipient_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `street` varchar(255) NOT NULL,
  `barangay` varchar(100) NOT NULL DEFAULT '',
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL DEFAULT '',
  `zip_code` varchar(10) NOT NULL DEFAULT '',
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`id`, `user_id`, `label`, `recipient_name`, `phone`, `street`, `barangay`, `city`, `province`, `zip_code`, `latitude`, `longitude`, `is_default`, `created_at`) VALUES
(1, 26, 'Home', 'Samuel', '09627022406', 'Blk 8 Lot 63 Lapaz Homes 2', 'Cabezas', 'Trece Martires City', 'Cavite', '4109', NULL, NULL, 1, '2026-03-10 15:23:22');

-- --------------------------------------------------------

--
-- Table structure for table `warranties`
--

CREATE TABLE `warranties` (
  `id` int(11) NOT NULL,
  `repair_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('active','expired','void') DEFAULT 'active',
  `terms` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `repair_id` (`repair_id`),
  ADD KEY `technician_id` (`technician_id`);

--
-- Indexes for table `business_applications`
--
ALTER TABLE `business_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `corporate_accounts`
--
ALTER TABLE `corporate_accounts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `repair_id` (`repair_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `ownership_records`
--
ALTER TABLE `ownership_records`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_token` (`token`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `repair_id` (`repair_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `product_serial_numbers`
--
ALTER TABLE `product_serial_numbers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `serial_number` (`serial_number`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `repairs`
--
ALTER TABLE `repairs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `owner_id` (`owner_id`);

--
-- Indexes for table `repair_assignments`
--
ALTER TABLE `repair_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `repair_id` (`repair_id`),
  ADD KEY `technician_id` (`technician_id`);

--
-- Indexes for table `repair_logs`
--
ALTER TABLE `repair_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `repair_id` (`repair_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviewer_id` (`reviewer_id`);

--
-- Indexes for table `spare_parts`
--
ALTER TABLE `spare_parts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `technician_profiles`
--
ALTER TABLE `technician_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `technician_skills`
--
ALTER TABLE `technician_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `technician_id` (`technician_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `shop_code` (`shop_code`),
  ADD KEY `email` (`email`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `warranties`
--
ALTER TABLE `warranties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `repair_id` (`repair_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `business_applications`
--
ALTER TABLE `business_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `corporate_accounts`
--
ALTER TABLE `corporate_accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ownership_records`
--
ALTER TABLE `ownership_records`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product_serial_numbers`
--
ALTER TABLE `product_serial_numbers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `repairs`
--
ALTER TABLE `repairs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `repair_assignments`
--
ALTER TABLE `repair_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `repair_logs`
--
ALTER TABLE `repair_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `spare_parts`
--
ALTER TABLE `spare_parts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `technician_profiles`
--
ALTER TABLE `technician_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `technician_skills`
--
ALTER TABLE `technician_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `warranties`
--
ALTER TABLE `warranties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`repair_id`) REFERENCES `repairs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`technician_id`) REFERENCES `technician_profiles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `business_applications`
--
ALTER TABLE `business_applications`
  ADD CONSTRAINT `biz_app_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_product_ibfk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_user_ibfk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `corporate_accounts`
--
ALTER TABLE `corporate_accounts`
  ADD CONSTRAINT `corporate_accounts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`repair_id`) REFERENCES `repairs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `order_user_ibfk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_item_order_ibfk` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_item_product_ibfk` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `ownership_records`
--
ALTER TABLE `ownership_records`
  ADD CONSTRAINT `ownership_records_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`repair_id`) REFERENCES `repairs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `product_serial_numbers`
--
ALTER TABLE `product_serial_numbers`
  ADD CONSTRAINT `product_serial_numbers_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `repairs`
--
ALTER TABLE `repairs`
  ADD CONSTRAINT `repairs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `repairs_ibfk_2` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `repair_assignments`
--
ALTER TABLE `repair_assignments`
  ADD CONSTRAINT `repair_assignments_ibfk_1` FOREIGN KEY (`repair_id`) REFERENCES `repairs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `repair_assignments_ibfk_2` FOREIGN KEY (`technician_id`) REFERENCES `technician_profiles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `repair_logs`
--
ALTER TABLE `repair_logs`
  ADD CONSTRAINT `repair_logs_ibfk_1` FOREIGN KEY (`repair_id`) REFERENCES `repairs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `spare_parts`
--
ALTER TABLE `spare_parts`
  ADD CONSTRAINT `spare_parts_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `technician_profiles`
--
ALTER TABLE `technician_profiles`
  ADD CONSTRAINT `technician_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `technician_skills`
--
ALTER TABLE `technician_skills`
  ADD CONSTRAINT `technician_skills_ibfk_1` FOREIGN KEY (`technician_id`) REFERENCES `technician_profiles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `address_user_ibfk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warranties`
--
ALTER TABLE `warranties`
  ADD CONSTRAINT `warranties_ibfk_1` FOREIGN KEY (`repair_id`) REFERENCES `repairs` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
