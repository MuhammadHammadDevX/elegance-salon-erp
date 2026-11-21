-- ========================================================
-- Elegance Salon Karachi - Complete Database Dump
-- Ready for Import on InfinityFree / cPanel / Localhost
-- Generated at: 2026-08-28 07:27:04
-- ========================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+05:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Disable foreign key checks for clean drop
DROP TABLE IF EXISTS `appointments`, `reviews`, `contact_messages`, `inventory`, `staff`, `services`, `user`, `clients`, `commissions`, `invoices`, `purchase_orders`, `suppliers`;

-- --------------------------------------------------------
-- Table structure for `user`
-- --------------------------------------------------------

CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','receptionist','stylist','client') NOT NULL DEFAULT 'client',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `phone` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `user`
INSERT INTO `user` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES ('2', 'Ayesha Reception', 'reception@elegance.pk', '03111234567', '2d73067c9d5e8194bb0d5c53bfdc2c5b', 'receptionist', '2026-08-20 18:16:51');
INSERT INTO `user` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES ('3', 'Zachary Nickell', 'zachary@elegance.pk', '03221234567', '$2y$10$TcGck9mnIVG2lazCgKhwQ.ymuzhLnPx0RLx27Q0X9ED04WH5TLhLC', 'stylist', '2026-08-20 18:16:51');
INSERT INTO `user` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES ('4', 'Hamza Farooq', 'hamza@gmail.com', '03211234567', '$2y$10$TcGck9mnIVG2lazCgKhwQ.ymuzhLnPx0RLx27Q0X9ED04WH5TLhLC', 'client', '2026-08-20 18:16:51');
INSERT INTO `user` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES ('5', 'saad', 'saadkhan23@gmail.com', '03879878787', '$2y$10$20YZHaCwixMDO3fUZO9OAeRCHsAoW2VPHUJF14UE4t6v36dKq6n/2', 'client', '2026-08-20 19:03:41');
INSERT INTO `user` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES ('6', 'alibahi', 'ali@gamil.com', '03856749787', '$2y$10$Je3ogMKF4jYUXmViO1mY6uwcyyOASK/6WV8PpnxpvI07Iaw5hfNxS', 'client', '2026-08-20 19:07:14');
INSERT INTO `user` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES ('7', 'asad', 'asad@gmail.com', '03749809096', '$2y$10$UtCCvNXHgZXTjU3bERCNA.Pxe19TihkCQKQy8HSwDBO7xp2fm5TLa', 'client', '2026-08-20 19:44:21');
INSERT INTO `user` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES ('8', 'ahmed1', 'ahmed@gamil.com', '03746898686', '$2y$10$M/AMoeuoubQIozrDdju4C.EwgArh/smP/mIL04ePF/BQa65rRo96K', 'client', '2026-08-20 19:49:36');
INSERT INTO `user` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES ('9', 'zafarali1', 'zafar@gamil.com', '03875687683', '$2y$10$F4uvvbsq9B9q.lOuWeupTumdTktQl0XparUumq3pQvb8nWaIxb4d.', 'client', '2026-08-20 19:54:51');
INSERT INTO `user` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES ('12', 'alihasan23', 'alihasan123@gmail.com', '03120362892', '$2y$10$VCdw11QYFAWCCEYNso7KO.ZdjM0CpeVW7tfusKS4rc3GXNP0niqzG', 'client', '2026-08-21 08:35:31');
INSERT INTO `user` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES ('15', 'Muhammad Saad1', 'sqofficial101@gmail.com', '3120362892', '$2y$10$fOSzd8Vr1zQSOoO9yclSK.hnkJx/KHRaj8jKjAqzHDoy1gFWNO2m2', 'client', '2026-08-21 11:40:52');
INSERT INTO `user` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES ('16', 'hasan', 'hasan123@gmail.com', '03193678787', '3b8dc5b2005216cc2da403e5c8b297fc', 'client', '2026-08-22 11:56:54');
INSERT INTO `user` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES ('17', 'asadkhan123', 'asad12@gmail.com', '0328798978', '5883a6b7893ad8f022e1dfe864cb4890', 'client', '2026-08-22 12:45:20');
INSERT INTO `user` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES ('21', 'Saad Admin', 'admin@elegance.pk', '03001122334', '0192023a7bbd73250516f069df18b500', 'admin', '2026-08-27 19:23:32');
INSERT INTO `user` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES ('23', 'Daniyal Client', 'client@elegance.pk', '03009988776', '3677b23baa08f74c28aba07f0cb6554e', 'client', '2026-08-27 19:23:46');
INSERT INTO `user` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES ('24', 'dg', 'dg@gmail.com', '03847578855', '0bee8e95809f9a82de065c381e7aa230', 'client', '2026-08-27 20:11:15');
INSERT INTO `user` (`id`, `name`, `email`, `phone`, `password`, `role`, `created_at`) VALUES ('25', 'bilal', 'bilal@gmail.com', '03376855556', 'edac712ac8cc6e53f6dafed051da93b1', 'client', '2026-08-27 22:07:31');

