DROP TABLE IF EXISTS `purchase`;

CREATE TABLE IF NOT EXISTS `purchase` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` varchar(50) NOT NULL,
  `product_id` varchar(50) NOT NULL,
  `device_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `deleted` enum('yes','no') NOT NULL DEFAULT 'no',
  `purchase_date` datetime NOT NULL,
  `expires_date` datetime DEFAULT NULL,
  `subscription_type` enum('1year', '2year', 'archive', 'auto-renewable') DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `transaction_id_2` (`transaction_id`,`product_id`,`device_id`),
  KEY `transaction_id` (`transaction_id`),
  KEY `product_id` (`product_id`),
  KEY `udid` (`device_id`),
  KEY `created` (`created`),
  KEY `deleted` (`deleted`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;