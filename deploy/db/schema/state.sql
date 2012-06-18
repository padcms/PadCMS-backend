DROP TABLE IF EXISTS `state`;

CREATE TABLE IF NOT EXISTS `state` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;