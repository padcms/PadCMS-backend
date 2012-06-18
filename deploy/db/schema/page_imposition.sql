DROP TABLE IF EXISTS `page_imposition`;

CREATE TABLE IF NOT EXISTS `page_imposition` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `page` int(12) NOT NULL,
  `is_linked_to` int(12) NOT NULL,
  `link_type` enum('left','right','top','bottom') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `page` (`page`),
  KEY `is_linked_to` (`is_linked_to`),
  KEY `link_type` (`link_type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;