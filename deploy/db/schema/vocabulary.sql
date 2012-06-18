DROP TABLE IF EXISTS `vocabulary`;

CREATE TABLE IF NOT EXISTS `vocabulary` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `has_hierarchy` tinyint(1) NOT NULL DEFAULT '0',
  `multiple` tinyint(1) NOT NULL DEFAULT '0',
  `application` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `has_hierarchy` (`has_hierarchy`),
  KEY `multiple` (`multiple`),
  KEY `application` (`application`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;