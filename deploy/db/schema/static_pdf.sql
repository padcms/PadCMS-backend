DROP TABLE IF EXISTS `static_pdf`;

CREATE TABLE IF NOT EXISTS `static_pdf` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `issue` int(12) NOT NULL,
  `weight` int(4) NOT NULL DEFAULT '0',
  `name` varchar(255) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `issue` (`issue`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;