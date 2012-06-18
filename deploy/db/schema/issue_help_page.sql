DROP TABLE IF EXISTS `issue_help_page`;

CREATE TABLE IF NOT EXISTS `issue_help_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_issue` int(12) NOT NULL,
  `type` enum('vertical', 'horizontal') NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_type_issue` (`id_issue`, `type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;