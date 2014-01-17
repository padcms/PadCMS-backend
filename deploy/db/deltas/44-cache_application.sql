CREATE TABLE IF NOT EXISTS `cache` (
  `application_id` int(11) NOT NULL,
  `data_ios` text NOT NULL,
  `data_android` text,
  `data_publisher` text,
  UNIQUE KEY `application_id` (`application_id`)
) ENGINE=InnoDB;
