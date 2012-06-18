DROP TABLE IF EXISTS `page`;
CREATE TABLE IF NOT EXISTS `page` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `template` int(6) NOT NULL,
  `revision` int(12) NOT NULL,
  `user` int(10) NOT NULL,
  `additional_data` varchar(255) DEFAULT NULL,
  `machine_name` varchar(255) DEFAULT NULL,
  `pdf_page` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` enum('yes','no') NOT NULL DEFAULT 'no',
  `connections` TINYINT NOT NULL DEFAULT 0,
  `toc` int(12) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `template` (`template`),
  KEY `revision` (`revision`),
  KEY `user` (`user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;