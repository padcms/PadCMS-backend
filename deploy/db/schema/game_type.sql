DROP TABLE IF EXISTS `game_type`;

CREATE TABLE IF NOT EXISTS `game_type` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `title` varchar(256) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;