DROP TABLE IF EXISTS `statistic_view_type`;

CREATE TABLE IF NOT EXISTS `statistic_view_type` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `device_session` bigint(20) NOT NULL,
  `page` bigint(20) NOT NULL,
  `type` int(4) NOT NULL,
  `number` int(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `device_session` (`device_session`,`page`,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;