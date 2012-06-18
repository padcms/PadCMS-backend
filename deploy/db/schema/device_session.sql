DROP TABLE IF EXISTS `device_session`;
CREATE TABLE IF NOT EXISTS `device_session` (
  `id` char(32),
  `modified` int,
  `lifetime` int,
  `data` text,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;