DROP TABLE IF EXISTS `term`;

CREATE TABLE IF NOT EXISTS `term` (
  `id` int(12) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `color` char(6) DEFAULT NULL,
  `thumb_stripe` varchar(255) DEFAULT NULL,
  `thumb_summary` varchar(255) DEFAULT NULL,
  `vocabulary` int(12) NOT NULL,
  `parent_term` int(12) DEFAULT NULL,
  `revision` int(12) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`),
  KEY `vocabulary` (`vocabulary`),
  KEY `parent_term` (`parent_term`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;