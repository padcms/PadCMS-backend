DROP TABLE IF EXISTS `application`;
CREATE TABLE IF NOT EXISTS `application` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `preview` int(4) NOT NULL DEFAULT '0',
  `version` varchar(16) NOT NULL DEFAULT '1',
  `product_id` varchar(255) DEFAULT NULL,
  `description` text,
  `client` int(8) NOT NULL,
  `frequency` int(4) DEFAULT NULL,
  `nm_twitter_ios` text,
  `nm_twitter_android` text,
  `nm_fbook_ios` text,
  `nm_fbook_android` text,
  `nt_email_ios` varchar(100) DEFAULT NULL,
  `nt_email_android` varchar(100) DEFAULT NULL,
  `nm_email_ios` text,
  `nm_email_android` text,
  `deleted` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;