DROP TABLE IF EXISTS `statistic_view_duration`;

CREATE TABLE IF NOT EXISTS `statistic_view_duration` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `device_session` bigint(20) NOT NULL,
  `page` bigint(20) NOT NULL,
  `duration` int(12) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `device_session` (`device_session`,`page`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;