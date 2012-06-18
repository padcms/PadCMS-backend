DROP TABLE IF EXISTS `term_page`;

CREATE TABLE IF NOT EXISTS `term_page` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `term` int(12) NOT NULL,
  `page` int(12) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `term` (`term`),
  KEY `page` (`page`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;