-- --------------------------------------------------------
-- Table structure for `services`
-- --------------------------------------------------------

CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT 'General',
  `price` decimal(10,2) NOT NULL,
  `duration` varchar(50) DEFAULT '60 min',
  `description` text DEFAULT NULL,
  `bookings` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `tag` varchar(100) DEFAULT 'SIGNATURE SERVICE',
  `subtitle` varchar(255) DEFAULT NULL,
  `hero_image` varchar(255) DEFAULT 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=1920&auto=format&fit=crop',
  `card1_title` varchar(100) DEFAULT NULL,
  `card1_desc` text DEFAULT NULL,
  `card1_img` varchar(255) DEFAULT NULL,
  `card2_title` varchar(100) DEFAULT NULL,
  `card2_desc` text DEFAULT NULL,
  `card2_img` varchar(255) DEFAULT NULL,
  `card3_title` varchar(100) DEFAULT NULL,
  `card3_desc` text DEFAULT NULL,
  `card3_img` varchar(255) DEFAULT NULL,
  `package_details` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `services`
INSERT INTO `services` (`id`, `name`, `category`, `price`, `duration`, `description`, `bookings`, `is_active`, `tag`, `subtitle`, `hero_image`, `card1_title`, `card1_desc`, `card1_img`, `card2_title`, `card2_desc`, `card2_img`, `card3_title`, `card3_desc`, `card3_img`, `package_details`) VALUES ('1', 'Total Grooming', 'Grooming', '5000.00', '120 min', 'Indulge in our comprehensive Total Grooming package, combining expert hair styling, precise beard shaping, deep skin cleanup, and a relaxing hot towel finish for the complete gentleman\'s transformation.', '87', '1', 'ALL-IN-ONE LUXURY', 'The ultimate full-service rejuvenation package designed for the gentleman of distinction.', 'https://images.unsplash.com/photo-1503951914875-452162b0f3f1?q=80&w=1920&auto=format&fit=crop', 'Complete Hair Styling', 'Professional shampoo, scalp massage, precision cut, and modern blow-dry styling tailored specifically to your personality.', 'assets/images/homepage/total-grooming/0633082c6c4e7371ced709f46e8750f0.jpg', 'Beard Maintenance', 'Meticulous trimming, shaping, razor lining, and hydrating beard oil treatment to keep your facial hair soft and sharp.', 'assets/images/homepage/total-grooming/2.jpg', 'Facial Cleanup & Hot Towel', 'Deep pore cleansing, blackhead removal, skin toning, and soothing hot towel relaxation to completely rejuvenate your face.', 'assets/images/homepage/total-grooming/3.jpg', 'Includes complete hair styling, beard shaping, facial cleanup, hot towel therapy, and premium grooming products.');
INSERT INTO `services` (`id`, `name`, `category`, `price`, `duration`, `description`, `bookings`, `is_active`, `tag`, `subtitle`, `hero_image`, `card1_title`, `card1_desc`, `card1_img`, `card2_title`, `card2_desc`, `card2_img`, `card3_title`, `card3_desc`, `card3_img`, `package_details`) VALUES ('2', 'Signature Cut & Beard', 'Hair', '3500.00', '90 min', 'Experience premier grooming with our Signature Cut & Beard service. Our master stylists blend tailored scissor work, skin fades, and precision razor lines.', '142', '1', 'MASTER BARBERING', 'Precision architectural haircutting paired with sharp, symmetry-driven beard sculpting.', 'https://images.unsplash.com/photo-1599351431202-1e0f0137899a?q=80&w=1920&auto=format&fit=crop', 'Precision Haircut & Fade', 'Custom consultation, shear work, seamless fade transitions, and texturizing for an effortless masculine profile.', 'assets/images/homepage/signature-cut/1.jpg', 'Beard Sculpting & Lining', 'Detailed jawline trimming, cheek fading, and sharp straight-razor contouring tailored to your face geometry.', 'assets/images/homepage/signature-cut/2.jpg', 'Hot Towel Finish & Styling', 'Warm towel infused with essential oils followed by premium styling clay or pomade for long-lasting hold.', 'assets/images/homepage/signature-cut/3.jpg', 'Includes consultation, wash, precision haircut, beard trim, razor finish, and styling products.');
INSERT INTO `services` (`id`, `name`, `category`, `price`, `duration`, `description`, `bookings`, `is_active`, `tag`, `subtitle`, `hero_image`, `card1_title`, `card1_desc`, `card1_img`, `card2_title`, `card2_desc`, `card2_img`, `card3_title`, `card3_desc`, `card3_img`, `package_details`) VALUES ('3', 'Beard Designs', 'Beard', '2000.00', '45 min', 'Define your facial structure with our meticulous Beard Design service. Our master barbers specialize in custom contouring, razor-sharp edge-ups, and professional beard grooming.', '98', '1', 'FACIAL HAIR ARTISTRY', 'Custom jawline contouring, razor-sharp edge definition, and organic hydration therapy.', 'https://images.unsplash.com/photo-1621605815971-fbc98d665033?q=80&w=1920&auto=format&fit=crop', 'Custom Contouring & Shaping', 'Tailored beard mapping based on your face shape, balancing volume, length, and jawline definition for a striking profile.', 'assets/images/homepage/beard-design/1.jpg', 'Precision Razor Line-Up', 'Flawless straight-razor detailing along the cheeks and neckline, ensuring razor-sharp symmetry and clean professional edges.', 'assets/images/homepage/beard-design/2.jpg', 'Organic Oil & Balm Treatment', 'Finished with premium organic essential oils and styling balms to condition hair follicles, soothe skin, and provide long-lasting hold.', 'assets/images/homepage/beard-design/3.jpg', 'Includes facial hair consultation, custom beard shaping, straight-razor line-up, hot towel touch, and organic oil treatment.');
INSERT INTO `services` (`id`, `name`, `category`, `price`, `duration`, `description`, `bookings`, `is_active`, `tag`, `subtitle`, `hero_image`, `card1_title`, `card1_desc`, `card1_img`, `card2_title`, `card2_desc`, `card2_img`, `card3_title`, `card3_desc`, `card3_img`, `package_details`) VALUES ('4', 'Traditional Shave', 'Shave', '1800.00', '30 min', 'Experience the timeless luxury of a classic straight-razor shave. Complete with soothing hot and cold towel therapy, nourishing pre-shave oils, and expert master barber precision.', '63', '1', 'TIMELESS RITUAL', 'The authentic straight-razor shave with essential hot and cold eucalyptus towel therapy.', 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=1920&auto=format&fit=crop', 'Hot Towel & Oil Prep', 'Steamed hot towel application combined with natural pre-shave oils to open pores, soften coarse hair, and protect sensitive skin.', 'assets/images/homepage/traditional-design/1.jpg', 'Straight-Razor Technique', 'Expert single-blade straight razor shave executed with precision strokes for a luxuriously close and clean finish.', 'assets/images/homepage/traditional-design/2.jpg', 'Cold Towel & Balm Finish', 'Refreshed with a chilled eucalyptus towel to close pores, followed by alcohol-free soothing aftershave balm.', 'assets/images/homepage/traditional-design/3.jpg', 'Includes hot towel preparation, pre-shave oil, classic straight-razor shave, cold towel closing, and aftershave balm.');
INSERT INTO `services` (`id`, `name`, `category`, `price`, `duration`, `description`, `bookings`, `is_active`, `tag`, `subtitle`, `hero_image`, `card1_title`, `card1_desc`, `card1_img`, `card2_title`, `card2_desc`, `card2_img`, `card3_title`, `card3_desc`, `card3_img`, `package_details`) VALUES ('5', 'Deep Clean Facial', 'Facial', '2500.00', '60 min', 'Revitalize your skin with our professional Deep Clean Facial. Designed specifically for men\'s skin to remove impurities, unclog pores, and restore a healthy, energetic glow.', '54', '1', 'SKINCARE REVOLUTION', 'Pore purification, detoxifying scrubs, and cooling hydration designed for urban men.', 'https://images.unsplash.com/photo-1512690459411-b9245aed614b?q=80&w=1920&auto=format&fit=crop', 'Deep Pore Cleansing', 'Gentle yet thorough cleansing milk and steam application to open up blocked pores and flush out trapped environmental toxins.', 'assets/images/homepage/deep-clean-facial/1.jpg', 'Exfoliation & Extraction', 'Professional scrub exfoliation to slough off dead skin cells, followed by safe extraction to clear blackheads and blemishes.', 'assets/images/homepage/deep-clean-facial/2.jpg', 'Hydrating Mask & Massage', 'A nutrient-rich cooling face mask paired with a relaxing facial massage to lock in moisture and calm the skin.', 'assets/images/homepage/deep-clean-facial/3.jpg', 'Includes deep pore cleansing, steam, gentle exfoliation, blackhead extraction, soothing mask, and hydration.');

-- --------------------------------------------------------
-- Table structure for `staff`
-- --------------------------------------------------------

CREATE TABLE `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) NOT NULL,
  `specialization` varchar(100) DEFAULT 'Master Stylist',
  `commission_rate` decimal(5,2) NOT NULL DEFAULT 18.00,
  `work_schedule` text DEFAULT NULL,
  `image` varchar(255) DEFAULT 'assets/images/homepage/stylist/1.jpg',
  `bio` text DEFAULT NULL,
  `status` enum('Active','On Leave','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `staff_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `staff`
INSERT INTO `staff` (`id`, `user_id`, `name`, `email`, `phone`, `specialization`, `commission_rate`, `work_schedule`, `image`, `bio`, `status`, `created_at`) VALUES ('1', '3', 'Asif', 'zachary@elegance.pk', '03221234567', 'Master Barber &amp; Beard Stylist', '20.00', 'Tue-Sun 11AM-9PM', 'assets/images/homepage/stylist/1.jpg', '', 'Active', '2026-08-20 18:16:51');
INSERT INTO `staff` (`id`, `user_id`, `name`, `email`, `phone`, `specialization`, `commission_rate`, `work_schedule`, `image`, `bio`, `status`, `created_at`) VALUES ('3', NULL, 'Zafar', 'marcus@elegance.pk', '03334455667', 'Skin Treatments &amp; Grooming', '18.00', 'Tue-Sun 12PM-10PM', 'assets/images/homepage/stylist/2.jpg', '', 'Active', '2026-08-20 18:16:51');
INSERT INTO `staff` (`id`, `user_id`, `name`, `email`, `phone`, `specialization`, `commission_rate`, `work_schedule`, `image`, `bio`, `status`, `created_at`) VALUES ('5', NULL, 'Aliyan', 'sqofficial101@gmail.com', '03273657676', 'Skin Treatments &amp; Grooming', '18.00', 'Tue-Sun 12PM-10PM', 'assets/images/homepage/stylist/3.jpg', '', 'Active', '2026-08-24 18:53:22');
INSERT INTO `staff` (`id`, `user_id`, `name`, `email`, `phone`, `specialization`, `commission_rate`, `work_schedule`, `image`, `bio`, `status`, `created_at`) VALUES ('6', NULL, 'Sufiyan', 'sufiyan@gmail.com', '03129896765', 'shave', '18.00', 'Tue-Sun 11AM-9PM', 'assets/images/homepage/stylist/2.jpg', '', 'Active', '2026-08-24 19:24:57');

-- --------------------------------------------------------
-- Table structure for `inventory`
-- --------------------------------------------------------

CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `brand` varchar(100) DEFAULT 'Elegance',
  `image` varchar(255) DEFAULT 'assets/images/products/pomade.jpg',
  `category` varchar(50) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `min_threshold` int(11) NOT NULL DEFAULT 5,
  `cost_price` decimal(10,2) NOT NULL,
  `retail_price` decimal(10,2) DEFAULT 0.00,
  `unit_type` varchar(30) DEFAULT 'Pieces',
  `supplier_name` varchar(100) DEFAULT NULL,
  `status` enum('In Stock','Low Stock','Out of Stock') DEFAULT 'In Stock',
  `last_restocked` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `inventory`
INSERT INTO `inventory` (`id`, `code`, `name`, `brand`, `image`, `category`, `quantity`, `min_threshold`, `cost_price`, `retail_price`, `unit_type`, `supplier_name`, `status`, `last_restocked`, `created_at`) VALUES ('1', 'SKU-UPP-101', 'Deluxe Matte Clay Pomade (100g)', 'Uppercut Deluxe', 'https://images.unsplash.com/photo-1597854710119-a5a856942c73?q=80&w=600&auto=format&fit=crop', 'Hair Care', '28', '6', '1800.00', '3200.00', 'Jars', 'Uppercut Australia Direct Imports', 'In Stock', '2026-08-27', '2026-08-27 22:21:02');
INSERT INTO `inventory` (`id`, `code`, `name`, `brand`, `image`, `category`, `quantity`, `min_threshold`, `cost_price`, `retail_price`, `unit_type`, `supplier_name`, `status`, `last_restocked`, `created_at`) VALUES ('2', 'SKU-PRO-202', 'Eucalyptus & Menthol Pre-Shave Cream (100ml)', 'Proraso Firenze', 'https://images.unsplash.com/photo-1621605815971-fbc98d665033?q=80&w=600&auto=format&fit=crop', 'Shave Supplies', '19', '5', '1200.00', '2400.00', 'Jars', 'Italian Barber Supplies Karachi', 'In Stock', '2026-08-27', '2026-08-27 22:21:02');
INSERT INTO `inventory` (`id`, `code`, `name`, `brand`, `image`, `category`, `quantity`, `min_threshold`, `cost_price`, `retail_price`, `unit_type`, `supplier_name`, `status`, `last_restocked`, `created_at`) VALUES ('3', 'SKU-MOR-303', 'Argan Conditioning Beard Oil (50ml)', 'Moroccanoil Grooming', 'https://images.unsplash.com/photo-1608248597359-5b583093c72b?q=80&w=600&auto=format&fit=crop', 'Beard Care', '4', '6', '2200.00', '4500.00', 'Bottles', 'Morocco Organic Imports PK', 'Low Stock', '2026-08-22', '2026-08-27 22:21:02');
INSERT INTO `inventory` (`id`, `code`, `name`, `brand`, `image`, `category`, `quantity`, `min_threshold`, `cost_price`, `retail_price`, `unit_type`, `supplier_name`, `status`, `last_restocked`, `created_at`) VALUES ('4', 'SKU-REU-404', 'Blue Strong Hold High Sheen Pomade (113g)', 'Reuzel Holland', 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?q=80&w=600&auto=format&fit=crop', 'Hair Care', '15', '5', '1950.00', '3600.00', 'Jars', 'Schorem Barbers Distributor PK', 'In Stock', '2026-08-27', '2026-08-27 22:21:02');
INSERT INTO `inventory` (`id`, `code`, `name`, `brand`, `image`, `category`, `quantity`, `min_threshold`, `cost_price`, `retail_price`, `unit_type`, `supplier_name`, `status`, `last_restocked`, `created_at`) VALUES ('5', 'SKU-LOR-505', 'Serie Expert Silver Anti-Brass Shampoo (300ml)', 'L\'Oreal Professionnel', 'https://images.unsplash.com/photo-1535585209827-a15fcdbc4c2d?q=80&w=600&auto=format&fit=crop', 'Hair Care', '22', '8', '2100.00', '3800.00', 'Bottles', 'L\'Oreal Pakistan Official', 'In Stock', '2026-08-27', '2026-08-27 22:21:02');
INSERT INTO `inventory` (`id`, `code`, `name`, `brand`, `image`, `category`, `quantity`, `min_threshold`, `cost_price`, `retail_price`, `unit_type`, `supplier_name`, `status`, `last_restocked`, `created_at`) VALUES ('6', 'SKU-DER-606', 'Daily Microfoliant Detox Facial Scrub (75g)', 'Dermalogica Men', 'https://images.unsplash.com/photo-1556228720-195a672e8a03?q=80&w=600&auto=format&fit=crop', 'Skin Care', '0', '4', '3400.00', '6200.00', 'Bottles', 'Dermalogica Karachi Clinic Supplies', 'Out of Stock', '2026-08-09', '2026-08-27 22:21:02');
INSERT INTO `inventory` (`id`, `code`, `name`, `brand`, `image`, `category`, `quantity`, `min_threshold`, `cost_price`, `retail_price`, `unit_type`, `supplier_name`, `status`, `last_restocked`, `created_at`) VALUES ('7', 'SKU-FEA-707', 'Hi-Stainless Double Edge Barber Blades (Pack of 100)', 'Feather Japan', 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=600&auto=format&fit=crop', 'Tools & Blades', '45', '10', '1400.00', '2800.00', 'Packs', 'Tokyo Surgical Instruments Co.', 'In Stock', '2026-08-27', '2026-08-27 22:21:02');
INSERT INTO `inventory` (`id`, `code`, `name`, `brand`, `image`, `category`, `quantity`, `min_threshold`, `cost_price`, `retail_price`, `unit_type`, `supplier_name`, `status`, `last_restocked`, `created_at`) VALUES ('8', 'SKU-TOM-808', 'Oud Wood Luxury Grooming Beard Balm (50ml)', 'Tom Ford Private Blend', 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?q=80&w=600&auto=format&fit=crop', 'Fragrance', '8', '3', '4800.00', '9500.00', 'Jars', 'Luxury Perfumery Dubai Imports', 'In Stock', '2026-08-27', '2026-08-27 22:21:02');

-- --------------------------------------------------------
-- Table structure for `appointments`
-- --------------------------------------------------------

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `booking_ref` varchar(20) DEFAULT NULL,
  `token` varchar(10) DEFAULT NULL,
  `client_name` varchar(100) NOT NULL,
  `client_phone` varchar(20) NOT NULL,
  `client_email` varchar(100) DEFAULT NULL,
  `service_id` int(11) DEFAULT NULL,
  `service_name` varchar(100) NOT NULL,
  `stylist_id` int(11) DEFAULT NULL,
  `stylist_name` varchar(100) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` varchar(20) NOT NULL,
  `chair_no` int(11) DEFAULT 0,
  `price` decimal(10,2) NOT NULL,
  `commission_amount` decimal(10,2) DEFAULT 0.00,
  `payment_mode` varchar(50) DEFAULT 'Cash',
  `payment_status` enum('Unpaid','Paid') DEFAULT 'Paid',
  `status` enum('Booked','Confirmed','In Chair','Completed','Cancelled','Rescheduled') DEFAULT 'Confirmed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_ref` (`booking_ref`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `appointments`
INSERT INTO `appointments` (`id`, `user_id`, `booking_ref`, `token`, `client_name`, `client_phone`, `client_email`, `service_id`, `service_name`, `stylist_id`, `stylist_name`, `appointment_date`, `appointment_time`, `chair_no`, `price`, `commission_amount`, `payment_mode`, `payment_status`, `status`, `created_at`) VALUES ('1', NULL, 'ELG-8901', 'TK-101', 'Daniyal Khan', '+92 300 9283741', 'daniyal@gmail.com', NULL, 'Executive Haircut & Styling', NULL, 'Asif', '2026-08-24', '11:00 AM', '1', '2500.00', '450.00', 'Cash', 'Paid', 'Completed', '2026-08-24 19:30:34');
INSERT INTO `appointments` (`id`, `user_id`, `booking_ref`, `token`, `client_name`, `client_phone`, `client_email`, `service_id`, `service_name`, `stylist_id`, `stylist_name`, `appointment_date`, `appointment_time`, `chair_no`, `price`, `commission_amount`, `payment_mode`, `payment_status`, `status`, `created_at`) VALUES ('2', NULL, 'ELG-8902', 'TK-102', 'Hamza Sheikh', '+92 321 8847291', 'hamza.s@hotmail.com', NULL, 'Royal Beard Sculpt & Razor Lining', NULL, 'Zafar', '2026-08-24', '12:30 PM', '2', '1800.00', '324.00', 'Card / POS', 'Paid', 'Completed', '2026-08-24 19:30:34');
INSERT INTO `appointments` (`id`, `user_id`, `booking_ref`, `token`, `client_name`, `client_phone`, `client_email`, `service_id`, `service_name`, `stylist_id`, `stylist_name`, `appointment_date`, `appointment_time`, `chair_no`, `price`, `commission_amount`, `payment_mode`, `payment_status`, `status`, `created_at`) VALUES ('3', NULL, 'ELG-8903', 'TK-103', 'Bilal Ahmed', '+92 333 4910283', 'bilal.ahmed@yahoo.com', NULL, 'Total Grooming Package', NULL, 'Aliyan', '2026-08-24', '02:00 PM', '3', '5500.00', '990.00', 'JazzCash', 'Paid', 'Completed', '2026-08-24 19:30:34');
INSERT INTO `appointments` (`id`, `user_id`, `booking_ref`, `token`, `client_name`, `client_phone`, `client_email`, `service_id`, `service_name`, `stylist_id`, `stylist_name`, `appointment_date`, `appointment_time`, `chair_no`, `price`, `commission_amount`, `payment_mode`, `payment_status`, `status`, `created_at`) VALUES ('4', NULL, 'ELG-8904', 'TK-104', 'Farhan Qureshi', '+92 312 9018274', 'farhan.q@gmail.com', NULL, 'Charcoal Detox Facial & Cleanup', NULL, 'Asif', '2026-08-24', '03:30 PM', '1', '3500.00', '630.00', 'Cash', 'Unpaid', 'Confirmed', '2026-08-24 19:30:34');
INSERT INTO `appointments` (`id`, `user_id`, `booking_ref`, `token`, `client_name`, `client_phone`, `client_email`, `service_id`, `service_name`, `stylist_id`, `stylist_name`, `appointment_date`, `appointment_time`, `chair_no`, `price`, `commission_amount`, `payment_mode`, `payment_status`, `status`, `created_at`) VALUES ('5', NULL, 'ELG-8905', 'TK-105', 'Shahmir Tariq', '+92 345 7719283', 'shahmir@gmail.com', NULL, 'Deep Clean Facial', NULL, 'Zafar', '2026-08-24', '05:00 PM', '2', '2500.00', '450.00', 'EasyPaisa', 'Paid', 'Booked', '2026-08-24 19:30:34');
INSERT INTO `appointments` (`id`, `user_id`, `booking_ref`, `token`, `client_name`, `client_phone`, `client_email`, `service_id`, `service_name`, `stylist_id`, `stylist_name`, `appointment_date`, `appointment_time`, `chair_no`, `price`, `commission_amount`, `payment_mode`, `payment_status`, `status`, `created_at`) VALUES ('6', NULL, 'ELG-8906', 'TK-106', 'Usman Siddiqui', '+92 301 2293847', 'usman.sid@gmail.com', NULL, 'Keratin Smoothing Therapy', NULL, 'Aliyan', '2026-08-23', '04:00 PM', '3', '7500.00', '1350.00', 'Card / POS', 'Paid', 'Completed', '2026-08-24 19:30:34');
INSERT INTO `appointments` (`id`, `user_id`, `booking_ref`, `token`, `client_name`, `client_phone`, `client_email`, `service_id`, `service_name`, `stylist_id`, `stylist_name`, `appointment_date`, `appointment_time`, `chair_no`, `price`, `commission_amount`, `payment_mode`, `payment_status`, `status`, `created_at`) VALUES ('7', NULL, 'ELG-8907', 'TK-107', 'Zubair Mallick', '+92 322 6619283', 'zubair@outlook.com', NULL, 'Royal Beard Sculpt & Razor Lining', NULL, 'Asif', '2026-08-23', '06:30 PM', '1', '1800.00', '324.00', 'Cash', 'Paid', 'Completed', '2026-08-24 19:30:34');
INSERT INTO `appointments` (`id`, `user_id`, `booking_ref`, `token`, `client_name`, `client_phone`, `client_email`, `service_id`, `service_name`, `stylist_id`, `stylist_name`, `appointment_date`, `appointment_time`, `chair_no`, `price`, `commission_amount`, `payment_mode`, `payment_status`, `status`, `created_at`) VALUES ('8', NULL, 'ELG-8908', 'TK-108', 'Taha Javed', '+92 334 5519280', 'taha.j@gmail.com', NULL, 'Executive Haircut & Styling', NULL, 'Zafar', '2026-08-22', '01:00 PM', '2', '2500.00', '450.00', 'Card / POS', 'Paid', 'Completed', '2026-08-24 19:30:34');
INSERT INTO `appointments` (`id`, `user_id`, `booking_ref`, `token`, `client_name`, `client_phone`, `client_email`, `service_id`, `service_name`, `stylist_id`, `stylist_name`, `appointment_date`, `appointment_time`, `chair_no`, `price`, `commission_amount`, `payment_mode`, `payment_status`, `status`, `created_at`) VALUES ('9', NULL, 'ELG-8442', '75', 'Saad Test Client', '03112345678', 'saadtest@gmail.com', NULL, 'Total Grooming', NULL, 'Zachary Nickell', '2026-08-27', '04:30 PM', '3', '5000.00', '900.00', 'Cash at Salon', 'Paid', 'Completed', '2026-08-27 18:41:08');
INSERT INTO `appointments` (`id`, `user_id`, `booking_ref`, `token`, `client_name`, `client_phone`, `client_email`, `service_id`, `service_name`, `stylist_id`, `stylist_name`, `appointment_date`, `appointment_time`, `chair_no`, `price`, `commission_amount`, `payment_mode`, `payment_status`, `status`, `created_at`) VALUES ('10', NULL, 'ELG-1752', 'TK-99', 'Test Walkin User', '03009998888', 'walkin@test.com', NULL, 'Total Grooming', NULL, 'Zachary Nickell', '2026-08-27', '03:00 PM', '1', '5000.00', '900.00', 'Cash', 'Paid', 'Completed', '2026-08-27 19:28:46');
INSERT INTO `appointments` (`id`, `user_id`, `booking_ref`, `token`, `client_name`, `client_phone`, `client_email`, `service_id`, `service_name`, `stylist_id`, `stylist_name`, `appointment_date`, `appointment_time`, `chair_no`, `price`, `commission_amount`, `payment_mode`, `payment_status`, `status`, `created_at`) VALUES ('11', NULL, 'ELG-3874', 'TK-63', 'alibhaijan', '03974374974', 'bhai@gmail.com', NULL, 'Signature Cut & Beard', NULL, 'Asif', '2026-08-27', '04:53 PM', '4', '3500.00', '630.00', 'EasyPaisa', 'Unpaid', 'Cancelled', '2026-08-27 19:53:43');
INSERT INTO `appointments` (`id`, `user_id`, `booking_ref`, `token`, `client_name`, `client_phone`, `client_email`, `service_id`, `service_name`, `stylist_id`, `stylist_name`, `appointment_date`, `appointment_time`, `chair_no`, `price`, `commission_amount`, `payment_mode`, `payment_status`, `status`, `created_at`) VALUES ('12', NULL, 'ELG-3605', 'TK-501', 'Muhammad Saad', '03974374974', 'sqofficial101@gmail.com', NULL, 'Deep Clean Facial', NULL, 'Aliyan', '2026-08-27', '10:00 AM', '1', '2500.00', '450.00', 'Cash', 'Paid', 'Completed', '2026-08-27 22:25:24');

-- --------------------------------------------------------
-- Table structure for `reviews`
-- --------------------------------------------------------

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_name` varchar(100) NOT NULL,
  `client_email` varchar(100) DEFAULT NULL,
  `service_name` varchar(100) DEFAULT NULL,
  `stylist_name` varchar(100) DEFAULT NULL,
  `rating` int(11) DEFAULT 5,
  `comment` text NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Approved',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `reviews`
INSERT INTO `reviews` (`id`, `client_name`, `client_email`, `service_name`, `stylist_name`, `rating`, `comment`, `status`, `created_at`) VALUES ('1', 'Daniyal Khan', 'daniyal@gmail.com', 'Executive Haircut & Styling', 'Asif', '5', 'Best skin fade in Karachi! Asif is truly a master of his craft. Exceptional ambiance.', 'Approved', '2026-08-24 19:30:34');
INSERT INTO `reviews` (`id`, `client_name`, `client_email`, `service_name`, `stylist_name`, `rating`, `comment`, `status`, `created_at`) VALUES ('2', 'Hamza Sheikh', 'hamza.s@hotmail.com', 'Royal Beard Sculpt & Razor Lining', 'Zafar', '5', 'Impeccable attention to detail. Hot towel finish was ultra-relaxing.', 'Approved', '2026-08-24 19:30:34');
INSERT INTO `reviews` (`id`, `client_name`, `client_email`, `service_name`, `stylist_name`, `rating`, `comment`, `status`, `created_at`) VALUES ('3', 'Bilal Ahmed', 'bilal.ahmed@yahoo.com', 'Total Grooming Package', 'Aliyan', '5', 'Top notch luxury grooming experience. Worth every single rupee!', 'Approved', '2026-08-24 19:30:34');
INSERT INTO `reviews` (`id`, `client_name`, `client_email`, `service_name`, `stylist_name`, `rating`, `comment`, `status`, `created_at`) VALUES ('4', 'dg', 'dg@gmail.com', 'Total Grooming', 'Asif', '5', 'sasasasasasa', 'Approved', '2026-08-28 09:41:10');

-- --------------------------------------------------------
-- Table structure for `contact_messages`
-- --------------------------------------------------------

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subject` varchar(150) DEFAULT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `contact_messages`
INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `created_at`) VALUES ('1', 'Adeel Siddiqui', 'adeel@gmail.com', '+92 300 4410928', 'Groom VIP Package Inquiry', 'Hello, do you offer exclusive bridal / groom packages for weddings? Please share pricing.', '2026-08-24 19:30:34');
INSERT INTO `contact_messages` (`id`, `name`, `email`, `phone`, `subject`, `message`, `created_at`) VALUES ('2', 'Moiz Baig', 'moiz@gmail.com', '+92 321 5519283', 'Corporate Grooming Event', 'We want to book grooming sessions for our executive corporate team next Friday. Please contact.', '2026-08-24 19:30:34');

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
