DROP TABLE IF EXISTS `task`;

CREATE TABLE IF NOT EXISTS `task` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `task_type_id` bigint(20) unsigned NOT NULL,
  `options` text NOT NULL,
  `status` enum('new', 'run', 'finish', 'error') NOT NULL DEFAULT 'new',
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_status` (`id`, `status`)
)
ENGINE=MyISAM DEFAULT CHARACTER SET = utf8 COLLATE = utf8_general_ci;