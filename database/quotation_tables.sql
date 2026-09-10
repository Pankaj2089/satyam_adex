CREATE TABLE `quotations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `quotation_no` varchar(100) NOT NULL,
  `quotation_date` date NOT NULL,
  `place_of_supply` varchar(150) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_address` text NOT NULL,
  `customer_contact` varchar(50) NOT NULL,
  `customer_gstin` varchar(50) DEFAULT NULL,
  `customer_state` varchar(100) DEFAULT NULL,
  `feature_text` text DEFAULT NULL,
  `total_quantity` decimal(14,4) NOT NULL DEFAULT '0.0000',
  `total_gst` decimal(14,2) NOT NULL DEFAULT '0.00',
  `total_amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `quotations_quotation_no_unique` (`quotation_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `quotation_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `quotation_id` int unsigned NOT NULL,
  `description` varchar(500) NOT NULL,
  `size` varchar(100) NOT NULL,
  `total_sqf` decimal(14,4) DEFAULT NULL,
  `quantity` decimal(14,4) NOT NULL,
  `unit` enum('Sqf','Sqi','Pcs') NOT NULL,
  `price` decimal(14,2) NOT NULL,
  `gst` decimal(14,2) NOT NULL DEFAULT '0.00',
  `amount` decimal(14,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `quotation_items_quotation_id_index` (`quotation_id`),
  CONSTRAINT `quotation_items_quotation_id_foreign` FOREIGN KEY (`quotation_id`) REFERENCES `quotations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;