DROP TABLE IF EXISTS `issue_simple_pdf`;

CREATE TABLE IF NOT EXISTS `issue_simple_pdf` (
  `id_issue` int(12) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id_issue`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;