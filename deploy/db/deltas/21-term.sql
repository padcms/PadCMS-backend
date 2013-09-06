CREATE TABLE IF NOT EXISTS `term_entity` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `term` int(11) unsigned NOT NULL,
  `entity` int(11) unsigned NOT NULL,
  `entity_type` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `term_2` (`term`,`entity`,`entity_type`),
  KEY `term` (`term`),
  KEY `entity` (`entity`,`entity_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;