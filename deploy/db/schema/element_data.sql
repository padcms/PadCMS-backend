DROP TABLE IF EXISTS `element_data`;
CREATE TABLE IF NOT EXISTS `element_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_element` int(11) NOT NULL,
  `key_name` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_element_key` (`id_element`, `key_name`),
  KEY `idx_key` (`key_name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;