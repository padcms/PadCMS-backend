DROP TABLE IF EXISTS `game`;

CREATE TABLE IF NOT EXISTS `game` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` int(12) NOT NULL,
  `type` int(11) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `created` datetime DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;