DROP TABLE IF EXISTS `page_horisontal`;

CREATE TABLE IF NOT EXISTS `page_horisontal` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `id_issue` int(12) NOT NULL,
  `resource` VARCHAR(255) NOT NULL,
  `weight` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_issue` (`id_issue`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;