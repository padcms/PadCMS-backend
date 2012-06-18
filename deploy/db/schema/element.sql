DROP TABLE IF EXISTS `element`;
CREATE TABLE IF NOT EXISTS `element` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `page` int(12) NOT NULL,
  `field` int(8) NOT NULL,
  `weight` int(4) NOT NULL DEFAULT '0',
  `extra_data` text,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `page` (`page`),
  KEY `field` (`field`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;