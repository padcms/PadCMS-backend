DROP TABLE IF EXISTS `revision`;

CREATE TABLE IF NOT EXISTS `revision` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `title` varchar(64) NOT NULL,
  `state` int(4) NOT NULL,
  `issue` int(10) NOT NULL,
  `user` int(10) NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`),
  KEY `state` (`state`),
  KEY `issue` (`issue`),
  KEY `user` (`user`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;