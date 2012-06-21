DROP TABLE IF EXISTS `issue`;

CREATE TABLE IF NOT EXISTS `issue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(128) DEFAULT NULL,
  `number` varchar(64) DEFAULT NULL,
  `release_date` datetime DEFAULT NULL,
  `state` int(4) NOT NULL,
  `application` int(10) NOT NULL,
  `user` int(10) NOT NULL,
  `type` enum('simple', 'enriched') DEFAULT 'simple',
  `orientation` enum('horizontal', 'vertical') DEFAULT 'vertical',
  `static_pdf_mode` enum('issue','page','2pages','none') DEFAULT 'none',
  `product_id` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `updated_static_pdf` datetime DEFAULT NULL,
  `deleted` enum('yes','no') NOT NULL DEFAULT 'no',
  `issue_color` varchar(6) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_id` (`product_id`),
  KEY `number` (`number`),
  KEY `state` (`state`),
  KEY `application` (`application`),
  KEY `user` (`user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;