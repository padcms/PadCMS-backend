ALTER TABLE  `issue` ADD  `pricing_plan` TINYINT NULL DEFAULT NULL AFTER  `product_id`;
ALTER TABLE `issue` DROP `is_issue_individually_paid`;
ALTER TABLE  `issue` ADD  `google_play_id` VARCHAR( 255 ) NULL DEFAULT NULL AFTER  `pricing_plan`;
ALTER TABLE  `issue` ADD  `itunes_id` VARCHAR( 255 ) NULL DEFAULT NULL AFTER  `google_play_id`;
