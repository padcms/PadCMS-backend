DROP TABLE IF EXISTS `template`;

CREATE TABLE IF NOT EXISTS `template` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `description` text,
  `weight` int(4) NOT NULL DEFAULT 0,
  `has_right_connector` tinyint(1) NOT NULL DEFAULT '1',
  `has_left_connector` tinyint(1) NOT NULL DEFAULT '1',
  `has_top_connector` tinyint(1) NOT NULL DEFAULT '1',
  `has_bottom_connector` tinyint(1) NOT NULL DEFAULT '1',
  `engine_version` int(6) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;