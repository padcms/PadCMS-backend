DROP TABLE IF EXISTS `page_background`;

CREATE TABLE IF NOT EXISTS `page_background` (
  `page` int(12) NOT NULL,
  `type` varchar(100) NOT NULL,
  `id` int(12) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`page`),
  KEY `type` (`type`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;