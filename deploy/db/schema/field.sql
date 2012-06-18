DROP TABLE IF EXISTS `field`;
CREATE TABLE IF NOT EXISTS `field` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `description` text,
  `field_type` int(4) NOT NULL,
  `min` int(4) NOT NULL DEFAULT '0',
  `max` int(4) NOT NULL DEFAULT '0',
  `max_width` int(8) NOT NULL DEFAULT '0',
  `max_height` int(8) NOT NULL DEFAULT '0',
  `weight` int(4) NOT NULL DEFAULT '0',
  `template` int(6) NOT NULL,
  `engine_version` int(6) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `field_type` (`field_type`),
  KEY `template` (`template`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;