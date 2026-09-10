ALTER TABLE `quotations`
  ADD COLUMN `feature_text` text DEFAULT NULL AFTER `customer_state`;

ALTER TABLE `quotation_items`
  MODIFY COLUMN `unit` enum('Sqf','Sqi','Pcs') NOT NULL;