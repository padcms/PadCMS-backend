DROP TABLE IF EXISTS `task_type`;

CREATE TABLE IF NOT EXISTS `task_type` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `class` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `class`(`class`)
)
ENGINE=MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;