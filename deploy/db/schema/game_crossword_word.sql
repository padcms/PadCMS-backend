DROP TABLE IF EXISTS `game_crossword_word`;

CREATE TABLE IF NOT EXISTS `game_crossword_word` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `game` int(11) NOT NULL,
  `answer` varchar(255) NOT NULL DEFAULT '',
  `question` varchar(255) NOT NULL DEFAULT '',
  `length` smallint(2) DEFAULT NULL,
  `direction`  varchar(50) NOT NULL DEFAULT '',
  `start_from` smallint(5) NOT NULL,
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `game` (`game`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;